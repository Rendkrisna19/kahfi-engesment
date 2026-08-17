<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use App\Models\UserCampaignAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserCampaignAccessController extends Controller
{
    /**
     * Menampilkan daftar campaign
     * untuk pengaturan hak akses.
     */
    public function index(): View
    {
        $campaigns = Campaign::with('client')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('campaign-access.index', compact('campaigns'));
    }

    /**
     * Menampilkan form pengaturan
     * hak akses campaign tertentu.
     */
    public function edit(Campaign $campaign): View
    {
        $users = User::whereIn('role', ['Admin', 'Client'])
            ->where('status', 'Aktif')
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        $accessUserIds = UserCampaignAccess::where(
            'campaign_id',
            $campaign->id
        )
            ->pluck('user_id')
            ->toArray();

        return view('campaign-access.edit', compact(
            'campaign',
            'users',
            'accessUserIds'
        ));
    }

    /**
     * Menyimpan perubahan hak akses campaign.
     */
    public function update(
        Request $request,
        Campaign $campaign
    ): RedirectResponse {
        $validated = $request->validate([
            'user_ids' => [
                'nullable',
                'array',
            ],

            'user_ids.*' => [
                'integer',
                'exists:users,id',
            ],
        ]);

        $userIds = $validated['user_ids'] ?? [];

        $allowedUserIds = User::whereIn('id', $userIds)
            ->whereIn('role', ['Admin', 'Client'])
            ->where('status', 'Aktif')
            ->pluck('id')
            ->toArray();

        UserCampaignAccess::where(
            'campaign_id',
            $campaign->id
        )->delete();

        foreach ($allowedUserIds as $userId) {
            UserCampaignAccess::create([
                'user_id' => $userId,
                'campaign_id' => $campaign->id,
            ]);
        }

        return redirect()
            ->route('campaign-access.index')
            ->with(
                'success',
                'Hak akses campaign berhasil diperbarui.'
            );
    }
}
