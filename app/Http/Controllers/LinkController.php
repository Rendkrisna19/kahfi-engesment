<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\KategoriCreator;
use App\Models\KategoriKonten;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCampaignAccess;
use Exception;

class LinkController extends Controller
{

    
    /**
     * Menampilkan daftar link konten dan form input operasional
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $kategoriKonten = KategoriKonten::orderBy('nama', 'asc')->get();
        $kategoriCreator = KategoriCreator::orderBy('nama', 'asc')->get();

        if ($user->hasRole('Admin Master') || $user->role === 'Admin Master') {
            $campaigns = Campaign::orderBy('nama_campaign', 'asc')->get();
        } elseif ($user->hasRole('Admin') || $user->role === 'Admin') {
            $accessIds = UserCampaignAccess::where('user_id', $user->id)->pluck('campaign_id');
            $campaigns = Campaign::whereIn('id', $accessIds)->orderBy('nama_campaign', 'asc')->get();
        } elseif ($user->hasRole('Client') || $user->role === 'Client') {
            $campaigns = Campaign::where('client_id', $user->id)->orderBy('nama_campaign', 'asc')->get();
        } else {
            $campaigns = collect();
        }

        $campaignIds = $campaigns->pluck('id');

        $query = Link::with(['campaign', 'kategoriKonten', 'kategoriCreator']);

        // Scope data link sesuai campaign yang bisa diakses user
        if (!($user->hasRole('Admin Master') || $user->role === 'Admin Master')) {
            $query->whereIn('campaign_id', $campaignIds);
        }

        $query->orderBy('id', 'desc');

        // Filter Campaign jika dipilih
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        }

        // Filter Platform jika dipilih
        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        // Filter Search / Pencarian jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('url', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('caption', 'like', "%{$search}%");
            });
        }

        // Filter Tanggal
        if ($request->filled('start_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('tanggal_upload', '>=', $request->start_date)
                  ->orWhere(function ($sub) use ($request) {
                      $sub->whereNull('tanggal_upload')
                          ->whereDate('updated_at', '>=', $request->start_date);
                  });
            });
        }

        if ($request->filled('end_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('tanggal_upload', '<=', $request->end_date)
                  ->orWhere(function ($sub) use ($request) {
                      $sub->whereNull('tanggal_upload')
                          ->whereDate('updated_at', '<=', $request->end_date);
                  });
            });
        }

        $links = $query->paginate(15)->withQueryString();

        $pendingCountQuery = Link::where('status_scraping', 'Pending');
        if (!($user->hasRole('Admin Master') || $user->role === 'Admin Master')) {
            $pendingCountQuery->whereIn('campaign_id', $campaignIds);
        }
        $pendingCount = $pendingCountQuery->count();

        return view('operasional-konten.index', compact('campaigns', 'kategoriKonten', 'kategoriCreator', 'links', 'pendingCount'));
    }

    /**
     * Menyimpan data link konten (Single, Bulk, CSV)
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validasi akses ke campaign
        if (!($user->hasRole('Admin Master') || $user->role === 'Admin Master')) {
            if ($user->hasRole('Admin') || $user->role === 'Admin') {
                $allowedIds = UserCampaignAccess::where('user_id', $user->id)->pluck('campaign_id')->toArray();
            } elseif ($user->hasRole('Client') || $user->role === 'Client') {
                $allowedIds = Campaign::where('client_id', $user->id)->pluck('id')->toArray();
            } else {
                $allowedIds = [];
            }

            if (!in_array($request->campaign_id, $allowedIds)) {
                return redirect()->back()->with('error', 'Maaf, kamu masih belum ditugaskan campaign ini.');
            }
        }

        $request->validate([
            'type' => 'required|in:single,bulk,csv',
            'campaign_id' => 'required|exists:campaigns,id',
            'kategori_konten_id' => 'required|exists:kategori_konten,id',
            'kategori_creator_id' => 'required|exists:kategori_creator,id',
        ]);

        $insertedCount = 0;

        if ($request->type === 'single') {
            $request->validate([
                'url' => 'required|url',
            ]);

            $url = strtok(trim($request->url), '?');
            $platform = $this->detectPlatform($url);

            Link::create([
                'campaign_id' => $request->campaign_id,
                'kategori_konten_id' => $request->kategori_konten_id,
                'kategori_creator_id' => $request->kategori_creator_id,
                'url' => $url,
                'platform' => $platform,
                'tanggal_upload' => now()->toDateString(),
                'status_scraping' => 'Pending'
            ]);

            $insertedCount = 1;
        } elseif ($request->type === 'bulk') {
            $request->validate([
                'urls' => 'required|string',
            ]);

            $urls = explode("\n", str_replace("\r", "", $request->urls));

            foreach ($urls as $rawUrl) {
                $trimmed = trim($rawUrl);
                if (filter_var($trimmed, FILTER_VALIDATE_URL)) {
                    $url = strtok($trimmed, '?');
                    $platform = $this->detectPlatform($url);

                    Link::create([
                        'campaign_id' => $request->campaign_id,
                        'kategori_konten_id' => $request->kategori_konten_id,
                        'kategori_creator_id' => $request->kategori_creator_id,
                        'url' => $url,
                        'platform' => $platform,
                        'tanggal_upload' => now()->toDateString(),
                        'status_scraping' => 'Pending'
                    ]);
                    $insertedCount++;
                }
            }
        } elseif ($request->type === 'csv') {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:5120',
            ]);

            if (($handle = fopen($request->file('file')->getRealPath(), 'r')) !== false) {
                fgetcsv($handle, 1000, ','); // Skip baris header

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    if (isset($data[0]) && filter_var(trim($data[0]), FILTER_VALIDATE_URL)) {
                        $url = strtok(trim($data[0]), '?');
                        $platform = $this->detectPlatform($url);

                        Link::create([
                            'campaign_id' => $request->campaign_id,
                            'kategori_konten_id' => $request->kategori_konten_id,
                            'kategori_creator_id' => $request->kategori_creator_id,
                            'url' => $url,
                            'platform' => $platform,
                            'tanggal_upload' => now()->toDateString(),
                            'status_scraping' => 'Pending'
                        ]);
                        $insertedCount++;
                    }
                }
                fclose($handle);
            }
        }

        return redirect()->route('operasional-konten.index')
            ->with('success', "Berhasil menyimpan {$insertedCount} link konten ke dalam status Pending.");
    }

    /**
     * Download template format CSV untuk upload bulk
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_upload_link.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['URL Konten']);
            fputcsv($file, ['https://www.tiktok.com/@creator/video/1234567890']);
            fputcsv($file, ['https://www.instagram.com/p/Cxyz123456/']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Menampilkan detail data link & metrik scraping
     */
    public function show($id)
    {
        $link = Link::with(['campaign', 'kategoriKonten', 'kategoriCreator'])->findOrFail($id);
        if (request()->has('debug')) {
            dd('Masuk ke halaman show!', $link->toArray());
        }
        return view('operasional-konten.show', compact('link'));
    }

