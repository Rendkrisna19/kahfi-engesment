<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-primary leading-tight">
            Laporan & Export Analytics 📄
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Summary KPI Row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <x-kpi-card 
                title="Total Tayangan (Views)" 
                value="{{ number_format($totalViews) }}" 
                trend="Total Views Filtered" 
                trendType="up" 
                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>'
            />
            
            <x-kpi-card 
                title="Total Interaksi" 
                value="{{ number_format($totalLikes + $totalComments) }}" 
                trend="Likes & Comments" 
                trendType="up" 
                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>'
            />

            <x-kpi-card 
                title="Rata-rata Engagement Rate" 
                value="{{ number_format($avgER, 2) }}%" 
                trend="Performa Konten" 
                trendType="up" 
                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>'
            />
        </div>

        <!-- Filter & Actions Bar -->
        <div class="bg-surface p-6 rounded-2xl border border-border flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-sm">
            <form method="GET" action="{{ route('laporan.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <div>
                    <select name="campaign_id" onchange="this.form.submit()" class="rounded-xl border-border bg-body text-primary text-sm focus:border-brand-blue focus:ring-brand-blue">
                        <option value="">-- Semua Campaign --</option>
                        @foreach($campaigns as $camp)
                            <option value="{{ $camp->id }}" {{ request('campaign_id') == $camp->id ? 'selected' : '' }}>
                                {{ $camp->nama_campaign }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="platform" onchange="this.form.submit()" class="rounded-xl border-border bg-body text-primary text-sm focus:border-brand-blue focus:ring-brand-blue">
                        <option value="">-- Semua Platform --</option>
                        <option value="tiktok" {{ request('platform') == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                        <option value="instagram" {{ request('platform') == 'instagram' ? 'selected' : '' }}>Instagram</option>
                    </select>
                </div>

                @if(request('campaign_id') || request('platform'))
                    <a href="{{ route('laporan.index') }}" class="text-xs text-brand-blue hover:underline font-semibold">Reset Filter</a>
                @endif
            </form>

            <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                <a href="{{ route('export.client.pdf') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-status-danger/10 text-status-danger border border-transparent rounded-xl font-semibold text-xs uppercase tracking-widest hover:bg-status-danger/20 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export PDF
                </a>
                <a href="{{ route('export.client.excel') }}" class="inline-flex items-center px-4 py-2 bg-status-success/10 text-status-success border border-transparent rounded-xl font-semibold text-xs uppercase tracking-widest hover:bg-status-success/20 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export Excel
                </a>
            </div>
        </div>

        <!-- Table Data Section -->
        <div class="bg-surface rounded-2xl border border-border overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-body/50 text-xs uppercase text-secondary">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Platform</th>
                            <th class="px-6 py-4 font-semibold">Campaign</th>
                            <th class="px-6 py-4 font-semibold">URL Konten</th>
                            <th class="px-6 py-4 font-semibold text-right">Views</th>
                            <th class="px-6 py-4 font-semibold text-right">Likes</th>
                            <th class="px-6 py-4 font-semibold text-right">Comments</th>
                            <th class="px-6 py-4 font-semibold text-right">ER (%)</th>
                            <th class="px-6 py-4 font-semibold text-center">Status</th>
                            <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($links as $link)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 bg-body border border-border rounded-lg text-xs font-semibold">{{ ucfirst($link->platform) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-primary">{{ $link->campaign->nama_campaign ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-secondary">
                                <a href="{{ $link->url }}" target="_blank" class="text-brand-blue hover:underline truncate inline-block max-w-[200px]">
                                    {{ $link->url }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-right font-medium text-primary">{{ number_format($link->views) }}</td>
                            <td class="px-6 py-4 text-sm text-right text-secondary">{{ number_format($link->likes) }}</td>
                            <td class="px-6 py-4 text-sm text-right text-secondary">{{ number_format($link->comments) }}</td>
                            <td class="px-6 py-4 text-sm text-right font-bold {{ $link->engagement_rate > 5 ? 'text-status-success' : 'text-status-warning' }}">{{ number_format($link->engagement_rate, 2) }}%</td>
                            <td class="px-6 py-4 text-center">
                                @if(in_array($link->status_scraping, ['Completed', 'Berhasil']))
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-status-success/10 text-status-success text-xs font-bold">Selesai</span>
                                @elseif($link->status_scraping === 'Pending')
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-status-warning/10 text-status-warning text-xs font-bold">Antrean</span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-status-danger/10 text-status-danger text-xs font-bold">Gagal</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('laporan.show', $link->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-brand-blue/10 text-brand-blue hover:bg-brand-blue/20 rounded-xl text-xs font-bold transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-secondary">Belum ada data laporan yang cocok.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($links->hasPages())
                <div class="px-6 py-4 border-t border-border">
                    {{ $links->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
