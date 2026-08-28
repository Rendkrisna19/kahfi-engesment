<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Kahfi Engagement') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Theme Initialization Script -->
        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
                document.documentElement.classList.remove('dark');
            }
        </script>

        <!-- Scripts & NProgress CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
        <style>
            #nprogress .bar {
                background: #2563eb !important;
                height: 3px !important;
                z-index: 99999 !important;
            }
            #nprogress .peg {
                box-shadow: 0 0 10px #2563eb, 0 0 5px #2563eb !important;
            }
            #nprogress .spinner {
                z-index: 99999 !important;
                top: 15px !important;
                right: 15px !important;
            }
            #nprogress .spinner-icon {
                border-top-color: #2563eb !important;
                border-left-color: #2563eb !important;
            }
        </style>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-surface-body text-primary transition-colors duration-200" x-data="{ sidebarOpen: false }">
        
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" style="display: none;" class="fixed inset-0 z-30 bg-gray-900/50 dark:bg-gray-900/80 sm:hidden" @click="sidebarOpen = false" x-transition.opacity></div>
        
        @include('layouts.sidebar')

        <div class="sm:ml-64 min-h-screen flex flex-col">
            <!-- Topbar -->
            <nav class="bg-surface border-b border-border px-4 py-3 sm:px-6 flex items-center justify-between sticky top-0 z-30 shadow-sm transition-colors duration-200">
                <div class="flex-1 flex items-center pr-4">
                    <button @click="sidebarOpen = true" class="p-2 mr-3 text-secondary rounded-lg sm:hidden hover:text-primary hover:bg-gray-100 dark:hover:bg-gray-800 focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    @isset($header)
                        {{ $header }}
                    @endisset
                </div>
                
                <div class="flex items-center gap-3 shrink-0">
                    @can('operasional-konten.view')
                    <!-- Apify Quota & Balance Status Badge -->
                    <div x-data="{
                        open: false,
                        loading: false,
                        status: null,
                        async fetchApifyStatus() {
                            this.loading = true;
                            try {
                                const res = await fetch('{{ route('apify.status') }}');
                                const data = await res.json();
                                this.status = data;
                            } catch (e) {
                                console.error('Apify status fetch error:', e);
                            } finally {
                                this.loading = false;
                            }
                        }
                    }" x-init="fetchApifyStatus()" class="relative">
                        <!-- Trigger Badge Button -->
                        <button type="button" @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-xl bg-body hover:bg-gray-100 dark:hover:bg-gray-800 text-primary border border-border transition shadow-2xs group" title="Klik untuk lihat rincian saldo Apify">
                            <div class="w-2 h-2 rounded-full shrink-0" :class="{
                                'bg-emerald-500 animate-pulse': status && status.configured && status.percentage_used < 85,
                                'bg-amber-500 animate-pulse': status && status.configured && status.percentage_used >= 85 && status.percentage_used < 95,
                                'bg-rose-500': status && status.configured && status.percentage_used >= 95,
                                'bg-gray-400': !status || !status.configured
                            }"></div>
                            
                            <svg class="w-4 h-4 text-brand-blue group-hover:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            
                            <span class="hidden sm:inline font-mono">
                                <template x-if="loading">
                                    <span class="text-secondary text-[11px]">Cek Saldo...</span>
                                </template>
                                <template x-if="!loading && status && status.configured">
                                    <span>
                                        <span class="text-secondary font-sans" x-text="status.plan_name + ':'"></span> 
                                        $<span class="text-emerald-600 dark:text-emerald-400 font-bold" x-text="status.remaining_usd"></span>
                                    </span>
                                </template>
                                <template x-if="!loading && (!status || !status.configured)">
                                    <span class="text-amber-500">Apify Off</span>
                                </template>
                            </span>

                            <svg class="w-3.5 h-3.5 text-secondary transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Dropdown Modal Popup -->
                        <div x-show="open" @click.outside="open = false" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                            style="display: none;"
                            class="absolute right-0 mt-2 w-72 p-4 bg-surface border border-border rounded-2xl shadow-xl z-50 text-xs">
                            
                            <div class="flex items-center justify-between pb-3 border-b border-border">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-brand-blue/10 text-brand-blue flex items-center justify-center font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-primary text-xs">Status Saldo Apify</h4>
                                        <p class="text-[10px] text-secondary" x-text="status && status.username ? '@' + status.username : 'Apify Scraper'"></p>
                                    </div>
                                </div>
                                <button type="button" @click="fetchApifyStatus()" :disabled="loading" class="p-1 text-secondary hover:text-primary rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition" title="Refresh Saldo">
                                    <svg class="w-3.5 h-3.5" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                </button>
                            </div>

                            <template x-if="status && status.configured">
                                <div class="mt-3 space-y-3">
                                    <!-- Plan Badge -->
                                    <div class="flex items-center justify-between p-2 bg-body rounded-xl border border-border">
                                        <span class="text-secondary font-medium">Paket Langganan:</span>
                                        <span class="font-bold px-2 py-0.5 rounded-lg bg-brand-blue/15 text-brand-blue text-[11px]" x-text="status.plan_name"></span>
                                    </div>

                                    <!-- Usage Details Grid -->
                                    <div class="grid grid-cols-2 gap-2 text-center">
                                        <div class="p-2.5 bg-body rounded-xl border border-border">
                                            <span class="text-[10px] text-secondary block">Terpakai Bulan Ini</span>
                                            <span class="font-black text-primary text-sm font-mono" x-text="'$' + status.usage_usd"></span>
                                        </div>
                                        <div class="p-2.5 bg-body rounded-xl border border-border">
                                            <span class="text-[10px] text-secondary block">Sisa Limit Kuota</span>
                                            <span class="font-black text-emerald-600 dark:text-emerald-400 text-sm font-mono" x-text="'$' + status.remaining_usd"></span>
                                        </div>
                                    </div>

                                    <!-- Progress Bar Limit -->
                                    <div>
                                        <div class="flex justify-between text-[10px] text-secondary mb-1">
                                            <span>Limit Penggunaan ($<span x-text="status.limit_usd"></span>/bln)</span>
                                            <span class="font-bold text-primary" x-text="status.percentage_used + '%'"></span>
                                        </div>
                                        <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full transition-all duration-500 rounded-full" 
                                                :style="'width: ' + status.percentage_used + '%'"
                                                :class="{
                                                    'bg-emerald-500': status.percentage_used < 85,
                                                    'bg-amber-500': status.percentage_used >= 85 && status.percentage_used < 95,
                                                    'bg-rose-500': status.percentage_used >= 95
                                                }">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!status || !status.configured">
                                <div class="mt-3 p-3 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-xl border border-amber-500/20 text-center">
                                    <p class="font-bold text-xs">Apify Belum Terhubung</p>
                                    <p class="text-[10px] mt-1" x-text="status ? status.message : 'Periksa APIFY_TOKEN di file .env'"></p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Refresh Data (SAW) Topbar Button -->
                    <form method="POST" action="{{ route('operasional-konten.refresh') }}" class="inline" onsubmit="this.querySelector('svg').classList.add('animate-spin')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-xl bg-body hover:bg-gray-100 dark:hover:bg-gray-800 text-primary border border-border transition shadow-2xs" title="Refresh & Hitung Ulang SAW Data">
                            <svg class="w-4 h-4 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <span class="hidden sm:inline">Refresh Data (SAW)</span>
                        </button>
                    </form>
                    @endcan

                    <x-theme-toggle />
                    
                    @can('profile.edit')
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 border-l border-border pl-4 hover:opacity-85 transition-opacity">
                    @else
                    <div class="flex items-center gap-3 border-l border-border pl-4">
                    @endcan
                        @if(Auth::user()->photo)
                            <img src="{{ asset(Auth::user()->photo) }}" alt="User" class="w-8 h-8 rounded-full shadow-sm object-cover">
                        @else
                            <div class="w-8 h-8 rounded-full bg-brand-gradient flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="hidden md:block">
                            <p class="text-sm font-semibold">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-secondary">{{ Auth::user()->role }}</p>
                        </div>
                    @can('profile.edit')
                    </a>
                    @else
                    </div>
                    @endcan
                </div>
            </nav>

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>

        <!-- SweetAlert2 & NProgress JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Configure NProgress
            NProgress.configure({ showSpinner: true, speed: 400, minimum: 0.1 });

            window.addEventListener('beforeunload', function () {
                NProgress.start();
            });

            document.addEventListener('DOMContentLoaded', function () {
                NProgress.done();
            });

            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function () {
                    NProgress.start();
                });
            });

            // SweetAlert2 Toast Global
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    title: "{{ session('error') }}"
                });
            @endif

            @if(session('warning'))
                Toast.fire({
                    icon: 'warning',
                    title: "{{ session('warning') }}"
                });
            @endif
        </script>
    </body>
</html>
