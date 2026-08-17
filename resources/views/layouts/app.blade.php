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
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
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
