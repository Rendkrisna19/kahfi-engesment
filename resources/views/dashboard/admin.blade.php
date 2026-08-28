<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-primary leading-tight flex items-center gap-2">
            <span>Halo {{ explode(' ', Auth::user()->name)[0] }}, Selamat Datang Kembali!</span>
            <svg class="w-6 h-6 text-amber-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5a1.5 1.5 0 113 0m-3 0V11m3-5.5a1.5 1.5 0 113 0m-3 0V11"></path></svg>
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Interactive Real-time Filters -->
        <div class="bg-surface p-6 rounded-2xl border border-border shadow-sm flex flex-wrap gap-4 items-center justify-between">
            <form method="GET" action="{{ route('dashboard.admin') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
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
                        <option value="">-- Semua Campaign --</option>
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
                    <a href="{{ route('dashboard.admin') }}" class="text-xs text-brand-blue hover:underline font-semibold">Reset Filter</a>
                @endif
            </form>
        </div>

        <!-- Main Highlight & Compact KPIs Grid (8 Cards) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 gap-4">
            <!-- Campaign & Links -->
            <div class="bg-brand-gradient rounded-2xl p-4 text-white shadow-md flex flex-col justify-between hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-white/80 font-medium">Campaign Aktif</span>
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
                    <span class="inline-flex items-center gap-1 text-[10px] text-status-success font-semibold">
                        <svg class="w-3 h-3 text-status-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <span>Tayangan</span>
                    </span>
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
                    <span class="inline-flex items-center gap-1 text-[10px] text-rose-500 font-semibold">
                        <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        <span>Suka</span>
                    </span>
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
                    <span class="inline-flex items-center gap-1 text-[10px] text-emerald-500 font-semibold">
                        <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <span>Komentar</span>
                    </span>
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
                    <span class="inline-flex items-center gap-1 text-[10px] text-indigo-500 font-semibold">
                        <svg class="w-3 h-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                        <span>Bagikan</span>
                    </span>
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
                    <span class="inline-flex items-center gap-1 text-[10px] text-amber-500 font-semibold">
                        <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                        <span>Simpan/Bookmark</span>
                    </span>
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
                    <span class="inline-flex items-center gap-1 text-[10px] text-cyan-500 font-semibold">
                        <svg class="w-3 h-3 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        <span>Engagement Rate</span>
                    </span>
                </div>
            </div>

            <!-- Total Views Increase -->
            <div class="bg-surface rounded-2xl p-4 border border-border shadow-xs flex flex-col justify-between hover:border-brand-blue/30 transition">
                <div class="flex items-center justify-between text-secondary">
                    <span class="text-xs font-semibold">Kenaikan Views</span>
                    <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <h3 class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400">+{{ number_format($totalViewsIncrease) }}</h3>
                    <span class="inline-flex items-center gap-1 text-[10px] text-emerald-500 font-semibold">
                        <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <span>Pasca Update</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Grafik Update Kenaikan Views (Line Chart) -->
        <div class="bg-surface p-6 rounded-2xl border border-border shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h3 class="text-lg font-bold text-primary flex items-center gap-2">
                        <span>Grafik Update Kenaikan Views</span>
                        <svg class="w-5 h-5 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                    </h3>
                    <p class="text-xs text-secondary">Tren pertumbuhan akumulasi tayangan dan kenaikan views pasca update data</p>
                </div>
                <span class="text-xs font-bold px-3 py-1 bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-xl border border-emerald-200 dark:border-emerald-800 self-start sm:self-auto">
                    +{{ number_format($totalViewsIncrease) }} Views Baru
                </span>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="viewsGrowthLineChart"></canvas>
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

        <!-- Top Content Ranking Section (Top 20 with show 5 initial) -->
        <div class="bg-surface rounded-2xl border border-border shadow-sm overflow-hidden" x-data="{ showAllTop: false }">
            <div class="p-6 border-b border-border flex justify-between items-center bg-body/25">
                <div>
                    <h3 class="text-lg font-bold text-primary flex items-center gap-2">
                        <span>Top Content Ranking</span>
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </h3>
                    <p class="text-xs text-secondary mt-1">Konten teratas berdasarkan total tayangan (Views)</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-brand-blue/10 text-brand-blue rounded-lg">
                    Menampilkan <span x-text="showAllTop ? '{{ $topContent->count() }}' : '{{ min(5, $topContent->count()) }}'"></span> dari {{ $topContent->count() }} Konten
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-body/50 text-xs uppercase text-secondary">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Rank</th>
                            <th class="px-6 py-4 font-semibold">Platform</th>
                            <th class="px-6 py-4 font-semibold">Campaign</th>
                            <th class="px-6 py-4 font-semibold">Akun</th>
                            <th class="px-6 py-4 font-semibold text-right">Views</th>
                            <th class="px-6 py-4 font-semibold text-right">Likes</th>
                            <th class="px-6 py-4 font-semibold text-right">Comments</th>
                            <th class="px-6 py-4 font-semibold text-right">ER (%)</th>
                            <th class="px-6 py-4 font-semibold text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($topContent as $index => $content)
                        <tr x-show="showAllTop || {{ $index }} < 5" class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="w-6 h-6 rounded-full inline-flex items-center justify-center text-xs font-extrabold {{ $index == 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-300' : ($index == 1 ? 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 border border-gray-300' : ($index == 2 ? 'bg-amber-800/10 text-amber-900 dark:bg-amber-900/20 dark:text-amber-400 border border-amber-800/30' : 'bg-body text-secondary')) }}">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 bg-body border border-border rounded-lg text-xs font-semibold">
                                    {{ ucfirst($content->platform) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-primary">{{ $content->campaign->nama_campaign ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-secondary font-bold">@​{{ $content->username ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-right font-bold text-brand-blue">{{ number_format($content->views) }}</td>
                            <td class="px-6 py-4 text-sm text-right text-secondary">{{ number_format($content->likes) }}</td>
                            <td class="px-6 py-4 text-sm text-right text-secondary">{{ number_format($content->comments) }}</td>
                            <td class="px-6 py-4 text-sm text-right font-bold text-status-success">{{ number_format($content->engagement_rate, 2) }}%</td>
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

            @if($topContent->count() > 5)
            <div class="p-4 text-center border-t border-border bg-body/20">
                <button @click="showAllTop = !showAllTop" class="inline-flex items-center gap-2 text-xs font-bold text-brand-blue hover:text-brand-blue-hover transition-colors px-4 py-2 rounded-xl bg-brand-blue/10 hover:bg-brand-blue/20">
                    <span x-text="showAllTop ? 'Tampilkan 5 Konten Ringkas' : 'Lihat Semua (Top {{ $topContent->count() }} Konten)'"></span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': showAllTop}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
            @endif
        </div>

        <!-- Charts Row (Campaign views list) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Bar Chart Area (Top 5 Campaigns) -->
            <div class="lg:col-span-2 bg-surface p-6 rounded-2xl border border-border">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-primary">Top 5 Campaign (Berdasarkan Views)</h3>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="campaignChart"></canvas>
                </div>
            </div>

            <!-- Detail Top Campaign -->
            <div class="lg:col-span-1 bg-surface p-6 rounded-2xl border border-border overflow-hidden">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-primary">Detail Top Campaign</h3>
                </div>
                
                <div class="space-y-4 max-h-[250px] overflow-y-auto pr-2">
                    @forelse($topCampaigns as $campaign)
                    <div class="flex justify-between items-center p-3 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl transition">
                        <div>
                            <p class="text-sm font-bold text-primary truncate max-w-[150px]">{{ $campaign->nama_campaign }}</p>
                            <p class="text-xs text-secondary mt-0.5">{{ $campaign->platform }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-black text-brand-blue">{{ number_format($campaign->links_sum_views ?? 0) }}</p>
                            <p class="text-xs text-secondary">Views</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-sm text-secondary py-8">
                        Belum ada data campaign.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isDarkMode = document.documentElement.classList.contains('dark') || document.documentElement.getAttribute('data-theme') === 'dark';
            const textColor = isDarkMode ? '#94a3b8' : '#475569';
            const gridColor = isDarkMode ? '#334155' : '#f1f5f9';

            // Views Growth Line Chart
            const growthCtx = document.getElementById('viewsGrowthLineChart');
            if (growthCtx) {
                const growthData = {!! json_encode($viewsTrendData) !!};
                const labels = growthData.map(item => item.date_label || 'Update');
                const totalViewsData = growthData.map(item => item.total_views);
                const increaseData = growthData.map(item => item.views_increase);

                new Chart(growthCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels.length > 0 ? labels : ['Belum Ada Data'],
                        datasets: [
                            {
                                label: 'Total akumulasi Views',
                                data: totalViewsData.length > 0 ? totalViewsData : [0],
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 4
                            },
                            {
                                label: 'Kenaikan Views (Pasca Update)',
                                data: increaseData.length > 0 ? increaseData : [0],
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.15)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: { color: textColor, font: { family: "'Montserrat', sans-serif", size: 11 } }
                            },
                            tooltip: {
                                backgroundColor: isDarkMode ? '#1e293b' : '#ffffff',
                                titleColor: isDarkMode ? '#f8fafc' : '#0f172a',
                                bodyColor: isDarkMode ? '#94a3b8' : '#475569',
                                borderColor: isDarkMode ? '#334155' : '#e2e8f0',
                                borderWidth: 1
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: gridColor, drawBorder: false },
                                ticks: { color: textColor }
                            },
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: { color: textColor }
                            }
                        }
                    }
                });
            }

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
                                    font: { family: "'Montserrat', sans-serif", size: 11 }
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

            // Campaign views bar chart
            const campaignCtx = document.getElementById('campaignChart');
            if (campaignCtx) {
                const labels = {!! json_encode($topCampaigns->pluck('nama_campaign')) !!};
                const data = {!! json_encode($topCampaigns->pluck('links_sum_views')) !!};

                new Chart(campaignCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels.length > 0 ? labels : ['Belum Ada Data'],
                        datasets: [{
                            label: 'Total Views',
                            data: data.length > 0 ? data : [0],
                            backgroundColor: 'rgba(37, 99, 235, 0.8)',
                            borderRadius: 6,
                            barThickness: 30
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: isDarkMode ? '#1e293b' : '#ffffff',
                                titleColor: isDarkMode ? '#f8fafc' : '#0f172a',
                                bodyColor: isDarkMode ? '#94a3b8' : '#475569',
                                borderColor: isDarkMode ? '#334155' : '#e2e8f0',
                                borderWidth: 1,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: gridColor, drawBorder: false },
                                ticks: { color: textColor }
                            },
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: {
                                    color: textColor,
                                    callback: function(value, index, values) {
                                        let label = this.getLabelForValue(value);
                                        return label.length > 10 ? label.substr(0, 10) + '...' : label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
