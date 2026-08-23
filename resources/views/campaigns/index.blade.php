<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-2xl text-primary leading-tight">
                Kelola Campaign
            </h2>
            <p class="text-sm text-secondary mt-1">Buat dan atur campaign yang akan digunakan untuk mengelompokkan link konten kreator.</p>
        </div>
    </x-slot>

    <x-toast />

    <div class="space-y-6" x-data="{ isLoaded: false }" x-init="setTimeout(() => isLoaded = true, 1000)">
        
        <!-- Kpi Summary (Optional for Admin Master) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <x-kpi-card 
                title="Total Campaign" 
                value="{{ $campaigns->count() }}" 
                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>'
            />
            <x-kpi-card 
                title="Campaign Aktif" 
                value="{{ $campaigns->where('status', 'Aktif')->count() }}" 
                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            />
            <x-kpi-card 
                title="Campaign Selesai" 
                value="{{ $campaigns->where('status', 'Selesai')->count() }}" 
                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>'
            />
        </div>

        <div class="bg-surface rounded-2xl border border-border overflow-hidden shadow-sm">
            <div class="p-6 border-b border-border flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h3 class="text-lg font-bold text-primary">Daftar Campaign</h3>
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="search" class="block w-full p-2.5 pl-10 text-sm border-border bg-body text-primary rounded-xl focus:border-brand-blue focus:ring-brand-blue shadow-sm" placeholder="Cari nama campaign...">
                    </div>
                    @can('campaigns.create')
                    <a href="{{ route('campaigns.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-brand-blue border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-blue-hover active:bg-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2 transition ease-in-out duration-150 shadow-md shadow-brand-blue/30 whitespace-nowrap">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Campaign
                    </a>
                    @endcan
                </div>
            </div>

            <div x-show="!isLoaded">
                <x-skeleton count="5" />
            </div>

            <div x-show="isLoaded" style="display: none;" class="overflow-x-auto">
                <table class="w-full text-sm text-left text-primary">
                    <thead class="text-xs text-secondary uppercase bg-body/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">Nama Campaign</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Platform</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Klien / PIC</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Admin Ditugaskan</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Periode</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Status</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($campaigns as $campaign)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-primary">{{ $campaign->nama_campaign }}</p>
                                <p class="text-xs text-secondary mt-0.5 truncate max-w-xs">{{ $campaign->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if(strtolower($campaign->platform) === 'tiktok')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                                        TikTok
                                    </span>
                                @elseif(strtolower($campaign->platform) === 'instagram')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-pink-50 text-pink-600 dark:bg-pink-500/10 dark:text-pink-400">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                        Instagram
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                        {{ $campaign->platform }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-brand-gradient flex items-center justify-center text-[10px] text-white font-bold">
                                        {{ substr($campaign->client->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-sm">{{ $campaign->client->name ?? 'Tidak ada Klien' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1 max-w-[200px]">
                                    @forelse($campaign->userAccess as $access)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-brand-blue/10 text-brand-blue dark:bg-brand-blue/20">
                                            {{ $access->user->name ?? 'Admin' }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-secondary italic">Semua Admin Master</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="flex items-center gap-1 text-secondary">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ \Carbon\Carbon::parse($campaign->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($campaign->tanggal_selesai)->format('d M Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($campaign->status === 'Aktif')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-status-success/10 text-status-success">Aktif</span>
                                @elseif($campaign->status === 'Draft')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">Draft</span>
                                @elseif($campaign->status === 'Selesai')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-brand-blue/10 text-brand-blue">Selesai</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-status-warning/10 text-status-warning">{{ $campaign->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @can('campaigns.edit')
                                    <a href="{{ route('campaigns.edit', $campaign) }}" class="text-secondary hover:text-brand-blue p-2 rounded-lg hover:bg-brand-blue/10 transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    @endcan
                                    @can('campaigns.delete')
                                    <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus campaign ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-secondary hover:text-status-danger p-2 rounded-lg hover:bg-status-danger/10 transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4 text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    </div>
                                    <p class="text-secondary mb-1 font-medium">Belum ada Campaign</p>
                                    <p class="text-sm text-muted">Mulai dengan menambahkan campaign baru.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>