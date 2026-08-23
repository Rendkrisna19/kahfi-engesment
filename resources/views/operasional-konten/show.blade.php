<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('operasional-konten.index') }}" class="text-secondary hover:text-primary transition-colors bg-surface border border-border p-2 rounded-lg hover:shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="font-semibold text-2xl text-primary leading-tight">
                    Detail Engagement Konten
                </h2>
                <p class="text-sm text-secondary mt-1">Laporan analitik dari link yang diproses.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Info Card -->
        <div class="bg-surface rounded-2xl border border-border overflow-hidden shadow-sm p-6 flex flex-col md:flex-row gap-6 md:items-center justify-between">
            <div class="flex items-center gap-4">
                @if(strtolower($link->platform) === 'tiktok')
                    <div class="w-16 h-16 rounded-2xl bg-black text-white flex items-center justify-center shrink-0 shadow-lg shadow-black/20">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                    </div>
                @else
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-purple-500/20">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                @endif
                <div>
                    <h3 class="text-xl font-bold text-primary">{{ $link->username ?? 'Akun Kreator' }}</h3>
                    <a href="{{ $link->url }}" target="_blank" class="text-sm text-brand-blue hover:underline break-all">{{ $link->url }}</a>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span class="bg-gray-100 dark:bg-gray-800 text-secondary text-xs px-2.5 py-1 rounded-md">{{ $link->campaign->nama_campaign ?? 'Default Campaign' }}</span>
                        <span class="bg-gray-100 dark:bg-gray-800 text-secondary text-xs px-2.5 py-1 rounded-md">{{ $link->kategoriKonten->nama ?? 'Kategori Konten' }}</span>
                        <span class="bg-gray-100 dark:bg-gray-800 text-secondary text-xs px-2.5 py-1 rounded-md">{{ $link->kategoriCreator->nama ?? 'Kategori Creator' }}</span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-secondary mb-1">Status Scraping</p>
                @if($link->status_scraping === 'Pending')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-status-warning/10 text-status-warning">
                        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Sedang Dalam Antrean (Pending)
                    </span>
                @elseif(in_array($link->status_scraping, ['Berhasil', 'Completed']))
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-status-success/10 text-status-success">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Selesai (Completed)
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-status-danger/10 text-status-danger">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Gagal Diproses
                    </span>
                @endif
            </div>
        </div>

        @if(in_array($link->status_scraping, ['Berhasil', 'Completed']) && ($link->post_type || $link->caption || $link->post_date))
            <div class="bg-surface rounded-2xl border border-border p-6 shadow-sm mb-6">
                <h3 class="text-sm font-bold text-primary mb-3">Detail Postingan:</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($link->post_type)
                    <div>
                        <p class="text-xs text-secondary">Tipe Konten</p>
                        <p class="text-sm font-medium text-primary">{{ $link->post_type }}</p>
                    </div>
                    @endif
                    @if($link->post_date)
                    <div>
                        <p class="text-xs text-secondary">Tanggal Posting</p>
                        <p class="text-sm font-medium text-primary">{{ \Carbon\Carbon::parse($link->post_date)->format('d M Y, H:i') }}</p>
                    </div>
                    @endif
                </div>
                @if($link->caption)
                <div class="mt-4">
                    <p class="text-xs text-secondary mb-1">Caption / Teks Postingan</p>
                    <div class="p-3 bg-body rounded-lg text-sm text-secondary italic break-words whitespace-pre-wrap max-h-32 overflow-y-auto">{{ $link->caption }}</div>
                </div>
                @endif
            </div>
        @endif

        @if(in_array($link->status_scraping, ['Berhasil', 'Completed']))
            <!-- Metrics Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Views -->
                <div class="bg-surface rounded-2xl border border-border p-6 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-brand-blue/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-secondary">Total Views</p>
                            <h4 class="text-2xl font-black text-primary mt-1">{{ number_format($link->views ?? 0, 0, ',', '.') }}</h4>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-brand-blue/10 text-brand-blue flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Likes -->
                <div class="bg-surface rounded-2xl border border-border p-6 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-pink-500/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-secondary">Total Likes</p>
                            <h4 class="text-2xl font-black text-primary mt-1">{{ number_format($link->likes ?? 0, 0, ',', '.') }}</h4>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-pink-500/10 text-pink-500 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Comments -->
                <div class="bg-surface rounded-2xl border border-border p-6 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-500/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-secondary">Total Comments</p>
                            <h4 class="text-2xl font-black text-primary mt-1">{{ number_format($link->comments ?? 0, 0, ',', '.') }}</h4>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Engagement Rate -->
                <div class="bg-surface rounded-2xl border border-border p-6 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-status-success/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-secondary">Engagement Rate</p>
                            <h4 class="text-2xl font-black text-primary mt-1">{{ number_format($link->engagement_rate ?? 0, 2) }}%</h4>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-status-success/10 text-status-success flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Metrics Grid: Shares, Saves, Reposts -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-surface rounded-xl border border-border p-4 flex items-center justify-between shadow-sm">
                    <span class="text-secondary text-sm font-medium">Total Shares</span>
                    <span class="text-primary font-bold text-lg">{{ number_format($link->shares ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="bg-surface rounded-xl border border-border p-4 flex items-center justify-between shadow-sm">
                    <span class="text-secondary text-sm font-medium">Total Saves / Bookmarks</span>
                    <span class="text-primary font-bold text-lg">{{ number_format($link->saves ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="bg-surface rounded-xl border border-border p-4 flex items-center justify-between shadow-sm">
                    <span class="text-secondary text-sm font-medium">Total Reposts</span>
                    <span class="text-primary font-bold text-lg">{{ number_format($link->reposts ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 gap-6">
                <!-- Bar Chart -->
                <div class="bg-surface rounded-2xl border border-border p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-primary mb-6">Distribusi Engagement</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="engagementChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Chart.js Script -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('engagementChart').getContext('2d');
                    const isDarkMode = document.documentElement.classList.contains('dark') || document.documentElement.getAttribute('data-theme') === 'dark';
                    const textColor = isDarkMode ? '#94a3b8' : '#475569';
                    const gridColor = isDarkMode ? '#334155' : '#f1f5f9';

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Likes', 'Comments', 'Shares', 'Saves'],
                            datasets: [{
                                label: 'Jumlah Interaksi',
                                data: [
                                    {{ (int) ($link->likes ?? 0) }},
                                    {{ (int) ($link->comments ?? 0) }},
                                    {{ (int) ($link->shares ?? 0) }},
                                    {{ (int) ($link->saves ?? 0) }}
                                ],
                                backgroundColor: [
                                    'rgba(236, 72, 153, 0.85)', // Pink (Likes)
                                    'rgba(168, 85, 247, 0.85)', // Purple (Comments)
                                    'rgba(59, 130, 246, 0.85)',  // Blue (Shares)
                                    'rgba(16, 185, 129, 0.85)'  // Green (Saves)
                                ],
                                borderRadius: 8,
                                barThickness: 32
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
                                    padding: 10,
                                    boxPadding: 4
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: gridColor, drawBorder: false },
                                    ticks: {
                                        color: textColor,
                                        font: { family: "'Montserrat', sans-serif" }
                                    }
                                },
                                x: {
                                    grid: { display: false, drawBorder: false },
                                    ticks: {
                                        color: textColor,
                                        font: { family: "'Montserrat', sans-serif" }
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
        @else
            <!-- If Pending / Failed -->
            <div class="bg-surface rounded-2xl border border-border p-12 shadow-sm text-center">
                @if($link->status_scraping === 'Pending')
                    <div class="w-20 h-20 bg-brand-blue/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-brand-blue animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-2">Data Belum Tersedia</h3>
                    <p class="text-secondary max-w-md mx-auto">Link ini sedang dalam antrean atau proses scraping. Silakan klik tombol <span class="font-semibold">Refresh Data (SAW)</span> di menu samping untuk memicu pengambilan data.</p>
                @else
                    <div class="w-20 h-20 bg-status-danger/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-status-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-status-danger mb-2">Scraping Gagal</h3>
                    <p class="text-secondary max-w-md mx-auto">Sistem tidak berhasil mengambil dataset dari link ini melalui Apify API. Pastikan URL akun/konten bersifat publik dan valid.</p>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>