    /**
     * Menghapus record link
     */
    public function destroy($id)
    {
        $link = Link::findOrFail($id);
        $campaignId = $link->campaign_id;
        $link->delete();

        // Hitung ulang SAW untuk campaign tersebut setelah data dihapus
        $this->calculateSawScoresForCampaign($campaignId);

        return redirect()->back()->with('success', 'Link konten berhasil dihapus.');
    }

    /**
     * Menghapus multiple record link (Bulk Delete)
     */
    public function destroyBulk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:links,id'
        ]);

        $links = Link::whereIn('id', $request->ids)->get();
        $count = $links->count();

        if ($count === 0) {
            return redirect()->back()->with('error', 'Pilih minimal satu link untuk dihapus.');
        }

        $campaignIds = $links->pluck('campaign_id')->unique();
        Link::whereIn('id', $request->ids)->delete();

        foreach ($campaignIds as $cId) {
            $this->calculateSawScoresForCampaign($cId);
        }

        return redirect()->back()->with('success', "Berhasil menghapus {$count} link konten.");
    }

    /**
     * Menjalankan Scraping Apify API untuk Link berstatus Pending dan Kalkulasi SAW
     */
    public function refreshData(Request $request)
    {
        $query = Link::where('status_scraping', 'Pending');

        // Jika refresh dipicu untuk campaign tertentu
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        }

        $pendingLinks = $query->get();

        if ($pendingLinks->isEmpty()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'processed_count' => 0,
                    'message' => 'Tidak ada link dengan status Pending.',
                    'remaining_pending' => 0
                ]);
            }
            return redirect()->back()->with('info', 'Tidak ada link dengan status Pending yang perlu diproses.');
        }

        $token = env('APIFY_TOKEN', env('APIFY_API_TOKEN'));
        $rawTiktokActor = env('APIFY_TIKTOK_ACTOR', env('APIFY_TIKTOK_ACTOR_ID', 'clockworks/free-tiktok-scraper'));
        $rawIgActor = env('APIFY_IG_ACTOR', env('APIFY_INSTAGRAM_ACTOR_ID', 'apify/instagram-scraper'));

        $tiktokActorId = str_replace('/', '~', $rawTiktokActor);
        $igActorId = str_replace('/', '~', $rawIgActor);

        if (empty($token)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'API Token Apify belum dikonfigurasi di file .env.'
                ], 400);
            }
            return redirect()->back()->with('error', 'API Token Apify belum dikonfigurasi di file .env.');
        }

        $successCount = 0;
        $errors = [];
        $affectedCampaigns = [];

        foreach ($pendingLinks as $link) {
            try {
                $cleanUrl = strtok($link->url, '?');
                $isTikTok = strtolower($link->platform) === 'tiktok';

                if ($isTikTok) {
                    $endpoint = "https://api.apify.com/v2/acts/{$tiktokActorId}/run-sync-get-dataset-items?token={$token}";
                    $input = [
                        'postURLs' => [$cleanUrl],
                        'resultsPerPage' => 1,
                        'shouldDownloadVideos' => false
                    ];
                } else {
                    $endpoint = "https://api.apify.com/v2/acts/{$igActorId}/run-sync-get-dataset-items?token={$token}";
                    $input = [
                        'directUrls' => [$cleanUrl],
                        'resultsType' => 'posts',
                        'resultsLimit' => 1
                    ];
                }

                $response = Http::timeout(180)->post($endpoint, $input);

                if ($response->successful()) {
                    $data = $response->json();

                    if (!empty($data) && isset($data[0])) {
                        $item = $data[0];

                        $views = 0;
                        $likes = 0;
                        $comments = 0;
                        $shares = 0;
                        $saves = 0;
                        $reposts = 0;
                        $username = null;
                        $caption = null;
                        $postDate = null;
                        $postType = null;

                        if ($isTikTok) {
                            $views = $item['playCount'] ?? $item['viewsCount'] ?? 0;
                            $likes = $item['diggCount'] ?? $item['likesCount'] ?? 0;
                            $comments = $item['commentCount'] ?? $item['commentsCount'] ?? 0;
                            $shares = $item['shareCount'] ?? $item['sharesCount'] ?? 0;
                            $saves = $item['collectCount'] ?? $item['bookmarksCount'] ?? 0;
                            $reposts = $item['repostCount'] ?? 0;
                            $username = $item['authorMeta']['name'] ?? $item['authorMeta']['nickName'] ?? ($item['author']['uniqueId'] ?? 'TikTok User');
                            $caption = $item['text'] ?? null;
                            $postDate = isset($item['createTime']) ? date('Y-m-d H:i:s', is_numeric($item['createTime']) ? $item['createTime'] : strtotime($item['createTime'])) : null;
                            $postType = 'Video';
                        } else {
                            // Instagram Logic
                            $views = $item['playCount'] ?? $item['videoViewCount'] ?? $item['videoPlayCount'] ?? $item['viewCount'] ?? 0;
                            $likes = $item['likesCount'] ?? $item['likeCount'] ?? 0;
                            $comments = $item['commentsCount'] ?? $item['commentCount'] ?? 0;
                            $shares = $item['sharesCount'] ?? 0;
                            $saves = $item['savesCount'] ?? 0;
                            
                            $postType = $item['type'] ?? $item['product_type'] ?? null;
                            $caption = $item['caption'] ?? $item['text'] ?? null;
                            
                            if (isset($item['timestamp'])) {
                                $postDate = date('Y-m-d H:i:s', strtotime($item['timestamp']));
                            } elseif (isset($item['created_at'])) {
                                $postDate = date('Y-m-d H:i:s', is_numeric($item['created_at']) ? $item['created_at'] : strtotime($item['created_at']));
                            }

                            $username = $item['ownerUsername'] ?? $item['ownerFullName'] ?? 'IG User';
                        }

                        // Hitung Engagement Rate: (Likes + Comments + Shares) / Views * 100
                        $er = 0;
                        if ($views > 0) {
                            $er = (($likes + $comments + $shares) / $views) * 100;
                        }

                        // Calculate SAW Score (dummy logic)
                        $sawScore = ($er * 0.5) + (($likes > 100 ? 10 : ($likes / 10)) * 0.3) + (($comments > 50 ? 10 : ($comments / 5)) * 0.2);

                        $link->update([
                            'status_scraping' => 'Completed',
                            'views' => $views,
                            'likes' => $likes,
                            'comments' => $comments,
                            'shares' => $shares,
                            'saves' => $saves,
                            'reposts' => $reposts,
                            'engagement_rate' => min(100, $er),
                            'saw_score' => min(10, $sawScore),
                            'username' => $username,
                            'post_type' => $postType,
                            'caption' => $caption,
                            'post_date' => $postDate,
                            'tanggal_upload' => $postDate ? date('Y-m-d', strtotime($postDate)) : date('Y-m-d'),
                            'updated_at' => now(),
                        ]);

                        $affectedCampaigns[$link->campaign_id] = true;
                        $successCount++;
                    } else {
                        $link->update(['status_scraping' => 'Gagal']);
                        $errors[] = "Data kosong untuk link: {$link->url}";
                    }
                } else {
                    $link->update(['status_scraping' => 'Gagal']);
                    $errors[] = "Gagal memproses {$link->url} (Status: {$response->status()})";
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $link->update(['status_scraping' => 'Gagal']);
                $errors[] = "Timeout saat memproses link: {$link->url}";
            } catch (Exception $e) {
                $link->update(['status_scraping' => 'Gagal']);
                $errors[] = "Error sistem pada {$link->url}: {$e->getMessage()}";
            }
        }

        // Kalkulasi Skor SAW untuk setiap campaign yang datanya baru terupdate
        foreach (array_keys($affectedCampaigns) as $campaignId) {
            $this->calculateSawScoresForCampaign($campaignId);
        }

        $remainingPending = Link::where('status_scraping', 'Pending')->count();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => $successCount > 0,
                'processed_count' => $successCount,
                'errors' => $errors,
                'remaining_pending' => $remainingPending,
                'message' => "Memproses {$successCount} link."
            ]);
        }

        if (count($errors) > 0 && $successCount === 0) {
            return redirect()->back()->with('error', 'Semua link gagal diproses: ' . implode(' | ', $errors));
        }

        if (count($errors) > 0 && $successCount > 0) {
            return redirect()->back()->with('warning', "Berhasil memproses {$successCount} link, namun beberapa link gagal: " . implode(' | ', $errors));
        }

        return redirect()->back()->with('success', "Sukses memproses {$successCount} link dan memperbarui ranking SAW!");
    }

    /**
     * Cek koneksi token Apify
     */
    public function testApifyConnection()
    {
        $token = env('APIFY_TOKEN', env('APIFY_API_TOKEN'));

        if (empty($token)) {
            return redirect()->back()->with('error', 'Token Apify belum diatur di file .env!');
        }

        try {
            $response = Http::timeout(10)->get("https://api.apify.com/v2/users/me?token={$token}");

            if ($response->successful()) {
                $user = $response->json()['data']['username'] ?? 'User Apify';
                return redirect()->back()->with('success', "Koneksi Apify Berhasil! Terhubung ke akun: {$user}");
            }

            return redirect()->back()->with('error', 'Koneksi Apify Gagal! Pastikan API Token valid.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Koneksi Apify Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Helper deteksi platform berdasarkan string URL
     */
    private function detectPlatform(string $url): string
    {
        $lower = strtolower($url);
        if (str_contains($lower, 'tiktok.com')) {
            return 'TikTok';
        } elseif (str_contains($lower, 'instagram.com')) {
            return 'Instagram';
        }
        return 'Unknown';
    }

    /**
     * Perhitungan Algoritma SAW (Simple Additive Weighting) per Campaign
     * Bobot: Share 35%, Comment 25%, Like 20%, View 10%, Save 10%
     */
    private function calculateSawScoresForCampaign(int $campaignId): void
    {
        $links = Link::where('campaign_id', $campaignId)
            ->whereIn('status_scraping', ['Completed', 'Berhasil'])
            ->get();

        if ($links->isEmpty()) {
            return;
        }

        // Ambil nilai maksimum kriteria benefit
        $maxShare = $links->max('shares') ?: 1;
        $maxComment = $links->max('comments') ?: 1;
        $maxLike = $links->max('likes') ?: 1;
        $maxView = $links->max('views') ?: 1;
        $maxSave = $links->max('saves') ?: 1;

        foreach ($links as $link) {
            // Normalisasi Matriks R
            $rShare = $link->shares / $maxShare;
            $rComment = $link->comments / $maxComment;
            $rLike = $link->likes / $maxLike;
            $rView = $link->views / $maxView;
            $rSave = $link->saves / $maxSave;

            // Hitung Preferensi SAW (V_i)
            $sawScore = (0.35 * $rShare) + 
                        (0.25 * $rComment) + 
                        (0.20 * $rLike) + 
                        (0.10 * $rView) + 
                        (0.10 * $rSave);

            $link->update([
                'saw_score' => round($sawScore, 4)
            ]);
        }
    }
}