<x-app-layout>
    <x-slot name="title">Pilih Konten & Re-Scrape SAW - {{ $campaign->nama_campaign }}</x-slot>

    @php
        $now = \Carbon\Carbon::now()->startOfDay();
        $deadline = $campaign->tanggal_selesai ? \Carbon\Carbon::parse($campaign->tanggal_selesai)->startOfDay() : null;
        $daysRemaining = $deadline ? (int) $now->diffInDays($deadline, false) : null;
    @endphp

    <div class="space-y-6" x-data="{ selectAll: false, selected: [] }">
        <!-- Back Navigation & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface p-6 rounded-2xl border border-border shadow-xs">
            <div class="flex items-center gap-4">
                <a href="{{ route('update-saw.index') }}" class="p-2 rounded-xl border border-border hover:bg-body transition-colors text-secondary hover:text-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-primary tracking-tight flex items-center gap-2.5 flex-wrap">
                        <span>Campaign: {{ $campaign->nama_campaign }}</span>
                        @if($daysRemaining !== null)
                            @if($daysRemaining < 0)
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                    🔴 Expired ({{ \Carbon\Carbon::parse($campaign->tanggal_selesai)->format('d M Y') }})
                                </span>
                            @elseif($daysRemaining == 0)
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                    ⚠️ Berakhir Hari Ini
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                    ⏳ Sisa {{ $daysRemaining }} Hari
                                </span>
                            @endif
                        @endif
                    </h2>
                    <p class="text-xs text-secondary mt-0.5">Centang link konten yang ingin di-scrape ulang untuk memperbarui Views, Engagement Rate, dan Skor SAW secara langsung.</p>
                </div>
            </div>

            <!-- Form Action Button Header -->
            <div class="flex items-center gap-3">
                <button type="button" @click="selectAll = !selectAll; $dispatch('toggle-all', selectAll)" class="px-3 py-2 text-xs font-semibold rounded-xl border border-border bg-body text-primary hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <span x-text="selectAll ? 'Batal Pilih Semua' : 'Pilih Semua Link'"></span>
                </button>
            </div>
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

        <!-- Main Form for Bulk Re-Scrape -->
        <form id="rescrapeForm" method="POST" action="{{ route('update-saw.process', $campaign->id) }}">
            @csrf
            
            <div class="bg-surface rounded-2xl border border-border overflow-hidden shadow-sm">
                <!-- Filter Bar & Submission Action -->
                <div class="p-6 border-b border-border flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-primary">Tabel Konten Campaign</span>
                        <span class="text-xs bg-brand-blue/10 text-brand-blue font-semibold px-2.5 py-0.5 rounded-full">
                            Total: {{ $links->total() }} Link
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Submit Re-Scrape Button -->
                        <button type="button" onclick="confirmRescrape()" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl bg-brand-blue hover:bg-brand-blue-hover text-white transition shadow-md shadow-brand-blue/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Jalankan Re-Scraping & Update SAW
                        </button>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-primary">
                        <thead class="text-xs text-secondary uppercase bg-body/50 border-b border-border">
                            <tr>
                                <th scope="col" class="p-4 w-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" x-model="selectAll" @change="$dispatch('toggle-all', selectAll)" class="w-4 h-4 text-brand-blue bg-body border-border rounded focus:ring-brand-blue">
                                    </div>
                                </th>
                                <th scope="col" class="px-4 py-3.5 font-semibold">Platform & Akun</th>
                                <th scope="col" class="px-4 py-3.5 font-semibold text-right">Views</th>
                                <th scope="col" class="px-4 py-3.5 font-semibold text-right">Likes</th>
                                <th scope="col" class="px-4 py-3.5 font-semibold text-right">Comments</th>
                                <th scope="col" class="px-4 py-3.5 font-semibold text-right">ER (%)</th>
                                <th scope="col" class="px-4 py-3.5 font-semibold text-center">Skor SAW (Baru vs Lama)</th>
                                <th scope="col" class="px-4 py-3.5 font-semibold text-center">Status / Re-Scraped</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border" x-data="{ checkAll: false }" @toggle-all.window="checkAll = $event.detail">
                            @forelse($links as $link)
                            @php
                                $viewsDiff = ($link->prev_views !== null) ? ($link->views - $link->prev_views) : 0;
                                $erDiff = ($link->prev_engagement_rate !== null) ? ($link->engagement_rate - $link->prev_engagement_rate) : 0;
                                $sawDiff = ($link->prev_saw_score !== null) ? ($link->saw_score - $link->prev_saw_score) : 0;
                                
                                $sawPercent = 0;
                                if ($link->prev_saw_score && $link->prev_saw_score > 0) {
                                    $sawPercent = (($link->saw_score - $link->prev_saw_score) / $link->prev_saw_score) * 100;
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <!-- Checkbox Column -->
                                <td class="p-4 w-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="link_ids[]" value="{{ $link->id }}" :checked="checkAll" class="w-4 h-4 text-brand-blue bg-body border-border rounded focus:ring-brand-blue">
                                    </div>
                                </td>

                                <!-- Platform & Akun -->
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-2.5">
                                        @if(strtolower($link->platform) === 'tiktok')
                                            <div class="w-7 h-7 rounded-lg bg-black text-white flex items-center justify-center shrink-0 shadow-xs">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                                            </div>
                                        @else
                                            <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="font-bold text-xs text-primary truncate max-w-[140px]" title="{{ $link->username }}">{{ $link->username ?? 'Sedang Scraping...' }}</p>
                                            <a href="{{ $link->url }}" target="_blank" class="text-[11px] text-brand-blue hover:underline truncate inline-block max-w-[140px]">{{ $link->url }}</a>
                                        </div>
                                    </div>
                                </td>

                                <!-- Views -->
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <span class="font-semibold text-xs text-primary block">
                                        {{ number_format($link->views) }}
                                    </span>
                                    @if($viewsDiff > 0)
                                        <span class="text-[10px] text-status-success font-bold inline-flex items-center">
                                            ▲ +{{ number_format($viewsDiff) }}
                                        </span>
                                    @elseif($viewsDiff < 0)
                                        <span class="text-[10px] text-status-danger font-medium inline-flex items-center">
                                            ▼ {{ number_format($viewsDiff) }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Likes -->
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <span class="font-medium text-xs text-primary block">
                                        {{ number_format($link->likes) }}
                                    </span>
                                </td>

                                <!-- Comments -->
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <span class="font-medium text-xs text-primary block">
                                        {{ number_format($link->comments) }}
                                    </span>
                                </td>

                                <!-- ER (%) -->
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <span class="font-semibold text-xs text-brand-blue block">
                                        {{ number_format($link->engagement_rate, 2) }}%
                                    </span>
                                    @if($erDiff > 0)
                                        <span class="text-[10px] text-status-success font-bold">
                                            ▲ +{{ number_format($erDiff, 2) }}%
                                        </span>
                                    @endif
                                </td>

                                <!-- Skor SAW (Comparison) -->
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="flex flex-col items-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-extrabold bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                            {{ number_format($link->saw_score, 4) }}
                                        </span>

                                        @if($sawDiff > 0)
                                            <!-- Green Indicator for Increased SAW Score -->
                                            <div class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold border border-emerald-200 dark:border-emerald-800">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                                <span>Naik +{{ number_format($sawDiff, 4) }} ({{ number_format($sawPercent, 1) }}%)</span>
                                            </div>
                                        @elseif($sawDiff < 0)
                                            <div class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-300 text-[10px] font-medium">
                                                <span>Turun {{ number_format($sawDiff, 4) }}</span>
                                            </div>
                                        @elseif($link->prev_saw_score !== null)
                                            <span class="text-[10px] text-secondary mt-0.5">Stabil (Tidak Berubah)</span>
                                        @else
                                            <span class="text-[10px] text-secondary mt-0.5">Data Perdana</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Status & Last Rescraped -->
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-status-success/10 text-status-success mb-0.5">
                                        Completed
                                    </span>
                                    <span class="text-[10px] text-secondary block">
                                        {{ $link->last_rescraped_at ? \Carbon\Carbon::parse($link->last_rescraped_at)->diffForHumans() : 'Awal' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-secondary">
                                    Tidak ada link konten dalam Campaign ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-border bg-body/30">
                    {{ $links->links() }}
                </div>
            </div>
        </form>
    </div>

    <!-- Confirm Re-Scrape Script using SweetAlert2 -->
    <script>
    function confirmRescrape() {
        const checkedCount = document.querySelectorAll('input[name="link_ids[]"]:checked').length;
        const selectAllCheckbox = document.querySelector('input[x-model="selectAll"]');
        const isAllSelected = selectAllCheckbox && selectAllCheckbox.checked;

        if (checkedCount === 0 && !isAllSelected) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Konten Terlebih Dahulu!',
                text: 'Silakan centang minimal satu link konten yang ingin Anda scrape ulang.',
                confirmButtonColor: '#2563eb',
                customClass: {
                    popup: 'rounded-2xl border border-border bg-surface text-primary shadow-xl',
                    confirmButton: 'rounded-xl px-4 py-2 text-xs font-bold'
                }
            });
            return;
        }

        const countText = isAllSelected ? 'semua' : checkedCount;

        Swal.fire({
            title: 'Jalankan Re-Scraping Apify?',
            text: `Metrik ${countText} link terpilih akan diperbarui. Data lama tersimpan untuk indikator komparasi skor SAW.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            confirmButtonText: '⚡ Ya, Scrape Ulang!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl border border-border bg-surface text-primary shadow-2xl',
                confirmButton: 'rounded-xl px-4 py-2 text-xs font-bold',
                cancelButton: 'rounded-xl px-4 py-2 text-xs font-semibold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Sedang Memproses Re-Scraping...',
                    text: 'Mohon tunggu, Apify sedang menarik data terbaru...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                document.getElementById('rescrapeForm').submit();
            }
        });
    }
    </script>
</x-app-layout>
