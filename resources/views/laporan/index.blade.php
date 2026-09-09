<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-primary leading-tight">
            Laporan & Export Analytics 📄
        </h2>
    </x-slot>

    <div class="space-y-4 sm:space-y-6">
        <!-- Summary KPI Row -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-6">
            <x-kpi-card 
                title="Total Views" 
                value="{{ number_format($totalViews) }}" 
                trend="Tayangan Filtered" 
                trendType="up" 
                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>'
            />
            
            <x-kpi-card 
                title="Total Interaksi" 
                value="{{ number_format($totalLikes + $totalComments + $totalShares + $totalSaves) }}" 
                trend="Likes, Komen, Share, Save" 
                trendType="up" 
                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>'
            />

            <x-kpi-card 
                title="Shares & Saves" 
                value="{{ number_format($totalShares + $totalSaves) }}" 
                trend="{{ number_format($totalShares) }} Share / {{ number_format($totalSaves) }} Save" 
                trendType="up" 
                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>'
            />

            <x-kpi-card 
                title="Rata-rata ER" 
                value="{{ number_format($avgER, 2) }}%" 
                trend="Performa Konten" 
                trendType="up" 
                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>'
            />
        </div>

        <!-- Filter & Actions Bar -->
        <div class="bg-surface p-4 sm:p-6 rounded-2xl border border-border flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-sm">
            <form method="GET" action="{{ route('laporan.index') }}" class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2.5 sm:gap-3 w-full md:w-auto">
                <div class="w-full sm:w-auto">
                    <select name="campaign_id" onchange="this.form.submit()" class="w-full rounded-xl border-border bg-body text-primary text-xs sm:text-sm py-2 px-2.5 sm:px-3 focus:border-brand-blue focus:ring-brand-blue max-w-full sm:max-w-[200px] truncate">
                        <option value="">-- Semua Campaign --</option>
                        @foreach($campaigns as $camp)
                            <option value="{{ $camp->id }}" {{ request('campaign_id') == $camp->id ? 'selected' : '' }}>
                                {{ $camp->nama_campaign }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-auto">
                    <select name="platform" onchange="this.form.submit()" class="w-full rounded-xl border-border bg-body text-primary text-xs sm:text-sm py-2 px-2.5 sm:px-3 focus:border-brand-blue focus:ring-brand-blue">
                        <option value="">-- Semua Platform --</option>
                        <option value="tiktok" {{ request('platform') == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                        <option value="instagram" {{ request('platform') == 'instagram' ? 'selected' : '' }}>Instagram</option>
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div class="flex items-center gap-1.5 w-full sm:w-auto">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()" class="rounded-xl border-border bg-body text-primary text-xs sm:text-sm py-2 px-2.5 sm:px-3 focus:border-brand-blue" title="Dari Tanggal">
                    <span class="text-xs text-secondary font-medium">s/d</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()" class="rounded-xl border-border bg-body text-primary text-xs sm:text-sm py-2 px-2.5 sm:px-3 focus:border-brand-blue" title="Sampai Tanggal">
                </div>

                @if(request('campaign_id') || request('platform') || request('start_date') || request('end_date'))
                    <div class="col-span-2 sm:col-span-1 text-center sm:text-left py-1">
                        <a href="{{ route('laporan.index') }}" class="text-xs text-brand-blue hover:underline font-semibold">Reset Filter</a>
                    </div>
                @endif
            </form>

            <div class="flex items-center gap-2 sm:gap-3 w-full md:w-auto justify-end">
                <a href="{{ route('export.client.pdf', request()->all()) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 bg-status-danger/10 text-status-danger border border-transparent rounded-xl font-semibold text-xs uppercase tracking-wider hover:bg-status-danger/20 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export PDF
                </a>
                <a href="{{ route('export.client.excel', request()->all()) }}" class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 bg-status-success/10 text-status-success border border-transparent rounded-xl font-semibold text-xs uppercase tracking-wider hover:bg-status-success/20 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export Excel
                </a>
            </div>
        </div>

        <!-- Table Data Section -->
        <div class="bg-surface rounded-2xl border border-border overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead class="bg-body/50 text-[11px] sm:text-xs uppercase text-secondary">
                        <tr>
                            <th class="px-3 sm:px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-3 sm:px-4 py-3 font-semibold">Platform</th>
                            <th class="px-3 sm:px-4 py-3 font-semibold">Campaign</th>
                            <th class="px-3 sm:px-4 py-3 font-semibold">Akun</th>
                            <th class="px-3 sm:px-4 py-3 font-semibold">URL Konten</th>
                            <th class="px-3 sm:px-4 py-3 font-semibold text-right">Views</th>
                            <th class="px-3 sm:px-4 py-3 font-semibold text-right">Likes</th>
                            <th class="px-3 sm:px-4 py-3 font-semibold text-right">Comments</th>
                            <th class="px-3 sm:px-4 py-3 font-semibold text-right">Shares</th>
                            <th class="px-3 sm:px-4 py-3 font-semibold text-right">Saves</th>
                            <th class="px-3 sm:px-4 py-3 font-semibold text-right">ER (%)</th>
                            <th class="px-3 sm:px-4 py-3 font-semibold text-center">Status</th>
                            <th class="px-3 sm:px-4 py-3 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($links as $link)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-3 sm:px-4 py-3 text-xs sm:text-sm font-medium text-secondary whitespace-nowrap">
                                {{ $link->tanggal_upload ? \Carbon\Carbon::parse($link->tanggal_upload)->format('d/m/Y') : ($link->updated_at ? \Carbon\Carbon::parse($link->updated_at)->format('d/m/Y') : '-') }}
                            </td>
                            <td class="px-3 sm:px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 bg-body border border-border rounded-lg text-xs font-semibold">{{ ucfirst($link->platform) }}</span>
                            </td>
                            <td class="px-3 sm:px-4 py-3 text-xs sm:text-sm font-medium text-primary">{{ $link->campaign->nama_campaign ?? '-' }}</td>
                            @php
                                $uName = trim($link->username ?? '');
                                if (empty($uName) || str_contains($uName, '{') || str_contains($uName, '$')) {
                                    $displayAccount = '-';
                                } else {
                                    $displayAccount = str_starts_with($uName, '@') ? $uName : '@' . $uName;
                                }
                            @endphp
                            <td class="px-3 sm:px-4 py-3 text-xs sm:text-sm font-bold text-secondary">{{ $displayAccount }}</td>
                            <td class="px-3 sm:px-4 py-3 text-xs text-secondary">
                                <a href="{{ $link->url }}" target="_blank" class="text-brand-blue hover:underline truncate inline-block max-w-[150px]">
                                    {{ $link->url }}
                                </a>
                            </td>
                            <td class="px-3 sm:px-4 py-3 text-xs sm:text-sm text-right font-medium text-primary">{{ number_format($link->views ?? 0) }}</td>
                            <td class="px-3 sm:px-4 py-3 text-xs sm:text-sm text-right text-secondary">{{ number_format($link->likes ?? 0) }}</td>
                            <td class="px-3 sm:px-4 py-3 text-xs sm:text-sm text-right text-secondary">{{ number_format($link->comments ?? 0) }}</td>
                            <td class="px-3 sm:px-4 py-3 text-xs sm:text-sm text-right font-medium text-indigo-600 dark:text-indigo-400">{{ number_format($link->shares ?? 0) }}</td>
                            <td class="px-3 sm:px-4 py-3 text-xs sm:text-sm text-right font-medium text-amber-600 dark:text-amber-400">{{ number_format($link->saves ?? 0) }}</td>
                            <td class="px-3 sm:px-4 py-3 text-xs sm:text-sm text-right font-bold {{ $link->engagement_rate > 5 ? 'text-status-success' : 'text-status-warning' }}">{{ number_format($link->engagement_rate ?? 0, 2) }}%</td>
                            <td class="px-3 sm:px-4 py-3 text-center">
                                @if(in_array($link->status_scraping, ['Completed', 'Berhasil']))
                                    <span class="inline-flex px-2 py-0.5 rounded-full bg-status-success/10 text-status-success text-xs font-bold">Selesai</span>
                                @elseif($link->status_scraping === 'Pending')
                                    <span class="inline-flex px-2 py-0.5 rounded-full bg-status-warning/10 text-status-warning text-xs font-bold">Antrean</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full bg-status-danger/10 text-status-danger text-xs font-bold">Gagal</span>
                                @endif
                            </td>
                            <td class="px-3 sm:px-4 py-3 text-center">
                                <a href="{{ route('laporan.show', $link->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-brand-blue/10 text-brand-blue hover:bg-brand-blue/20 rounded-xl text-xs font-bold transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="13" class="px-6 py-12 text-center text-secondary">Belum ada data laporan yang cocok.</td>
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
