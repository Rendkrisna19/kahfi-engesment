<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private function getDashboardData(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('Admin Master') || $user->role === 'Admin Master') {
            $campaigns = Campaign::all();
            $campaignIds = $campaigns->pluck('id');
        } elseif ($user->hasRole('Admin') || $user->role === 'Admin') {
            $accessIds = \App\Models\UserCampaignAccess::where('user_id', $user->id)->pluck('campaign_id');
            if ($accessIds->count() > 0) {
                $campaigns = Campaign::whereIn('id', $accessIds)->get();
            } else {
                $campaigns = Campaign::all();
            }
            $campaignIds = $campaigns->pluck('id');
        } elseif ($user->hasRole('Client') || $user->role === 'Client') {
            $campaigns = Campaign::where('client_id', $user->id)->get();
            $campaignIds = $campaigns->pluck('id');
        } else {
            $campaigns = collect();
            $campaignIds = collect();
        }

        $query = Link::whereIn('campaign_id', $campaignIds);

        // Apply filters
        if ($request->filled('campaign_id')) {
            $cId = (int) $request->campaign_id;
            if ($campaignIds->map(fn($id) => (int)$id)->contains($cId)) {
                $query->where('campaign_id', $cId);
            }
        }

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        if ($request->filled('year')) {
            $query->whereYear('tanggal_upload', $request->year);
        }
        if ($request->filled('month')) {
            $query->whereMonth('tanggal_upload', $request->month);
        }
        if ($request->filled('day')) {
            $query->whereDay('tanggal_upload', $request->day);
        }

        $totalCampaigns = $request->filled('campaign_id') ? 1 : $campaigns->count();
        $totalLinks = (clone $query)->count();
        $totalViews = (clone $query)->sum('views');
        $totalLikes = (clone $query)->sum('likes');
        $totalComments = (clone $query)->sum('comments');
        $totalShares = (clone $query)->sum('shares');
        $totalSaves = (clone $query)->sum('saves');
        $avgER = (clone $query)->where('engagement_rate', '>', 0)->avg('engagement_rate') ?? 0;
        $avgSawScore = (clone $query)->where('saw_score', '>', 0)->avg('saw_score') ?? 0;

        // Top 5 Campaigns by Views (matching date filters)
        $topCampaigns = Campaign::whereIn('id', $campaignIds)
            ->withSum(['links' => function($q) use($request) {
                if ($request->filled('platform')) $q->where('platform', $request->platform);
                if ($request->filled('year')) $q->whereYear('tanggal_upload', $request->year);
                if ($request->filled('month')) $q->whereMonth('tanggal_upload', $request->month);
                if ($request->filled('day')) $q->whereDay('tanggal_upload', $request->day);
            }], 'views')
            ->orderBy('links_sum_views', 'desc')
            ->take(5)
            ->get();

        // 3 Individual Charts (Views, Likes, Comments by Platform)
        $platformViews = (clone $query)
            ->select('platform', \Illuminate\Support\Facades\DB::raw('SUM(views) as total_views'))
            ->groupBy('platform')
            ->pluck('total_views', 'platform')
            ->toArray();

        $platformLikes = (clone $query)
            ->select('platform', \Illuminate\Support\Facades\DB::raw('SUM(likes) as total_likes'))
            ->groupBy('platform')
            ->pluck('total_likes', 'platform')
            ->toArray();

        $platformComments = (clone $query)
            ->select('platform', \Illuminate\Support\Facades\DB::raw('SUM(comments) as total_comments'))
            ->groupBy('platform')
            ->pluck('total_comments', 'platform')
            ->toArray();

        // Top Content Ranking (Top 5 Links by Views, Likes, Comments, SAW)
        $topContent = (clone $query)
            ->with('campaign', 'kategoriKonten')
            ->orderBy('saw_score', 'desc')
            ->take(5)
            ->get();

        // Links for Table
        $links = (clone $query)
            ->with('campaign', 'kategoriKonten')
            ->orderBy('id', 'desc')
            ->get();

        // Available years for filter dropdowns
        $availableYears = Link::whereIn('campaign_id', $campaignIds)
            ->whereNotNull('tanggal_upload')
            ->selectRaw('YEAR(tanggal_upload) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return compact(
            'campaigns', 'totalCampaigns', 'totalLinks', 'totalViews', 'totalLikes', 'totalComments', 'totalShares', 'totalSaves', 'avgER', 'avgSawScore',
            'topCampaigns', 'platformViews', 'platformLikes', 'platformComments', 'topContent', 'links', 'availableYears'
        );
    }

    public function adminMaster(Request $request)
    {
        $data = $this->getDashboardData($request);
        return view('dashboard.admin-master', $data);
    }

    public function admin(Request $request)
    {
        $data = $this->getDashboardData($request);
        return view('dashboard.admin', $data);
    }

    public function client(Request $request)
    {
        $data = $this->getDashboardData($request);
        return view('dashboard.client', $data);
    }
}
