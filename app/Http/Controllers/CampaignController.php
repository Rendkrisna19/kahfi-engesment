<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DAFTAR CAMPAIGN
    |--------------------------------------------------------------------------
    |
    | Admin Master -> semua campaign
    | Admin        -> campaign yang diberikan akses
    | Client       -> campaign miliknya
    |
    */

    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Campaign::with(['client', 'userAccess.user']);

        if ($user->role === 'Admin Master' || $user->hasRole('Admin Master')) {

            $query->orderBy('tanggal_mulai', 'desc');
        } elseif ($user->role === 'Admin' || $user->hasRole('Admin')) {

            $query->whereHas('userAccess', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });

            $query->orderBy('tanggal_mulai', 'desc');
        } elseif ($user->role === 'Client' || $user->hasRole('Client')) {

            $query->where('client_id', $user->id)
                ->orderBy('tanggal_mulai', 'desc');
        } else {

            abort(403, 'Role tidak dikenali.');
        }

        $campaigns = $query->get();

        return view('campaigns.index', compact('campaigns'));
    }


    /*
    |--------------------------------------------------------------------------
    | KELOLA CAMPAIGN - ADMIN MASTER
    |--------------------------------------------------------------------------
    */

    public function create(): View
    {
        $clients = User::where('role', 'Client')
            ->where('status', 'Aktif')
            ->orderBy('name')
            ->get();

        $admins = User::whereIn('role', ['Admin', 'Admin Master'])
            ->where('status', 'Aktif')
            ->orderBy('name')
            ->get();

        return view('campaigns.create', compact('clients', 'admins'));
    }


    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],

            'nama_campaign' => [
                'required',
                'string',
                'max:255',
            ],

            'platform' => [
                'required',
                'string',
                'max:100',
            ],

            'tanggal_mulai' => [
                'required',
                'date',
            ],

            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],

            'deskripsi' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:Draft,Aktif,Selesai,Arsip',
            ],

            'admin_ids' => [
                'nullable',
                'array',
            ],

            'admin_ids.*' => [
                'integer',
                'exists:users,id',
            ],
        ]);

        $adminIds = $validated['admin_ids'] ?? [];
        unset($validated['admin_ids']);

        $campaign = Campaign::create($validated);

        if (!empty($adminIds)) {
            foreach ($adminIds as $adminId) {
                \App\Models\UserCampaignAccess::create([
                    'user_id' => $adminId,
                    'campaign_id' => $campaign->id,
                ]);
            }
        }

        return redirect()
            ->route('campaigns.index')
            ->with('success', 'Campaign berhasil ditambahkan.');
    }


    public function edit(Campaign $campaign): View
    {
        $clients = User::where('role', 'Client')
            ->where('status', 'Aktif')
            ->orderBy('name')
            ->get();

        $admins = User::whereIn('role', ['Admin', 'Admin Master'])
            ->where('status', 'Aktif')
            ->orderBy('name')
            ->get();

        $assignedAdminIds = $campaign->userAccess()->pluck('user_id')->toArray();

        return view(
            'campaigns.edit',
            compact('campaign', 'clients', 'admins', 'assignedAdminIds')
        );
    }


    public function update(
        Request $request,
        Campaign $campaign
    ): RedirectResponse {

        $validated = $request->validate([
            'client_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],

            'nama_campaign' => [
                'required',
                'string',
                'max:255',
            ],

            'platform' => [
                'required',
                'string',
                'max:100',
            ],

            'tanggal_mulai' => [
                'required',
                'date',
            ],

            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],

            'deskripsi' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:Draft,Aktif,Selesai,Arsip',
            ],

            'admin_ids' => [
                'nullable',
                'array',
            ],

            'admin_ids.*' => [
                'integer',
                'exists:users,id',
            ],
        ]);

        $adminIds = $validated['admin_ids'] ?? [];
        unset($validated['admin_ids']);

        $campaign->update($validated);

        // Sync access
        $campaign->userAccess()->delete();
        if (!empty($adminIds)) {
            foreach ($adminIds as $adminId) {
                \App\Models\UserCampaignAccess::create([
                    'user_id' => $adminId,
                    'campaign_id' => $campaign->id,
                ]);
            }
        }

        return redirect()
            ->route('campaigns.index')
            ->with('success', 'Campaign berhasil diperbarui.');
    }


    public function destroy(
        Campaign $campaign
    ): RedirectResponse {

        $campaign->delete();

        return redirect()
            ->route('campaigns.index')
            ->with('success', 'Campaign berhasil dihapus.');
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN - CAMPAIGN YANG DITUGASKAN
    |--------------------------------------------------------------------------
    */

    public function adminIndex(Request $request): View
    {
        $user = $request->user();

        $campaigns = Campaign::with('client')
            ->whereHas('userAccess', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view(
            'admin.campaigns.index',
            compact('campaigns')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN - DETAIL CAMPAIGN
    |--------------------------------------------------------------------------
    */

    public function adminShow(
        Request $request,
        Campaign $campaign
    ): View {

        $user = $request->user();

        /*
        | Pastikan Admin memang mempunyai akses
        | ke campaign tersebut.
        */

        $hasAccess = $campaign->userAccess()
            ->where('user_id', $user->id)
            ->exists();

        if (! $hasAccess) {

            abort(
                403,
                'Anda tidak memiliki akses ke campaign ini.'
            );
        }

        return view(
            'admin.campaigns.show',
            compact('campaign')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN - INPUT LINK KONTEN
    |--------------------------------------------------------------------------
    */

    public function updateContent(
        Request $request,
        Campaign $campaign
    ): RedirectResponse {

        $user = $request->user();

        /*
        | Pastikan Admin mempunyai akses
        | ke campaign tersebut.
        */

        $hasAccess = $campaign->userAccess()
            ->where('user_id', $user->id)
            ->exists();

        if (! $hasAccess) {

            abort(
                403,
                'Anda tidak memiliki akses ke campaign ini.'
            );
        }

        /*
        | Validasi link konten.
        */

        $validated = $request->validate([
            'content_link' => [
                'required',
                'url',
                'max:2048',
            ],
        ]);

        /*
        | Untuk sementara kita simpan di session.
        |
        | Nanti bagian ini kita hubungkan dengan
        | tabel link/content yang sesuai sistem final.
        */

        session()->flash(
            'success',
            'Link konten berhasil disimpan.'
        );

        return redirect()
            ->route(
                'admin.campaigns.show',
                $campaign
            );
    }


    /*
    |--------------------------------------------------------------------------
    | CLIENT - DAFTAR CAMPAIGN
    |--------------------------------------------------------------------------
    */

    public function clientIndex(Request $request): View
    {
        $user = $request->user();

        $campaigns = Campaign::with('client')
            ->where('client_id', $user->id)
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view(
            'client.campaigns.index',
            compact('campaigns')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CLIENT - DETAIL CAMPAIGN
    |--------------------------------------------------------------------------
    */

    public function clientShow(
        Request $request,
        Campaign $campaign
    ): View {

        $user = $request->user();

        if ($campaign->client_id !== $user->id) {

            abort(
                403,
                'Anda tidak memiliki akses ke campaign ini.'
            );
        }

        return view(
            'client.campaigns.show',
            compact('campaign')
        );
    }
}
