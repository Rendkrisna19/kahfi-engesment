<x-app-layout>
    <x-slot name="title">Update Campaign - Kahfi Engagement</x-slot>

    <div class="space-y-6">
        <!-- Header & Description -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-surface p-6 rounded-2xl border border-border shadow-xs">
            <div>
                <h2 class="text-2xl font-bold text-primary tracking-tight">Update Campaign</h2>
                <p class="text-sm text-secondary mt-1">Pilih Campaign untuk menjalankan Re-Scraping Apify secara massal atau parsial, serta pantau kenaikan metrik secara real-time.</p>
            </div>
            
            <!-- Search & Platform Filter -->
            <form method="GET" action="{{ route('update-saw.index') }}" class="flex flex-wrap items-center gap-2">
                <!-- Platform Filter -->
                <select name="platform" onchange="this.form.submit()" class="text-xs border-border bg-body text-primary rounded-xl focus:border-brand-blue focus:ring-brand-blue py-2 px-3">
                    <option value="">-- Semua Platform --</option>
                    <option value="TikTok" {{ request('platform') == 'TikTok' ? 'selected' : '' }}>TikTok</option>
                    <option value="Instagram" {{ request('platform') == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                </select>

                <!-- Search Input -->
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Campaign..." class="text-xs border-border bg-body text-primary rounded-xl pl-9 pr-3 py-2 focus:border-brand-blue focus:ring-brand-blue min-w-[180px]">
                    <svg class="w-4 h-4 absolute left-3 top-2.5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                @if(request()->hasAny(['search', 'platform']))
                    <a href="{{ route('update-saw.index') }}" class="text-xs text-status-danger font-semibold hover:underline">Reset</a>
                @endif
            </form>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-status-success/10 border border-status-success/20 text-status-success text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-status-danger/10 border border-status-danger/20 text-status-danger text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <!-- Grid Cards Campaign (Compact 4 Per Page) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($campaigns as $campaign)
            @php
                $now = \Carbon\Carbon::now()->startOfDay();
                $deadline = $campaign->tanggal_selesai ? \Carbon\Carbon::parse($campaign->tanggal_selesai)->startOfDay() : null;
                $daysRemaining = $deadline ? (int) $now->diffInDays($deadline, false) : null;
            @endphp
            <div class="bg-surface rounded-2xl border border-border hover:border-brand-blue/40 transition-all duration-300 shadow-xs hover:shadow-sm flex flex-col justify-between overflow-hidden group">
                <div class="p-4">
                    <!-- Top Badge & Title -->
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-brand-blue/10 text-brand-blue flex items-center justify-center font-extrabold text-sm group-hover:scale-105 transition-transform shrink-0">
                            {{ strtoupper(substr($campaign->nama_campaign, 0, 2)) }}
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $campaign->links_count > 0 ? 'bg-status-success/10 text-status-success' : 'bg-gray-100 text-secondary' }}">
                                {{ $campaign->links_count }} Link
                            </span>
                            
                            <!-- Deadline Badge -->
                            @if($daysRemaining !== null)
                                @if($daysRemaining < 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 border border-rose-200 dark:border-rose-800" title="Tenggat Selesai pada {{ \Carbon\Carbon::parse($campaign->tanggal_selesai)->format('d M Y') }}">
                                        🔴 Expired
                                    </span>
                                @elseif($daysRemaining == 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                        ⚠️ Hari Ini
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        ⏳ Sisa {{ $daysRemaining }} Hari
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>

                    <h3 class="text-sm font-bold text-primary group-hover:text-brand-blue transition-colors line-clamp-1" title="{{ $campaign->nama_campaign }}">
                        {{ $campaign->nama_campaign }}
                    </h3>
                    <p class="text-[11px] text-secondary mt-0.5 truncate">
                        Client: <strong class="text-primary">{{ $campaign->client->name ?? 'Semua Client' }}</strong>
                    </p>

                    <!-- Stat Summary Grid -->
                    <div class="grid grid-cols-2 gap-2 mt-3 pt-3 border-t border-border/60">
                        <div class="bg-body/60 p-2 rounded-lg">
                            <span class="text-[10px] text-secondary font-medium block">Total Views</span>
                            <span class="text-xs font-bold text-primary mt-0.5 block">
                                {{ number_format($campaign->total_views) }}
                            </span>
                        </div>
                        <div class="bg-body/60 p-2 rounded-lg">
                            <span class="text-[10px] text-secondary font-medium block">Avg ER (%)</span>
                            <span class="text-xs font-bold text-brand-blue mt-0.5 block">
                                {{ number_format($campaign->avg_er ?? 0, 2) }}%
                            </span>
                        </div>
                    </div>

                    <div class="space-y-1 mt-3 pt-2 border-t border-border/40 text-[10px]">
                        <div class="flex items-center justify-between text-secondary">
                            <span>Deadline:</span>
                            <span class="font-semibold text-primary">
                                {{ $campaign->tanggal_selesai ? \Carbon\Carbon::parse($campaign->tanggal_selesai)->format('d/m/Y') : 'Tanpa Tenggat' }}
                            </span>
                        </div>

                        <div class="flex items-start justify-between text-secondary">
                            <span class="pt-0.5">Re-Scrape:</span>
                            <span class="text-right">
                                @if($campaign->last_rescrape)
                                    <strong class="block text-primary text-[10px]">{{ \Carbon\Carbon::parse($campaign->last_rescrape)->format('d/m/Y H:i') }}</strong>
                                    <span class="block text-[9px] text-brand-blue font-medium">({{ \Carbon\Carbon::parse($campaign->last_rescrape)->diffForHumans() }})</span>
                                @else
                                    <span class="text-muted font-normal">Belum Pernah</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Footer Card Link -->
                <div class="px-4 py-2.5 bg-body/40 border-t border-border flex items-center justify-between">
                    <span class="text-[10px] text-secondary">
                        <strong class="text-status-success">{{ $campaign->completed_count }}</strong> Selesai
                        @if($campaign->pending_count > 0)
                            • <strong class="text-status-warning">{{ $campaign->pending_count }}</strong> Pending
                        @endif
                    </span>
                    <a href="{{ route('update-saw.show', $campaign->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-brand-blue text-white hover:bg-brand-blue-hover transition-all shadow-2xs">
                        <span>Pilih</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full bg-surface p-12 rounded-2xl border border-border text-center">
                <svg class="w-12 h-12 text-secondary mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <h4 class="text-base font-bold text-primary">Tidak Ada Campaign Ditemukan</h4>
                <p class="text-xs text-secondary mt-1">Belum ada campaign yang dibuat atau sesuai dengan kata kunci pencarian Anda.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $campaigns->links() }}
        </div>
    </div>
</x-app-layout>
