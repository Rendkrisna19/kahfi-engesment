<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-primary leading-tight">
            Halo {{ explode(' ', Auth::user()->name)[0] }}, Selamat Datang Kembali! 👏
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Interactive Real-time Filters -->
        <div class="bg-surface p-6 rounded-2xl border border-border shadow-sm flex flex-wrap gap-4 items-center justify-between">
            <form method="GET" action="{{ route('dashboard.client') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <!-- Dropdown Platform -->
                <div>
                    <select name="platform" onchange="this.form.submit()" class="rounded-xl border-border bg-body text-primary text-sm focus:border-brand-blue focus:ring-brand-blue">
                        <option value="">-- Platform --</option>
                        <option value="TikTok" {{ request('platform') == 'TikTok' ? 'selected' : '' }}>TikTok</option>
                        <option value="Instagram" {{ request('platform') == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                    </select>
                </div>

                <!-- Dropdown Campaign -->
                <div>
                    <select name="campaign_id" onchange="this.form.submit()" class="rounded-xl border-border bg-body text-primary text-sm focus:border-brand-blue focus:ring-brand-blue max-w-[200px] truncate">
                        <option value="">-- Semua Campaign Anda --</option>
                        @foreach($campaigns as $camp)
                            <option value="{{ $camp->id }}" {{ request('campaign_id') == $camp->id ? 'selected' : '' }}>
                                {{ $camp->nama_campaign }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown Tahun -->
                <div>
                    <select name="year" onchange="this.form.submit()" class="rounded-xl border-border bg-body text-primary text-sm focus:border-brand-blue focus:ring-brand-blue">
                        <option value="">-- Tahun --</option>
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown Bulan -->
                <div>
                    <select name="month" onchange="this.form.submit()" class="rounded-xl border-border bg-body text-primary text-sm focus:border-brand-blue focus:ring-brand-blue">
                        <option value="">-- Bulan --</option>
                        @php
                            $months = [
                                '1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April',
                                '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus',
                                '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                            ];
                        @endphp
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown Hari -->
                <div>
                    <select name="day" onchange="this.form.submit()" class="rounded-xl border-border bg-body text-primary text-sm focus:border-brand-blue focus:ring-brand-blue">
                        <option value="">-- Hari --</option>
                        @for($d = 1; $d <= 31; $d++)
                            <option value="{{ $d }}" {{ request('day') == $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endfor
                    </select>
                </div>

                @if(request('platform') || request('campaign_id') || request('year') || request('month') || request('day'))
                    <a href="{{ route('dashboard.client') }}" class="text-xs text-brand-blue hover:underline font-semibold">Reset Filter</a>
                @endif
            </form>
        </div>

        <!-- Main Highlight & Compact KPIs Grid (8 Cards) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 gap-4">
            <!-- Campaign & Links -->
            <div class="bg-brand-gradient rounded-2xl p-4 text-white shadow-md flex flex-col justify-between hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-white/80 font-medium">Campaign Anda</span>
                    <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-full font-semibold">{{ number_format($totalLinks) }} Link</span>
                </div>
                <div class="mt-2">
                    <h3 class="text-2xl font-extrabold">{{ number_format($totalCampaigns) }}</h3>
                    <p class="text-[10px] text-white/80 mt-0.5">Campaign Terfilter</p>
                </div>
            </div>

            <!-- Total Views -->
            <div class="bg-surface rounded-2xl p-4 border border-border shadow-xs flex flex-col justify-between hover:border-brand-blue/30 transition">
                <div class="flex items-center justify-between text-secondary">
                    <span class="text-xs font-semibold">Total Views</span>
                    <span class="p-1.5 rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <h3 class="text-xl font-extrabold text-primary">{{ number_format($totalViews) }}</h3>
                    <span class="text-[10px] text-status-success font-semibold">👁️ Tayangan</span>
                </div>
            </div>

            <!-- Total Likes -->
            <div class="bg-surface rounded-2xl p-4 border border-border shadow-xs flex flex-col justify-between hover:border-brand-blue/30 transition">
                <div class="flex items-center justify-between text-secondary">
                    <span class="text-xs font-semibold">Total Likes</span>
                    <span class="p-1.5 rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <h3 class="text-xl font-extrabold text-primary">{{ number_format($totalLikes) }}</h3>
                    <span class="text-[10px] text-rose-500 font-semibold">❤️ Suka</span>
                </div>
            </div>

            <!-- Total Comments -->
            <div class="bg-surface rounded-2xl p-4 border border-border shadow-xs flex flex-col justify-between hover:border-brand-blue/30 transition">
                <div class="flex items-center justify-between text-secondary">
                    <span class="text-xs font-semibold">Total Comments</span>
                    <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <h3 class="text-xl font-extrabold text-primary">{{ number_format($totalComments) }}</h3>
                    <span class="text-[10px] text-emerald-500 font-semibold">💬 Komentar</span>
                </div>
            </div>

            <!-- Total Shares -->
            <div class="bg-surface rounded-2xl p-4 border border-border shadow-xs flex flex-col justify-between hover:border-brand-blue/30 transition">
                <div class="flex items-center justify-between text-secondary">
                    <span class="text-xs font-semibold">Total Shares</span>
                    <span class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <h3 class="text-xl font-extrabold text-primary">{{ number_format($totalShares) }}</h3>
                    <span class="text-[10px] text-indigo-500 font-semibold">🔄 Bagikan</span>
                </div>
            </div>

            <!-- Total Saves -->
            <div class="bg-surface rounded-2xl p-4 border border-border shadow-xs flex flex-col justify-between hover:border-brand-blue/30 transition">
                <div class="flex items-center justify-between text-secondary">
                    <span class="text-xs font-semibold">Total Saves</span>
                    <span class="p-1.5 rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <h3 class="text-xl font-extrabold text-primary">{{ number_format($totalSaves) }}</h3>
                    <span class="text-[10px] text-amber-500 font-semibold">🔖 Simpan/Bookmark</span>
                </div>
            </div>

            <!-- Avg ER -->
            <div class="bg-surface rounded-2xl p-4 border border-border shadow-xs flex flex-col justify-between hover:border-brand-blue/30 transition">
                <div class="flex items-center justify-between text-secondary">
                    <span class="text-xs font-semibold">Rata ER (%)</span>
                    <span class="p-1.5 rounded-lg bg-cyan-50 text-cyan-600 dark:bg-cyan-900/30 dark:text-cyan-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <h3 class="text-xl font-extrabold text-primary">{{ number_format($avgER, 2) }}%</h3>
                    <span class="text-[10px] text-cyan-500 font-semibold">📈 Engagement Rate</span>
                </div>
            </div>

            <!-- Avg SAW Score -->
            <div class="bg-surface rounded-2xl p-4 border border-border shadow-xs flex flex-col justify-between hover:border-brand-blue/30 transition">
                <div class="flex items-center justify-between text-secondary">
                    <span class="text-xs font-semibold">Rata Skor SAW</span>
                    <span class="p-1.5 rounded-lg bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <h3 class="text-xl font-extrabold text-purple-600 dark:text-purple-400">{{ number_format($avgSawScore, 4) }}</h3>
                    <span class="text-[10px] text-purple-500 font-semibold">⭐ Bobot SAW</span>
                </div>
            </div>
        </div>

        <!-- Individual Metric Charts Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Views per Platform -->
            <div class="bg-surface p-6 rounded-2xl border border-border shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-md font-bold text-primary mb-1">Views per Platform</h3>
                    <p class="text-xs text-secondary mb-4">Total views untuk setiap platform</p>
                </div>
                <div class="relative h-48 w-full flex items-center justify-center">
                    <canvas id="viewsChart"></canvas>
                </div>
            </div>

            <!-- Likes per Platform -->
            <div class="bg-surface p-6 rounded-2xl border border-border shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-md font-bold text-primary mb-1">Likes per Platform</h3>
                    <p class="text-xs text-secondary mb-4">Total likes untuk setiap platform</p>
                </div>
                <div class="relative h-48 w-full flex items-center justify-center">
                    <canvas id="likesChart"></canvas>
                </div>
            </div>

            <!-- Comments per Platform -->
            <div class="bg-surface p-6 rounded-2xl border border-border shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-md font-bold text-primary mb-1">Comments per Platform</h3>
                    <p class="text-xs text-secondary mb-4">Total comments untuk setiap platform</p>
                </div>
                <div class="relative h-48 w-full flex items-center justify-center">
                    <canvas id="commentsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Content Ranking Section -->
        <div class="bg-surface rounded-2xl border border-border shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border flex justify-between items-center bg-body/25">
                <div>
                    <h3 class="text-lg font-bold text-primary">Top 5 Content Ranking 🏆</h3>
                    <p class="text-xs text-secondary mt-1">Konten teratas berdasarkan preferensi skor SAW</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-body/50 text-xs uppercase text-secondary">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Platform</th>
                            <th class="px-6 py-4 font-semibold">Campaign</th>
                            <th class="px-6 py-4 font-semibold">Akun</th>
                            <th class="px-6 py-4 font-semibold text-right">Views</th>
                            <th class="px-6 py-4 font-semibold text-right">Likes</th>
                            <th class="px-6 py-4 font-semibold text-right">Comments</th>
                            <th class="px-6 py-4 font-semibold text-right">ER (%)</th>
                            <th class="px-6 py-4 font-semibold text-right">SAW Score</th>
                            <th class="px-6 py-4 font-semibold text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($topContent as $content)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 bg-body border border-border rounded-lg text-xs font-semibold">
                                    {{ ucfirst($content->platform) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-primary">{{ $content->campaign->nama_campaign ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-secondary font-bold">@​{{ $content->username ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-right font-medium text-primary">{{ number_format($content->views) }}</td>
                            <td class="px-6 py-4 text-sm text-right text-secondary">{{ number_format($content->likes) }}</td>
                            <td class="px-6 py-4 text-sm text-right text-secondary">{{ number_format($content->comments) }}</td>
                            <td class="px-6 py-4 text-sm text-right font-bold text-status-success">{{ number_format($content->engagement_rate, 2) }}%</td>
                            <td class="px-6 py-4 text-sm text-right font-black text-brand-blue">{{ number_format($content->saw_score, 4) }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('laporan.show', $content->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-brand-blue/10 text-brand-blue hover:bg-brand-blue/20 rounded-xl text-xs font-bold transition-colors">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-secondary">Belum ada data ranking konten.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Export & Data Table Section -->
        <div class="bg-surface rounded-2xl border border-border overflow-hidden shadow-sm mt-6">
            <div class="px-6 py-5 border-b border-border flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-body/25">
                <h3 class="text-lg font-bold text-primary">Daftar Link Konten (Laporan)</h3>
                <div class="flex gap-2">
                    <a href="{{ route('export.client.pdf') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-status-danger/10 text-status-danger border border-transparent rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-status-danger/20 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export PDF
                    </a>
                    <a href="{{ route('export.client.excel') }}" class="inline-flex items-center px-4 py-2 bg-status-success/10 text-status-success border border-transparent rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-status-success/20 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Excel
                    </a>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-body/50 text-xs uppercase text-secondary">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Platform</th>
                            <th class="px-6 py-4 font-semibold">Campaign</th>
                            <th class="px-6 py-4 font-semibold text-right">Views</th>
                            <th class="px-6 py-4 font-semibold text-right">ER (%)</th>
                            <th class="px-6 py-4 font-semibold text-center">Status</th>
                            <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($links as $link)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 bg-body border border-border rounded-md text-xs font-semibold">{{ ucfirst($link->platform) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-primary">{{ $link->campaign->nama_campaign ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-right font-medium text-primary">{{ number_format($link->views) }}</td>
                            <td class="px-6 py-4 text-sm text-right font-bold {{ $link->engagement_rate > 5 ? 'text-status-success' : 'text-status-warning' }}">{{ number_format($link->engagement_rate, 2) }}%</td>
                            <td class="px-6 py-4 text-center">
                                @if(in_array($link->status_scraping, ['Completed', 'Berhasil']))
                                    <span class="inline-flex px-2 py-1 rounded bg-status-success/10 text-status-success text-xs font-bold">Selesai</span>
                                @elseif($link->status_scraping === 'Pending')
                                    <span class="inline-flex px-2 py-1 rounded bg-status-warning/10 text-status-warning text-xs font-bold">Antrean</span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded bg-status-danger/10 text-status-danger text-xs font-bold">Gagal</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('laporan.show', $link->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-brand-blue/10 text-brand-blue hover:bg-brand-blue/20 rounded-xl text-xs font-bold transition-colors">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-secondary">Belum ada data link.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isDarkMode = document.documentElement.classList.contains('dark') || document.documentElement.getAttribute('data-theme') === 'dark';
            const textColor = isDarkMode ? '#94a3b8' : '#475569';
            const gridColor = isDarkMode ? '#334155' : '#f1f5f9';

            // Common doughnut chart generator
            function createDoughnut(canvasId, titleLabel, chartData) {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return;

                const platforms = Object.keys(chartData);
                const values = Object.values(chartData);

                if (platforms.length === 0) {
                    platforms.push('Tidak Ada Data');
                    values.push(1);
                }

                new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: platforms.map(p => p.toUpperCase()),
                        datasets: [{
                            label: titleLabel,
                            data: values,
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.85)',
                                'rgba(236, 72, 153, 0.85)',
                                'rgba(168, 85, 247, 0.85)',
                                'rgba(16, 185, 129, 0.85)'
                            ],
                            borderColor: isDarkMode ? '#1e293b' : '#ffffff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: textColor,
                                    font: { family: "'Outfit', 'Inter', sans-serif", size: 11 }
                                }
                            },
                            tooltip: {
                                backgroundColor: isDarkMode ? '#1e293b' : '#ffffff',
                                titleColor: isDarkMode ? '#f8fafc' : '#0f172a',
                                bodyColor: isDarkMode ? '#94a3b8' : '#475569',
                                borderColor: isDarkMode ? '#334155' : '#e2e8f0',
                                borderWidth: 1
                            }
                        },
                        cutout: '70%'
                    }
                });
            }

            createDoughnut('viewsChart', 'Views', {!! json_encode($platformViews) !!});
            createDoughnut('likesChart', 'Likes', {!! json_encode($platformLikes) !!});
            createDoughnut('commentsChart', 'Comments', {!! json_encode($platformComments) !!});
        });
    </script>
</x-app-layout>
