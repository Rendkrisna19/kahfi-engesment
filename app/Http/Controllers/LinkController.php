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
                'file' => 'required|file|max:10240',
            ]);

            $targetCampaign = Campaign::find($request->campaign_id);
            $targetPlatform = $targetCampaign ? $targetCampaign->platform : null;

            $filePath = $request->file('file')->getRealPath();
            $fileContents = file_get_contents($filePath);

            $rawUrls = [];

            // Check if file is HTML table format (.xls created from template)
            if (str_contains($fileContents, '<tr') || str_contains($fileContents, '<td')) {
                // Parse HTML rows line by line to extract URLs from table cells only
                preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $fileContents, $trMatches);
                if (!empty($trMatches[1])) {
                    foreach ($trMatches[1] as $trContent) {
                        // Skip instruction and banner rows
                        if (str_contains($trContent, 'PETUNJUK PENGISIAN') || str_contains($trContent, 'th-header') || str_contains($trContent, 'title-banner')) {
                            continue;
                        }

                        preg_match_all('/<td[^>]*>(.*?)<\/td>/is', $trContent, $tdMatches);
                        if (!empty($tdMatches[1])) {
                            // Extract URL from column A or any cell in this row
                            foreach ($tdMatches[1] as $cellValue) {
                                $cleanCell = trim(strip_tags($cellValue));
                                if (preg_match('/https?:\/\/[^\s"<>\'\,\;\r\n]+/i', $cleanCell, $urlMatch)) {
                                    $rawUrls[] = $urlMatch[0];
                                    break; // Only take 1 URL per table row
                                }
                            }
                        }
                    }
                }
            }

            // Fallback for CSV / Plain Text if HTML parsing didn't find URLs
            if (empty($rawUrls)) {
                preg_match_all('/https?:\/\/[^\s"<>\'\,\;\r\n]+/i', $fileContents, $matches);
                if (!empty($matches[0])) {
                    $rawUrls = $matches[0];
                }
            }

            $extractedUrls = [];
            foreach ($rawUrls as $rawUrl) {
                $cleanUrl = strtok(trim($rawUrl), '?');
                if (filter_var($cleanUrl, FILTER_VALIDATE_URL)) {
                    $extractedUrls[] = $cleanUrl;
                }
            }

            $extractedUrls = array_unique($extractedUrls);

            foreach ($extractedUrls as $url) {
                $detectedPlatform = $this->detectPlatform($url);

                // Platform Validation & Filtering based on Selected Target Campaign
                if ($targetPlatform && in_array($targetPlatform, ['TikTok', 'Instagram'])) {
                    // Skip Instagram links when TikTok campaign is selected, and vice versa
                    if ($targetPlatform === 'TikTok' && $detectedPlatform === 'Instagram') {
                        continue;
                    }
                    if ($targetPlatform === 'Instagram' && $detectedPlatform === 'TikTok') {
                        continue;
                    }
                    $finalPlatform = $targetPlatform;
                } else {
                    $finalPlatform = $detectedPlatform;
                }

                Link::create([
                    'campaign_id' => $request->campaign_id,
                    'kategori_konten_id' => $request->kategori_konten_id,
                    'kategori_creator_id' => $request->kategori_creator_id,
                    'url' => $url,
                    'platform' => $finalPlatform,
                    'tanggal_upload' => now()->toDateString(),
                    'status_scraping' => 'Pending'
                ]);
                $insertedCount++;
            }
        }

        return redirect()->route('operasional-konten.index')
            ->with('success', "Berhasil menyimpan {$insertedCount} link konten ke dalam status Pending.");
    }

    /**
     * Download template format Excel / CSV untuk upload bulk
     */
    public function downloadTemplate(Request $request)
    {
        $format = $request->query('format', 'excel');

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="template_upload_link.csv"',
            ];

            $callback = function () {
                $file = fopen('php://output', 'w');
                // UTF-8 BOM for Excel CSV compatibility
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, ['URL Konten', 'Platform', 'Keterangan']);
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Default: Excel (.xls) template with styled table borders and gridlines
        $filename = "template_upload_link.xls";

        $html = '<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Template Upload Link</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; color: #1f2937; }
        .title-banner { font-size: 14pt; font-weight: bold; color: #ffffff; background-color: #1e3a8a; text-align: center; height: 35px; vertical-align: middle; border: 2px solid #1e3a8a; }
        .instruction-banner { font-size: 9.5pt; color: #1e40af; background-color: #eff6ff; padding: 10px; border: 1px solid #93c5fd; text-align: left; vertical-align: middle; }
        .th-header { font-size: 11pt; font-weight: bold; color: #ffffff; background-color: #1e40af; text-align: center; vertical-align: middle; height: 30px; border: 1px solid #111827; }
        .td-no { text-align: center; vertical-align: middle; border: 1px solid #4b5563; background-color: #f3f4f6; font-weight: bold; color: #374151; }
        .td-empty { text-align: left; vertical-align: middle; border: 1px solid #9ca3af; background-color: #ffffff; height: 24px; }
    </style>
</head>
<body>
    <table border="1" style="border-collapse: collapse; width: 100%;">
        <tr>
            <td colspan="4" class="title-banner">TEMPLATE UPLOAD LINK KONTEN - KAHFI ENGAGEMENT</td>
        </tr>
        <tr>
            <td colspan="4" class="instruction-banner">
                <b>PETUNJUK PENGISIAN:</b><br/>
                1. Masukkan URL video TikTok atau postingan Instagram pada kolom <b>URL Konten (Kolom A)</b>.<br/>
                2. Pastikan URL lengkap diawali dengan <b>http://</b> atau <b>https://</b>.<br/>
                3. Kolom <b>Platform</b> dan <b>Keterangan</b> bersifat opsional (sistem akan otomatis mendeteksi platform).<br/>
                4. <b>CONTOH FORMAT URL:</b><br/>
                   - TikTok: <i>https://www.tiktok.com/@creator/video/1234567890</i><br/>
                   - Instagram: <i>https://www.instagram.com/p/Cxyz123456/</i>
            </td>
        </tr>
        <tr><td colspan="4" style="height: 10px; border: none; background-color: #ffffff;"></td></tr>
        <tr>
            <th style="width: 45px;" class="th-header">No</th>
            <th style="width: 450px;" class="th-header">URL Konten (TikTok / Instagram) *Wajib</th>
            <th style="width: 120px;" class="th-header">Platform</th>
            <th style="width: 200px;" class="th-header">Keterangan</th>
        </tr>';

        for ($i = 1; $i <= 20; $i++) {
            $html .= '
        <tr>
            <td class="td-no">' . $i . '</td>
            <td class="td-empty"></td>
            <td class="td-empty"></td>
            <td class="td-empty"></td>
        </tr>';
        }

        $html .= '
    </table>
</body>
</html>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
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
     * Update metrik engagement link secara manual (Views, Likes, Comments, Shares, Saves)
     */
    public function update(Request $request, $id)
    {
        $link = Link::findOrFail($id);

        // Security check: status Gagal cannot be edited
        if ($link->status_scraping === 'Gagal') {
            return redirect()->back()->with('error', 'Link dengan status Gagal tidak dapat diedit secara manual.');
        }

        // Check user access to this campaign
        $user = auth()->user();
        if ($user->hasRole('Admin')) {
            $allowedIds = UserCampaignAccess::where('user_id', $user->id)->pluck('campaign_id')->toArray();
            if (!in_array($link->campaign_id, $allowedIds)) {
                return redirect()->back()->with('error', 'Maaf, Anda tidak memiliki akses ke campaign ini.');
            }
        }

        $validated = $request->validate([
            'username' => 'nullable|string|max:255',
            'tanggal_upload' => 'nullable|date',
            'views' => 'required|numeric|min:0',
            'likes' => 'required|numeric|min:0',
            'comments' => 'required|numeric|min:0',
            'shares' => 'required|numeric|min:0',
            'saves' => 'required|numeric|min:0',
        ]);

        $views = (int) $validated['views'];
        $likes = (int) $validated['likes'];
        $comments = (int) $validated['comments'];
        $shares = (int) $validated['shares'];
        $saves = (int) $validated['saves'];

        // Calculate Engagement Rate: (Likes + Comments + Shares) / Views * 100
        $er = 0;
        if ($views > 0) {
            $er = (($likes + $comments + $shares) / $views) * 100;
        }

        // If status was Pending, update status to Completed because metrics are now entered manually
        $statusScraping = in_array($link->status_scraping, ['Pending']) ? 'Completed' : $link->status_scraping;

        $link->update([
            'username' => $validated['username'] ?? $link->username,
            'tanggal_upload' => $validated['tanggal_upload'] ?? $link->tanggal_upload,
            'views' => $views,
            'likes' => $likes,
            'comments' => $comments,
            'shares' => $shares,
            'saves' => $saves,
            'engagement_rate' => min(100, round($er, 2)),
            'status_scraping' => $statusScraping,
            'updated_at' => now(),
        ]);

        // Recalculate SAW score for the campaign
        $this->calculateSawScoresForCampaign($link->campaign_id);

        return redirect()->route('operasional-konten.index')
            ->with('success', "Berhasil memperbarui metrik engagement untuk link secara manual!");
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

    /**
     * Cek Saldo & Limit Kuota Apify Account
     */
    public function getApifyStatus()
    {
        $token = env('APIFY_TOKEN', env('APIFY_API_TOKEN'));
        if (empty($token)) {
            return response()->json([
                'configured' => false,
                'message' => 'Token Apify belum dikonfigurasi di .env'
            ]);
        }

        try {
            $response = Http::timeout(8)
                ->withToken($token)
                ->get('https://api.apify.com/v2/users/me');

            if ($response->successful()) {
                $userData = $response->json('data', []);

                $username = $userData['username'] ?? ($userData['email'] ?? 'User Apify');
                $planName = $userData['plan']['name'] ?? ($userData['plan']['id'] ?? 'Free');

                $maxUsageUsd = (float)($userData['plan']['maxMonthlyUsageUsd'] 
                    ?? ($userData['limits']['monthlyUsageUsd'] ?? 5.00));
                    
                $currentUsageUsd = (float)($userData['monthlyUsage']['totalUsageUsd'] 
                    ?? ($userData['stats']['usageUsd'] ?? 0.00));

                if (!isset($userData['monthlyUsage']['totalUsageUsd'])) {
                    $usageRes = Http::timeout(5)->withToken($token)->get('https://api.apify.com/v2/users/me/usage/monthly');
                    if ($usageRes->successful()) {
                        $currentUsageUsd = (float)$usageRes->json('data.totalUsageUsd', $currentUsageUsd);
                    }
                }

                $remainingUsd = max(0, $maxUsageUsd - $currentUsageUsd);
                $percentageUsed = $maxUsageUsd > 0 ? min(100, round(($currentUsageUsd / $maxUsageUsd) * 100, 1)) : 0;

                return response()->json([
                    'configured' => true,
                    'username' => $username,
                    'plan_name' => $planName,
                    'usage_usd' => number_format($currentUsageUsd, 2),
                    'limit_usd' => number_format($maxUsageUsd, 2),
                    'remaining_usd' => number_format($remainingUsd, 2),
                    'percentage_used' => $percentageUsed
                ]);
            }

            return response()->json([
                'configured' => false,
                'message' => 'Gagal mengontak Apify API (Status: ' . $response->status() . ')'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'configured' => false,
                'message' => 'Koneksi Apify error: ' . $e->getMessage()
            ]);
        }
    }
}