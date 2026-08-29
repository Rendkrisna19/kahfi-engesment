<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class UpdateSawController extends Controller
{
    /**
     * Menampilkan daftar Campaign dalam bentuk Grid / Card untuk Pilihan Update SAW
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Campaign::withCount('links');

        // Scope filter berdasarkan Role User
        if ($user->hasRole('Client') || $user->role === 'Client') {
            $query->where('client_id', $user->id);
        } elseif (($user->hasRole('Admin') || $user->role === 'Admin') && !($user->hasRole('Admin Master') || $user->role === 'Admin Master')) {
            $accessIds = \App\Models\UserCampaignAccess::where('user_id', $user->id)->pluck('campaign_id');
            $query->whereIn('id', $accessIds);
        }

        if ($request->filled('search')) {
            $query->where('nama_campaign', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('platform')) {
            $platform = $request->platform;
            $query->where(function ($q) use ($platform) {
                $q->where('platform', $platform)
                  ->orWhereHas('links', function ($l) use ($platform) {
                      $l->where('platform', $platform);
                  });
            });
        }

        // Pagination 4 items per page (compact grid)
        $campaigns = $query->paginate(4)->withQueryString();

        // Hitung statistik ringkas untuk tiap campaign
        foreach ($campaigns as $campaign) {
            $links = Link::where('campaign_id', $campaign->id)->get();
            $campaign->total_views = $links->sum('views');
            $campaign->total_likes = $links->sum('likes');
            $campaign->avg_er = $links->avg('engagement_rate') ?? 0;
            $campaign->avg_saw_score = $links->avg('saw_score') ?? 0;
            $campaign->last_rescrape = $links->max('last_rescraped_at') ?? $links->max('updated_at');
            $campaign->pending_count = $links->where('status_scraping', 'Pending')->count();
            $campaign->completed_count = $links->whereIn('status_scraping', ['Completed', 'Berhasil'])->count();
        }

        return view('update-saw.index', compact('campaigns'));
    }

    /**
     * Menampilkan daftar link dari Campaign yang dipilih untuk seleksi Checkbox & Re-Scrape
     */
    public function show($id, Request $request)
    {
        $user = Auth::user();
        $campaign = Campaign::findOrFail($id);

        if (($user->hasRole('Client') || $user->role === 'Client') && $campaign->client_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke Campaign ini.');
        }

        if (($user->hasRole('Admin') || $user->role === 'Admin') && !($user->hasRole('Admin Master') || $user->role === 'Admin Master')) {
            $hasAccess = \App\Models\UserCampaignAccess::where('user_id', $user->id)->where('campaign_id', $campaign->id)->exists();
            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki akses ke Campaign ini.');
            }
        }

        $query = Link::where('campaign_id', $campaign->id);

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('url', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('caption', 'like', "%{$search}%");
            });
        }

        $links = $query->orderBy('saw_score', 'desc')->paginate(20)->withQueryString();

        return view('update-saw.show', compact('campaign', 'links'));
    }

    /**
     * Memproses Re-Scraping untuk link terpilih (Checkbox) & memperbarui Skor SAW
     */
    public function process(Request $request, $id)
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '512M');

        $campaign = Campaign::findOrFail($id);

        $request->validate([
            'link_ids' => 'nullable|array',
            'link_ids.*' => 'exists:links,id',
            'select_all' => 'nullable|boolean',
        ]);

        if ($request->boolean('select_all')) {
            $linkIds = Link::where('campaign_id', $campaign->id)->pluck('id')->toArray();
        } else {
            $linkIds = $request->input('link_ids', []);
        }

        if (empty($linkIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu link untuk di-scrape ulang.');
        }

        $linksToProcess = Link::whereIn('id', $linkIds)->get();

        // 1. Pindahkan metrik saat ini ke prev_* sebelum scraping ulang
        foreach ($linksToProcess as $link) {
            $link->update([
                'prev_views' => $link->views,
                'prev_likes' => $link->likes,
                'prev_comments' => $link->comments,
                'prev_shares' => $link->shares,
                'prev_saves' => $link->saves,
                'prev_engagement_rate' => $link->engagement_rate,
                'prev_saw_score' => $link->saw_score,
                'status_scraping' => 'Pending',
            ]);
        }

        // 2. Jalankan Scraping Apify secara langsung
        $token = env('APIFY_TOKEN', env('APIFY_API_TOKEN'));
        $rawTiktokActor = env('APIFY_TIKTOK_ACTOR', env('APIFY_TIKTOK_ACTOR_ID', 'clockworks/free-tiktok-scraper'));
        $rawIgActor = env('APIFY_IG_ACTOR', env('APIFY_INSTAGRAM_ACTOR_ID', 'apify/instagram-scraper'));

        $tiktokActorId = str_replace('/', '~', $rawTiktokActor);
        $igActorId = str_replace('/', '~', $rawIgActor);

        $processedCount = 0;
        $failedCount = 0;

        if (!empty($token)) {
            foreach ($linksToProcess as $link) {
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

                    $response = Http::timeout(35)->post($endpoint, $input);

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

                            $er = ($views > 0) ? (($likes + $comments + $shares) / $views) * 100 : 0;
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
                                'last_rescraped_at' => now(),
                                'updated_at' => now(),
                            ]);

                            $processedCount++;
                        } else {
                            // Empty data response fallback
                            if (($link->views ?? 0) > 0 || ($link->likes ?? 0) > 0) {
                                $link->update(['status_scraping' => 'Completed']);
                            } else {
                                $link->update(['status_scraping' => 'Gagal']);
                                $failedCount++;
                            }
                        }
                    } else {
                        if (($link->views ?? 0) > 0 || ($link->likes ?? 0) > 0) {
                            $link->update(['status_scraping' => 'Completed']);
                        } else {
                            $link->update(['status_scraping' => 'Gagal']);
                            $failedCount++;
                        }
                    }
                } catch (\Throwable $e) {
                    \Log::error("Error re-scraping link {$link->id}: " . $e->getMessage());
                    if (($link->views ?? 0) > 0 || ($link->likes ?? 0) > 0) {
                        $link->update(['status_scraping' => 'Completed']);
                    } else {
                        $link->update(['status_scraping' => 'Gagal']);
                        $failedCount++;
                    }
                }
            }
        }

        // 3. Kalkulasi ulang seluruh Skor SAW untuk Campaign ini
        $this->recalculateSawForCampaign($campaign->id);

        return redirect()->route('update-saw.show', $campaign->id)
            ->with('success', "Proses Re-Scraping & Update SAW berhasil diselesaikan untuk {$processedCount} link.");
    }

    /**
     * Recalculate SAW score for campaign dataset
     */
    private function recalculateSawForCampaign($campaignId)
    {
        $links = Link::where('campaign_id', $campaignId)
            ->whereIn('status_scraping', ['Completed', 'Berhasil'])
            ->get();

        if ($links->isEmpty()) {
            return;
        }

        $maxViews = $links->max('views') ?: 1;
        $maxLikes = $links->max('likes') ?: 1;
        $maxComments = $links->max('comments') ?: 1;
        $maxShares = $links->max('shares') ?: 1;
        $maxSaves = $links->max('saves') ?: 1;
        $maxEr = $links->max('engagement_rate') ?: 1;

        foreach ($links as $link) {
            $normViews = $link->views / $maxViews;
            $normLikes = $link->likes / $maxLikes;
            $normComments = $link->comments / $maxComments;
            $normShares = $link->shares / $maxShares;
            $normSaves = $link->saves / $maxSaves;
            $normEr = $link->engagement_rate / $maxEr;

            // Bobot: ER (35%), Views (25%), Likes (15%), Comments (10%), Shares (10%), Saves (5%)
            $sawScore = ($normEr * 0.35) + ($normViews * 0.25) + ($normLikes * 0.15) + ($normComments * 0.10) + ($normShares * 0.10) + ($normSaves * 0.05);

            $link->update([
                'saw_score' => round($sawScore, 4)
            ]);
        }
    }
}
