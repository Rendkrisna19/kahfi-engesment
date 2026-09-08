@php
    $settings = $settings ?? (\Illuminate\Support\Facades\Schema::hasTable('landing_settings') ? \App\Models\LandingSetting::allKeyValues() : []);
    $clients = $clients ?? (\Illuminate\Support\Facades\Schema::hasTable('landing_items') ? \App\Models\LandingItem::ofType('client_logo')->active()->ordered()->get() : collect());
    $whyJoins = $whyJoins ?? (\Illuminate\Support\Facades\Schema::hasTable('landing_items') ? \App\Models\LandingItem::ofType('why_join')->active()->ordered()->get() : collect());
    $notFors = $notFors ?? (\Illuminate\Support\Facades\Schema::hasTable('landing_items') ? \App\Models\LandingItem::ofType('not_for')->active()->ordered()->get() : collect());
    $howSteps = $howSteps ?? (\Illuminate\Support\Facades\Schema::hasTable('landing_items') ? \App\Models\LandingItem::ofType('how_step')->active()->ordered()->get() : collect());
    $ctaChips = $ctaChips ?? (\Illuminate\Support\Facades\Schema::hasTable('landing_items') ? \App\Models\LandingItem::ofType('cta_chip')->active()->ordered()->get() : collect());
    $portfolios = $portfolios ?? (\Illuminate\Support\Facades\Schema::hasTable('landing_items') ? \App\Models\LandingItem::ofType('portfolio')->active()->ordered()->get() : collect());

    // Dynamic database statistics (from real links, creators, and campaigns)
    $totalViews = $totalViews ?? (\Illuminate\Support\Facades\Schema::hasTable('links') ? (\App\Models\Link::sum('views') ?: 0) : 0);
    $totalCreators = $totalCreators ?? (\Illuminate\Support\Facades\Schema::hasTable('links') ? (\App\Models\Link::whereNotNull('username')->where('username', '!=', '')->distinct('username')->count('username') ?: (\App\Models\User::where('role', 'Creator')->count() ?: 1)) : 1);
    $totalBrands = $totalBrands ?? (\Illuminate\Support\Facades\Schema::hasTable('campaigns') ? max(\App\Models\Campaign::count(), \App\Models\User::where('role', 'Client')->count(), 1) : 1);

    // Number formatting helper
    $formatMetric = function($num) {
        if ($num >= 1000000000) return round($num / 1000000000, 1) . 'B+';
        if ($num >= 1000000) return round($num / 1000000, 1) . 'M+';
        if ($num >= 1000) return round($num / 1000, 1) . 'K+';
        return number_format($num, 0, ',', '.') . '+';
    };

    $formattedViews = $formatMetric($totalViews);
    $formattedCreators = ($totalCreators >= 1000 ? round($totalCreators / 1000, 1) . 'K+' : $totalCreators . '+');
    $formattedBrands = ($totalBrands >= 1000 ? round($totalBrands / 1000, 1) . 'K+' : $totalBrands . '+');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $settings['brand_name'] ?? 'Kahfi Engagement' }} - {{ $settings['brand_tagline'] ?? 'Agency Engagement & Influencer Campaign Management' }}</title>
        
        <!-- Google Fonts: Plus Jakarta Sans for ultra-clean readability -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        
        <!-- Benzin Font Definition -->
        <style>
            @font-face {
                font-family: 'Benzin';
                src: url('/fonts/Benzin-Bold.woff2') format('woff2'),
                     url('https://db.onlinewebfonts.com/t/e37c5bb24acd803973c692c8c59a6230.woff2') format('woff2');
                font-weight: 700;
                font-style: normal;
                font-display: swap;
            }
            @font-face {
                font-family: 'Benzin';
                src: url('/fonts/Benzin-Bold.woff2') format('woff2'),
                     url('https://db.onlinewebfonts.com/t/e37c5bb24acd803973c692c8c59a6230.woff2') format('woff2');
                font-weight: 800;
                font-style: normal;
                font-display: swap;
            }
            @font-face {
                font-family: 'Benzin';
                src: url('/fonts/Benzin-Bold.woff2') format('woff2'),
                     url('https://db.onlinewebfonts.com/t/e37c5bb24acd803973c692c8c59a6230.woff2') format('woff2');
                font-weight: 900;
                font-style: normal;
                font-display: swap;
            }

            body {
                font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }
            .font-benzin {
                font-family: 'Benzin', 'Plus Jakarta Sans', system-ui, sans-serif;
                letter-spacing: -0.02em;
            }
            @keyframes marquee {
                0% { transform: translateX(0%); }
                100% { transform: translateX(-50%); }
            }
            .animate-marquee {
                display: flex;
                width: max-content;
                animation: marquee 30s linear infinite;
            }
            .animate-marquee:hover {
                animation-play-state: paused;
            }
        </style>

        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                            benzin: ['Benzin', '"Plus Jakarta Sans"', 'sans-serif'],
                        },
                        colors: {
                            brand: {
                                blue: '#1d4ed8',
                                blueHover: '#1e40af',
                                dark: '#0f172a',
                            }
                        }
                    }
                }
            }
        </script>

        <!-- Alpine JS CDN -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Vite Assets (Optional) -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        @php
            $brandLogoUrl = \App\Models\LandingSetting::brandLogoUrl();
        @endphp
        @if($brandLogoUrl)
            <link rel="icon" type="image/png" href="{{ $brandLogoUrl }}">
        @endif
    </head>
    <body class="bg-[#F8FAFC] text-slate-900 antialiased min-h-screen relative overflow-x-hidden selection:bg-blue-600 selection:text-white">

        <!-- ================= NAVBAR ================= -->
        <header x-data="{ mobileMenu: false }" class="sticky top-0 z-50 w-full bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm transition-all duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <!-- Brand Logo & Name -->
                <a href="/" class="flex items-center gap-3 group">
                    @if($brandLogoUrl)
                        <img src="{{ $brandLogoUrl }}" alt="{{ $settings['brand_name'] ?? 'Kahfi Engagement' }}" class="h-10 w-auto max-w-[150px] object-contain rounded-lg">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-benzin font-extrabold text-base shadow-sm group-hover:bg-blue-700 transition">
                            K
                        </div>
                    @endif
                    <div class="flex flex-col">
                        <span class="text-lg sm:text-xl font-benzin font-extrabold tracking-tight text-slate-900">
                            {{ $settings['brand_name'] ?? 'Kahfi Engagement' }}
                        </span>
                        <span class="text-[10px] font-bold text-blue-600 tracking-wider uppercase">Organic Growth Agency</span>
                    </div>
                </a>
                
                <!-- Desktop Nav (Simplified: 4 Clean Links) -->
                <nav class="hidden md:flex items-center gap-8 text-xs font-bold text-slate-700 uppercase tracking-wider">
                    <a href="#kenapa-gabung" class="hover:text-blue-600 transition-colors">Kenapa Kami</a>
                    <a href="#portofolio" class="hover:text-blue-600 transition-colors">Portofolio</a>
                    <a href="#klien" class="hover:text-blue-600 transition-colors">Klien</a>
                    <a href="#cara-kerja" class="hover:text-blue-600 transition-colors">Cara Kerja</a>
                </nav>
                
                <!-- Action Buttons (Desktop) -->
                <div class="hidden md:flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-sm hover:shadow transition duration-150 flex items-center gap-2">
                                <span>Dashboard</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-xs sm:text-sm font-bold text-slate-700 hover:text-blue-600 transition px-3 py-2">
                                Masuk
                            </a>
                            <a href="{{ $settings['hero_primary_cta_url'] ?? 'https://wa.me/'.($settings['contact_whatsapp'] ?? '6281234567890') }}" target="_blank" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-sm hover:shadow transition duration-150 flex items-center gap-1.5">
                                <span>Konsultasi</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        @endauth
                    @endif
                </div>

                <!-- Mobile Header Right (Masuk + Hamburger) -->
                <div class="flex items-center gap-2 md:hidden">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-3 py-1.5 text-xs font-bold text-slate-700 border border-slate-200 rounded-lg">
                                Masuk
                            </a>
                        @endauth
                    @endif
                    <button @click="mobileMenu = !mobileMenu" class="p-2 rounded-xl text-slate-700 hover:bg-slate-100 transition focus:outline-none" aria-label="Toggle Menu">
                        <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <svg x-show="mobileMenu" style="display: none;" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Dropdown Menu (Clean, No Button Overlap) -->
            <div x-show="mobileMenu" x-transition style="display: none;" class="md:hidden border-t border-slate-200 bg-white px-5 py-4 space-y-3 shadow-lg">
                <a @click="mobileMenu = false" href="#kenapa-gabung" class="block py-2 text-sm font-bold text-slate-800 hover:text-blue-600">Kenapa Kami</a>
                <a @click="mobileMenu = false" href="#portofolio" class="block py-2 text-sm font-bold text-slate-800 hover:text-blue-600">Portofolio</a>
                <a @click="mobileMenu = false" href="#klien" class="block py-2 text-sm font-bold text-slate-800 hover:text-blue-600">Klien</a>
                <a @click="mobileMenu = false" href="#cara-kerja" class="block py-2 text-sm font-bold text-slate-800 hover:text-blue-600">Cara Kerja</a>
                <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                    <a href="{{ $settings['hero_primary_cta_url'] ?? 'https://wa.me/'.($settings['contact_whatsapp'] ?? '6281234567890') }}" target="_blank" class="w-full text-center py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm">
                        Konsultasi Campaign Sekarang
                    </a>
                </div>
            </div>
        </header>

        <!-- ================= SECTION 1: HERO SECTION ================= -->
        <section class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-16 lg:pt-20 lg:pb-24 text-center">
            <!-- Badge Hook Pill -->
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs sm:text-sm font-bold mb-6">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <span>{{ $settings['hero_badge'] ?? '🚀 Sistem Distribusi & Engagement Organik Terbesar' }}</span>
            </div>

            <!-- Main Headline (Benzin Font, High Contrast Blue & Black) -->
            <h1 class="font-benzin text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.18] tracking-tight max-w-5xl mx-auto">
                @php
                    $headline = $settings['hero_headline'] ?? 'Banjir Jutaan Views & Ribuan Followers Tanpa Iklan Mahal';
                    $words = explode(' ', $headline);
                    $total = count($words);
                    $firstPart = implode(' ', array_slice($words, 0, max(1, (int)($total * 0.5))));
                    $secondPart = implode(' ', array_slice($words, max(1, (int)($total * 0.5))));
                @endphp
                {{ $firstPart }}
                <span class="text-blue-600">
                    {{ $secondPart }}
                </span>
            </h1>

            <!-- Subheadline -->
            <p class="mt-6 text-sm sm:text-lg text-slate-600 max-w-3xl mx-auto leading-relaxed font-normal">
                {{ $settings['hero_subheadline'] ?? 'Tingkatkan brand awareness, trust, dan penjualan Anda secara eksponensial melalui jaringan ratusan kreator organik terverifikasi. Terpantau realtime dalam 1 dashboard.' }}
            </p>

            <!-- Dual CTA Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ $settings['hero_primary_cta_url'] ?? '#' }}" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-benzin text-xs sm:text-sm font-bold shadow-md hover:shadow-lg transition duration-150 group">
                    <span>{{ $settings['hero_primary_cta_text'] ?? 'Mulai Campaign Sekarang' }}</span>
                    <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
                <a href="{{ $settings['hero_secondary_cta_url'] ?? '#portofolio' }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-white border-2 border-slate-200 text-slate-800 hover:border-blue-600 hover:text-blue-600 rounded-xl font-benzin text-xs sm:text-sm font-bold transition duration-150">
                    {{ $settings['hero_secondary_cta_text'] ?? 'Lihat Portofolio Kami' }}
                </a>
            </div>

            <!-- Real Stats Bar (Matching Screenshot 1 with Real Live Database Data) -->
            <div class="mt-14 max-w-4xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-6 p-6 sm:p-8 rounded-2xl bg-white border border-slate-200 shadow-sm">
                <div class="text-center sm:border-r border-slate-200 sm:pr-4">
                    <div class="font-benzin text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                        {{ $formattedCreators }}
                    </div>
                    <div class="text-xs sm:text-sm font-semibold text-slate-500 mt-1.5">
                        Kreator Aktif
                    </div>
                </div>
                <div class="text-center sm:border-r border-slate-200 sm:px-4">
                    <div class="font-benzin text-3xl sm:text-4xl font-extrabold text-blue-600 tracking-tight">
                        {{ $formattedViews }}
                    </div>
                    <div class="text-xs sm:text-sm font-semibold text-slate-500 mt-1.5">
                        Total Views Terdistribusi
                    </div>
                </div>
                <div class="text-center sm:pl-4">
                    <div class="font-benzin text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                        {{ $formattedBrands }}
                    </div>
                    <div class="text-xs sm:text-sm font-semibold text-slate-500 mt-1.5">
                        Brand & Bisnis Puas
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= SECTION 2: CLIENT / BRAND PARTNERS MARQUEE ================= -->
        <section id="klien" class="relative z-10 py-10 border-y border-slate-200 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-5 text-center">
                <p class="text-[11px] font-extrabold uppercase tracking-widest text-blue-600">
                    {{ $settings['client_badge'] ?? 'BRAND TERPERCAYA' }}
                </p>
                <h3 class="font-benzin text-lg sm:text-xl font-bold text-slate-900 mt-1">
                    {{ $settings['client_title'] ?? 'Dipercaya Oleh Brand & Pebisnis Terkemuka' }}
                </h3>
            </div>

            <div class="relative w-full overflow-hidden">
                <!-- Clean Fade Borders Left & Right -->
                <div class="absolute left-0 top-0 bottom-0 w-20 bg-gradient-to-r from-white to-transparent z-10 pointer-events-none"></div>
                <div class="absolute right-0 top-0 bottom-0 w-20 bg-gradient-to-l from-white to-transparent z-10 pointer-events-none"></div>

                <div class="animate-marquee gap-6 items-center py-2">
                    @php
                        $allClients = ($clients && $clients->isNotEmpty()) ? $clients->concat($clients) : ($clients ?? collect());
                    @endphp
                    @foreach($allClients as $client)
                        <div class="px-5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-3 shrink-0 hover:border-blue-500 hover:bg-white transition">
                            @if($client->image_url)
                                <img src="{{ $client->image_url }}" alt="{{ $client->title }}" class="h-7 max-w-[110px] object-contain">
                            @else
                                <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 font-benzin font-extrabold text-xs flex items-center justify-center">
                                    {{ strtoupper(substr($client->title, 0, 2)) }}
                                </div>
                            @endif
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-slate-800 whitespace-nowrap">{{ $client->title }}</span>
                                @if($client->subtitle)
                                    <span class="text-[10px] text-slate-500 whitespace-nowrap">{{ $client->subtitle }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ================= SECTION 3: KENAPA KAMU HARUS GABUNG ================= -->
        <section id="kenapa-gabung" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="inline-block px-3.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider border border-blue-200 mb-3">
                    {{ $settings['why_join_badge'] ?? 'KEUNGGULAN KAMI' }}
                </span>
                <h2 class="font-benzin text-2xl sm:text-4xl font-extrabold text-slate-900 leading-tight">
                    {{ $settings['why_join_title'] ?? 'Kenapa Kamu Harus Gabung Kahfi Engagement?' }}
                </h2>
                <p class="text-sm sm:text-base text-slate-600 mt-3 leading-relaxed">
                    {{ $settings['why_join_subtitle'] ?? 'Kami merevolusi cara brand berpromosi: dari iklan berbayar yang kian mahal ke gelombang konten organik yang autentik dan dipercaya audiens.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($whyJoins as $idx => $item)
                    <div class="p-7 rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-md hover:border-blue-400 transition duration-200 flex flex-col justify-between group">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center font-extrabold text-lg mb-5 shadow-sm group-hover:bg-blue-700 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h3 class="font-benzin text-base sm:text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                                {{ $item->title }}
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600 mt-2.5 leading-relaxed">
                                {{ $item->description }}
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-blue-600">
                            <span>Fitur Terverifikasi</span>
                            <span class="text-slate-400 font-mono">0{{ $idx + 1 }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- ================= SECTION 4: BUKAN UNTUK SEMUA ORANG ================= -->
        <section id="bukan-untuk-semua" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
            <div class="p-6 sm:p-12 rounded-2xl bg-white border-2 border-rose-200 shadow-sm">
                <div class="text-center max-w-3xl mx-auto mb-10">
                    <span class="inline-block px-3 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-bold uppercase tracking-wider border border-rose-200 mb-3">
                        {{ $settings['not_for_badge'] ?? 'KUALIFIKASI KLIEN' }}
                    </span>
                    <h2 class="font-benzin text-2xl sm:text-4xl font-extrabold text-slate-900 leading-tight">
                        {{ $settings['not_for_title'] ?? 'Bukan Untuk Semua Orang' }}
                    </h2>
                    <h3 class="font-benzin text-xl sm:text-2xl font-bold text-rose-600 mt-2">
                        {{ $settings['not_for_subtitle'] ?? 'Jangan Daftar Kalau Kamu:' }}
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2">
                        {{ $settings['not_for_description'] ?? 'Kami hanya bekerja dengan brand dan pebisnis yang serius ingin membangun aset awareness jangka panjang dan siap scale up kapasitas penjualan.' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($notFors as $item)
                        <div class="p-5 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-4 hover:border-rose-300 transition">
                            <div class="w-9 h-9 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-base shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">
                                    {{ $item->title }}
                                </h4>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                    {{ $item->description }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ================= SECTION 5: PORTOFOLIO KAMI ================= -->
        <section id="portofolio" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="inline-block px-3.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider border border-blue-200 mb-3">
                    {{ $settings['portfolio_badge'] ?? 'HASIL NYATA' }}
                </span>
                <h2 class="font-benzin text-2xl sm:text-4xl font-extrabold text-slate-900 leading-tight">
                    {{ $settings['portfolio_title'] ?? 'Portofolio Kami' }}
                </h2>
                <p class="text-sm sm:text-base text-slate-600 mt-3 leading-relaxed">
                    {{ $settings['portfolio_subtitle'] ?? 'Bukti distribusi video organik dengan jutaan views di TikTok dan Instagram Reels tanpa biaya ads.' }}
                </p>

                <!-- Total Views Highlight Pill -->
                <div class="mt-6 inline-flex flex-col sm:flex-row items-center gap-3 px-6 py-3 rounded-xl bg-blue-600 text-white shadow-sm">
                    <span class="font-benzin text-xl sm:text-2xl font-extrabold tracking-tight">
                        {{ $formattedViews }}
                    </span>
                    <span class="text-xs sm:text-sm font-bold text-blue-100">
                        {{ $settings['portfolio_views_label'] ?? 'Total Views Berhasil Kami Hasilkan untuk Klien' }}
                    </span>
                </div>
            </div>

            <!-- Portfolio Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach($portfolios as $portfolio)
                    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden shadow-sm hover:shadow-md hover:border-blue-400 transition duration-200 flex flex-col group">
                        <!-- Thumbnail Container -->
                        <div class="h-52 bg-slate-900 relative overflow-hidden flex items-center justify-center">
                            @if($portfolio->image_url)
                                <img src="{{ $portfolio->image_url }}" alt="{{ $portfolio->title }}" class="w-full h-full object-cover group-hover:scale-102 transition duration-300">
                            @else
                                <div class="w-full h-full bg-slate-800 flex flex-col items-center justify-center p-6 text-center text-white">
                                    <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center mb-2 text-white">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <span class="text-xs font-bold text-blue-300 uppercase tracking-wider">{{ $portfolio->extra_meta['platform'] ?? 'Campaign Video' }}</span>
                                    <span class="text-sm font-bold mt-1 truncate max-w-[200px]">{{ $portfolio->title }}</span>
                                </div>
                            @endif

                            <!-- Floating Views Count Badge -->
                            <div class="absolute top-3 right-3 px-3 py-1 rounded-lg bg-slate-900/90 text-white font-benzin font-bold text-xs shadow border border-white/10 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                <span>{{ $portfolio->extra_meta['views'] ?? 'Viral' }}</span>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <p class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">
                                    {{ $portfolio->subtitle }}
                                </p>
                                <h3 class="font-benzin text-base font-bold text-slate-900 mt-1 group-hover:text-blue-600 transition-colors">
                                    {{ $portfolio->title }}
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                                    {{ $portfolio->description }}
                                </p>
                            </div>

                            <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-600 flex items-center gap-1">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    {{ $portfolio->extra_meta['creators'] ?? 'Verified Creator' }}
                                </span>
                                <a href="{{ $settings['hero_primary_cta_url'] ?? '#' }}" target="_blank" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 group/btn">
                                    <span>Konsultasi</span>
                                    <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- ================= SECTION 6: BAGAIMANA KAMI BEKERJA ================= -->
        <section id="cara-kerja" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="inline-block px-3.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider border border-blue-200 mb-3">
                    {{ $settings['how_badge'] ?? 'PROSES KERJA' }}
                </span>
                <h2 class="font-benzin text-2xl sm:text-4xl font-extrabold text-slate-900 leading-tight">
                    {{ $settings['how_title'] ?? 'Bagaimana Kami Bekerja' }}
                </h2>
                <p class="text-sm sm:text-base text-slate-600 mt-3 leading-relaxed">
                    {{ $settings['how_subtitle'] ?? 'Sistem 4 langkah teruji yang memastikan konten Anda viral tepat sasaran tanpa membuang waktu dan energi Anda.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($howSteps as $idx => $step)
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-md hover:border-blue-400 transition duration-200 flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-mono font-bold text-xs">
                                    {{ $step->subtitle ?: 'LANGKAH 0'.($idx+1) }}
                                </span>
                                <span class="font-benzin text-3xl font-extrabold text-blue-600">
                                    0{{ $idx + 1 }}
                                </span>
                            </div>
                            <h3 class="font-benzin text-base font-bold text-slate-900 leading-snug">
                                {{ $step->title }}
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                                {{ $step->description }}
                            </p>
                        </div>
                        <div class="mt-5 pt-3 border-t border-slate-100">
                            <span class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                Tahap {{ $idx + 1 }} dari 4
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- ================= SECTION 7: SIAP BANJIR JUTAAN VIEWS? (CTA CONVERSION) ================= -->
        <section class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="p-8 sm:p-14 rounded-3xl bg-[#0B1528] text-white border border-slate-800 shadow-xl relative overflow-hidden text-center">
                <div class="relative z-10 max-w-3xl mx-auto">
                    <span class="inline-block px-3.5 py-1 rounded-full bg-blue-900/60 border border-blue-500/40 text-blue-300 text-xs font-bold uppercase tracking-widest mb-4">
                        {{ $settings['cta_badge'] ?? 'AMBIL KESEMPATAN' }}
                    </span>

                    <h2 class="font-benzin text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight text-white">
                        {{ $settings['cta_title'] ?? 'Siap Banjir Jutaan Views?' }}
                    </h2>

                    <p class="text-sm sm:text-base text-slate-300 mt-4 leading-relaxed font-normal">
                        {{ $settings['cta_subtitle'] ?? 'Slot campaign bulanan kami dibatasi agar setiap brand mendapatkan alokasi kreator terbaik dan engagement maksimal. Hubungi kami sekarang sebelum kuota penuh.' }}
                    </p>

                    <!-- Feature Chips -->
                    <div class="mt-7 flex flex-wrap items-center justify-center gap-2.5">
                        @foreach($ctaChips as $chip)
                            <span class="px-3.5 py-1.5 rounded-xl bg-white/10 border border-white/10 text-xs sm:text-sm font-semibold text-white">
                                {{ $chip->title }}
                            </span>
                        @endforeach
                    </div>

                    <!-- CTA Button -->
                    <div class="mt-8">
                        <a href="{{ $settings['cta_button_url'] ?? 'https://wa.me/'.($settings['contact_whatsapp'] ?? '6281234567890') }}" target="_blank" class="inline-flex items-center justify-center px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-benzin text-xs sm:text-sm font-bold shadow-lg hover:shadow-xl transition duration-150 group">
                            <span>{{ $settings['cta_button_text'] ?? 'Amankan Slot Campaign Anda Sekarang' }}</span>
                            <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= FLOATING WHATSAPP CTA ================= -->
        <aside aria-label="WhatsApp Contact" class="fixed bottom-6 right-6 z-50 flex items-center gap-3">
            <a href="https://wa.me/{{ $settings['contact_whatsapp'] ?? '6281234567890' }}?text=Halo%20Admin%20{{ urlencode($settings['brand_name'] ?? 'Kahfi Engagement') }},%20saya%20tertarik%20bekerjasama" target="_blank" class="flex items-center gap-2.5 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full shadow-lg hover:shadow-xl transition duration-150">
                <div class="relative">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.071.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.579 4.178 1.594 5.91l-1.693 6.183 6.356-1.667c1.677.915 3.599 1.438 5.643 1.438 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                    <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-white animate-ping"></span>
                </div>
                <span class="text-xs sm:text-sm font-bold pr-1">{{ $settings['floating_wa_text'] ?? 'Konsultasi Campaign Sekarang' }}</span>
            </a>
        </aside>

        <!-- ================= FOOTER ================= -->
        <footer class="relative z-10 border-t border-slate-200 bg-white py-10 px-4 sm:px-6 lg:px-8 text-slate-600">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-5">
                <div class="flex items-center gap-3">
                    @if($brandLogoUrl)
                        <img src="{{ $brandLogoUrl }}" alt="{{ $settings['brand_name'] ?? 'Kahfi Engagement' }}" class="h-8 w-auto max-w-[120px] object-contain rounded-lg">
                    @else
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-benzin font-bold text-xs">
                            K
                        </div>
                    @endif
                    <span class="font-benzin font-extrabold text-slate-900 text-sm">
                        {{ $settings['brand_name'] ?? 'Kahfi Engagement' }}
                    </span>
                    <span class="text-xs text-slate-400 hidden sm:inline">| {{ $settings['brand_tagline'] ?? 'Influencer Campaign & Growth Management' }}</span>
                </div>

                <div class="flex items-center gap-6 text-xs font-bold text-slate-700">
                    <a href="#kenapa-gabung" class="hover:text-blue-600 transition">Kenapa Kami</a>
                    <a href="#portofolio" class="hover:text-blue-600 transition">Portofolio</a>
                    <a href="#klien" class="hover:text-blue-600 transition">Klien</a>
                    <a href="#cara-kerja" class="hover:text-blue-600 transition">Cara Kerja</a>
                    <a href="{{ route('login') }}" class="hover:text-blue-600 transition">Login</a>
                </div>

                <div class="text-xs text-slate-500 font-medium">
                    &copy; {{ date('Y') }} {{ $settings['brand_name'] ?? 'Kahfi Engagement' }}. All rights reserved.
                </div>
            </div>
        </footer>

    </body>
</html>
