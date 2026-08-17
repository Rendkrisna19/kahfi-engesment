<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function index(Request $request)
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

        $query = Link::whereIn('campaign_id', $campaignIds)->with('campaign', 'kategoriKonten');

        // Filter by Campaign if selected
        if ($request->has('campaign_id') && $request->campaign_id != '') {
            // Ensure client cannot filter to campaign they don't own
            if ($campaignIds->contains($request->campaign_id)) {
                $query->where('campaign_id', $request->campaign_id);
            } else {
                $query->whereRaw('1 = 0'); // force empty
            }
        }

        // Filter by Platform if selected
        if ($request->has('platform') && $request->platform != '') {
            $query->where('platform', $request->platform);
        }

        $links = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $totalViews = (clone $query)->sum('views');
        $totalLikes = (clone $query)->sum('likes');
        $totalComments = (clone $query)->sum('comments');
        $avgER = (clone $query)->where('engagement_rate', '>', 0)->avg('engagement_rate') ?? 0;

        return view('laporan.index', compact('campaigns', 'links', 'totalViews', 'totalLikes', 'totalComments', 'avgER'));
    }

    public function show($id)
    {
        $user = Auth::user();

        $link = Link::with(['campaign', 'kategoriKonten', 'kategoriCreator'])->findOrFail($id);

        if ($user->hasRole('Admin Master') || $user->role === 'Admin Master') {
            // Super Admin can see all
        } elseif ($user->hasRole('Admin') || $user->role === 'Admin') {
            $campaignIds = \App\Models\UserCampaignAccess::where('user_id', $user->id)->pluck('campaign_id');
            if (!$campaignIds->contains($link->campaign_id)) {
                abort(403, 'Anda tidak memiliki akses ke laporan link ini.');
            }
        } elseif ($user->hasRole('Client') || $user->role === 'Client') {
            if ($link->campaign->client_id != $user->id) {
                abort(403, 'Anda tidak memiliki akses ke laporan link ini.');
            }
        } else {
            abort(403, 'Anda tidak memiliki akses.');
        }

        return view('laporan.show', compact('link'));
    }
}
