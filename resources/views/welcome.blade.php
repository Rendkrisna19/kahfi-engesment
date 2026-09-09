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
    $totalEngagement = $totalEngagement ?? (\Illuminate\Support\Facades\Schema::hasTable('links') ? ((\App\Models\Link::sum('likes') + \App\Models\Link::sum('comments') + \App\Models\Link::sum('shares') + \App\Models\Link::sum('saves')) ?: 0) : 0);
    $totalCreators = $totalCreators ?? (\Illuminate\Support\Facades\Schema::hasTable('links') ? (\App\Models\Link::whereNotNull('username')->where('username', '!=', '')->distinct('username')->count('username') ?: (\App\Models\User::where('role', 'Creator')->count() ?: 1)) : 1);
    $totalBrands = $totalBrands ?? (\Illuminate\Support\Facades\Schema::hasTable('campaigns') ? max(\App\Models\Campaign::count(), \App\Models\User::where('role', 'Client')->count(), 1) : 1);

    // Number formatting helper
    $formatMetric = function($num, $fallback = '1.84M') {
        if (!$num || $num <= 0) return $fallback;
        if ($num >= 1000000000) return round($num / 1000000000, 2) . 'B';
        if ($num >= 1000000) return round($num / 1000000, 2) . 'M';
        if ($num >= 1000) return round($num / 1000, 1) . 'K';
        return number_format($num, 0, ',', '.');
    };

    $formattedViews = $totalViews > 0 ? $formatMetric($totalViews, '1.84M') : '1.84M';
    $formattedEngagement = $totalEngagement > 0 ? $formatMetric($totalEngagement, '294.6K') : '294.6K';
    $formattedCreators = ($totalCreators >= 1000 ? round($totalCreators / 1000, 1) . 'K+' : $totalCreators . '+');
    $formattedBrands = ($totalBrands >= 1000 ? round($totalBrands / 1000, 1) . 'K+' : $totalBrands . '+');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $settings['brand_name'] ?? 'Kahfi Engagement' }} - {{ $settings['brand_tagline'] ?? 'Agency Engagement & Influencer Campaign Management' }}</title>
        
        <!-- Google Fonts: Montserrat (Font Lama Bersih, Tegas & Modern) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        
        <style>
            body {
                font-family: 'Montserrat', sans-serif;
            }
            .bg-grid-pattern {
                background-size: 44px 44px;
                background-image: radial-gradient(circle, rgba(99, 102, 241, 0.09) 1px, transparent 1px);
            }
            @keyframes marquee {
                0% { transform: translateX(0%); }
                100% { transform: translateX(-50%); }
            }
            .animate-marquee {
                display: flex;
                width: max-content;
                animation: marquee 32s linear infinite;
            }
            .animate-marquee:hover {
                animation-play-state: paused;
            }
            @keyframes marqueePortfolio {
                0% { transform: translateX(0%); }
                100% { transform: translateX(-50%); }
            }
            .animate-marquee-portfolio {
                display: flex;
                width: max-content;
                animation: marqueePortfolio 35s linear infinite;
            }
            .animate-marquee-portfolio:hover {
                animation-play-state: paused;
            }
            @keyframes marqueeReverse {
                0% { transform: translateX(-50%); }
                100% { transform: translateX(0%); }
            }
            .animate-marquee-reverse {
                display: flex;
                width: max-content;
                animation: marqueeReverse 35s linear infinite;
            }
            .animate-marquee-reverse:hover {
                animation-play-state: paused;
            }
            .text-gradient-purple-blue {
                background: linear-gradient(135deg, #2563eb 0%, #6366f1 50%, #7c3aed 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
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
                            sans: ['"Montserrat"', 'sans-serif'],
                        },
                        colors: {
                            brand: {
                                blue: '#2563eb',
                                purple: '#7c3aed',
                                indigo: '#4f46e5',
                                dark: '#0b1329',
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
    <body class="bg-[#F8FAFC] text-slate-900 antialiased min-h-screen relative overflow-x-hidden bg-grid-pattern selection:bg-purple-600 selection:text-white">

        <!-- Ambient Gradient Background Glows (Tema Ungu, Biru, Putih) -->
        <div class="fixed top-0 right-0 w-[600px] h-[600px] rounded-full bg-purple-500/15 blur-[150px] pointer-events-none z-0"></div>
        <div class="fixed top-[24%] -left-32 w-[650px] h-[650px] rounded-full bg-blue-500/15 blur-[160px] pointer-events-none z-0"></div>
        <div class="fixed bottom-0 right-[10%] w-[550px] h-[550px] rounded-full bg-indigo-500/15 blur-[150px] pointer-events-none z-0"></div>
        <div class="fixed top-[55%] left-[25%] w-[450px] h-[450px] rounded-full bg-violet-400/10 blur-[160px] pointer-events-none z-0"></div>

        <!-- ================= NAVBAR ================= -->
        <header x-data="{ mobileMenu: false }" class="sticky top-0 z-50 w-full bg-white/85 backdrop-blur-md border-b border-slate-200/80 shadow-sm transition-all duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <!-- Brand Logo & Name -->
                <a href="/" class="flex items-center gap-3 group">
                    @if($brandLogoUrl)
                        <img src="{{ $brandLogoUrl }}" alt="{{ $settings['brand_name'] ?? 'Kahfi Engagement' }}" class="h-10 w-auto max-w-[150px] object-contain rounded-lg">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-purple-600 flex items-center justify-center text-white font-black text-base shadow-md shadow-indigo-500/25 group-hover:scale-105 transition">
                            K
                        </div>
                    @endif
                    <div class="flex flex-col">
                        <span class="text-lg sm:text-xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600">
                            {{ $settings['brand_name'] ?? 'Kahfi Engagement' }}
                        </span>
                        <span class="text-[10px] font-bold text-slate-500 tracking-wider uppercase">Organic Growth Agency</span>
                    </div>
                </a>
                
                <!-- Desktop Nav (Clean 4 Links) -->
                <nav class="hidden md:flex items-center gap-8 text-xs font-bold text-slate-700 uppercase tracking-wider">
                    <a href="#kenapa-gabung" class="hover:text-purple-600 transition-colors">Kenapa Kami</a>
                    <a href="#portofolio" class="hover:text-purple-600 transition-colors">Portofolio</a>
                    <a href="#klien" class="hover:text-purple-600 transition-colors">Klien</a>
                    <a href="#cara-kerja" class="hover:text-purple-600 transition-colors">Cara Kerja</a>
                </nav>
                
                <!-- Action Buttons (Desktop) -->
                <div class="hidden md:flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 hover:from-blue-700 hover:via-indigo-700 hover:to-purple-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-indigo-500/20 hover:shadow-lg transition duration-200 flex items-center gap-2">
                                <span>Dashboard</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-xs sm:text-sm font-bold text-slate-700 hover:text-purple-600 transition px-3 py-2">
                                Masuk
                            </a>
                            <a href="{{ $settings['hero_primary_cta_url'] ?? 'https://wa.me/'.($settings['contact_whatsapp'] ?? '6281234567890') }}" target="_blank" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 hover:from-blue-700 hover:via-indigo-700 hover:to-purple-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-indigo-500/20 hover:shadow-lg transition duration-200 flex items-center gap-1.5">
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
                            <a href="{{ url('/dashboard') }}" class="px-3 py-1.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg text-xs font-bold shadow-sm">
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

            <!-- Mobile Dropdown Menu -->
            <div x-show="mobileMenu" x-transition style="display: none;" class="md:hidden border-t border-slate-200 bg-white px-5 py-4 space-y-3 shadow-lg">
                <a @click="mobileMenu = false" href="#kenapa-gabung" class="block py-2 text-sm font-bold text-slate-800 hover:text-purple-600">Kenapa Kami</a>
                <a @click="mobileMenu = false" href="#portofolio" class="block py-2 text-sm font-bold text-slate-800 hover:text-purple-600">Portofolio</a>
                <a @click="mobileMenu = false" href="#klien" class="block py-2 text-sm font-bold text-slate-800 hover:text-purple-600">Klien</a>
                <a @click="mobileMenu = false" href="#cara-kerja" class="block py-2 text-sm font-bold text-slate-800 hover:text-purple-600">Cara Kerja</a>
                <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                    <a href="{{ $settings['hero_primary_cta_url'] ?? 'https://wa.me/'.($settings['contact_whatsapp'] ?? '6281234567890') }}" target="_blank" class="w-full text-center py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white rounded-xl text-xs font-bold shadow-sm">
                        Konsultasi Campaign Sekarang
                    </a>
                </div>
            </div>
        </header>

        <!-- ================= SECTION 1: HERO SECTION ================= -->
        <section class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-16 lg:pt-20 lg:pb-24 text-center">
            <!-- Badge Hook Pill (No Emojis, Clean SVG Icon) -->
            <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-gradient-to-r from-blue-500/10 via-purple-500/10 to-indigo-500/10 border border-indigo-200/80 text-indigo-900 text-xs sm:text-sm font-bold mb-6 shadow-sm">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-purple-600"></span>
                </span>
                <svg class="w-4 h-4 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>{{ preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F700}-\x{1F77F}\x{1F780}-\x{1F7FF}\x{1F800}-\x{1F8FF}\x{1F900}-\x{1F9FF}\x{1FA00}-\x{1FA6F}\x{1FA70}-\x{1FAFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{FE00}-\x{FE0F}\x{1F1E6}-\x{1F1FF}]/u', '', $settings['hero_badge'] ?? 'Sistem Distribusi & Engagement Organik Terbesar') }}</span>
            </div>

            <!-- Main Headline (Clean Montserrat, Ungu Biru Putih Theme) -->
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-slate-900 leading-[1.18] tracking-tight max-w-5xl mx-auto">
                @php
                    $headline = $settings['hero_headline'] ?? 'Banjir Jutaan Views & Ribuan Followers Tanpa Iklan Mahal';
                    $words = explode(' ', $headline);
                    $total = count($words);
                    $firstPart = implode(' ', array_slice($words, 0, max(1, (int)($total * 0.5))));
                    $secondPart = implode(' ', array_slice($words, max(1, (int)($total * 0.5))));
                @endphp
                {{ $firstPart }}
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600">
                    {{ $secondPart }}
                </span>
            </h1>

            <!-- Subheadline -->
            <p class="mt-6 text-sm sm:text-lg text-slate-600 max-w-3xl mx-auto leading-relaxed font-medium">
                {{ $settings['hero_subheadline'] ?? 'Tingkatkan brand awareness, trust, dan penjualan Anda secara eksponensial melalui jaringan ratusan kreator organik terverifikasi. Terpantau realtime dalam 1 dashboard.' }}
            </p>

            <!-- Dual CTA Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ $settings['hero_primary_cta_url'] ?? '#' }}" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 hover:from-blue-700 hover:via-indigo-700 hover:to-purple-700 text-white rounded-xl text-xs sm:text-sm font-extrabold shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/35 transition duration-200 group">
                    <span>{{ $settings['hero_primary_cta_text'] ?? 'Mulai Campaign Sekarang' }}</span>
                    <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
                <a href="{{ $settings['hero_secondary_cta_url'] ?? '#portofolio' }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-white/90 backdrop-blur-sm border-2 border-indigo-200/80 text-slate-800 hover:border-purple-600 hover:text-purple-600 rounded-xl text-xs sm:text-sm font-extrabold shadow-sm hover:shadow transition duration-200">
                    {{ $settings['hero_secondary_cta_text'] ?? 'Lihat Portofolio Kami' }}
                </a>
            </div>

            <!-- ================= REALTIME ANALYTICS PREVIEW ================= -->
            <div class="mt-10 sm:mt-14 w-full max-w-lg mx-auto bg-white/95 backdrop-blur-md border border-indigo-100/90 rounded-3xl p-5 sm:p-7 shadow-2xl shadow-indigo-500/10 text-left relative overflow-hidden transition-all duration-300">
                <!-- Header with Mac-style Dots -->
                <div class="flex items-center justify-between pb-3 sm:pb-4 border-b border-slate-100 mb-4 sm:mb-5">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-red-400"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                        <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                    </div>
                    <span class="text-[10px] sm:text-xs font-extrabold text-indigo-400 tracking-wider uppercase">
                        REALTIME ANALYTICS PREVIEW
                    </span>
                </div>

                <!-- 2 Top Stats Cards (Side by side on mobile and desktop) -->
                <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-5 sm:mb-6">
                    <!-- Total Views Card -->
                    <div class="bg-gradient-to-br from-blue-50/70 to-indigo-50/40 p-3.5 sm:p-4 rounded-2xl border border-blue-100/80 flex flex-col justify-between">
                        <p class="text-[10px] sm:text-xs text-blue-900/60 font-bold uppercase tracking-wider">
                            TOTAL VIEWS
                        </p>
                        <h4 class="text-2xl sm:text-3xl font-black text-blue-600 mt-1 sm:mt-1.5 tracking-tight">
                            {{ $formattedViews }}
                        </h4>
                        <p class="text-[10px] sm:text-xs text-emerald-600 font-bold mt-1 sm:mt-1.5 flex items-center gap-1">
                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                            <span>+14.2% bulan ini</span>
                        </p>
                    </div>

                    <!-- Total Engagement Card -->
                    <div class="bg-gradient-to-br from-purple-50/70 to-indigo-50/40 p-3.5 sm:p-4 rounded-2xl border border-purple-100/80 flex flex-col justify-between">
                        <p class="text-[10px] sm:text-xs text-purple-900/60 font-bold uppercase tracking-wider">
                            TOTAL ENGAGEMENT
                        </p>
                        <h4 class="text-2xl sm:text-3xl font-black text-purple-600 mt-1 sm:mt-1.5 tracking-tight">
                            {{ $formattedEngagement }}
                        </h4>
                        <p class="text-[10px] sm:text-xs text-emerald-600 font-bold mt-1 sm:mt-1.5 flex items-center gap-1">
                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                            <span>+8.7% bulan ini</span>
                        </p>
                    </div>
                </div>

                <!-- Progress Rows for TikTok & Instagram Campaigns -->
                <div class="space-y-3.5 sm:space-y-4">
                    <!-- TikTok Campaign -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs sm:text-sm font-bold">
                            <span class="text-slate-800">TikTok Campaign (Active)</span>
                            <span class="text-blue-600 font-black text-xs sm:text-sm">84%</span>
                        </div>
                        <div class="w-full h-2.5 sm:h-3 bg-slate-100 rounded-full overflow-hidden p-0.5">
                            <div class="h-full bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full transition-all duration-700" style="width: 84%"></div>
                        </div>
                    </div>

                    <!-- Instagram Campaign -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs sm:text-sm font-bold">
                            <span class="text-slate-800">Instagram Campaign (Processing)</span>
                            <span class="text-purple-600 font-black text-xs sm:text-sm">62%</span>
                        </div>
                        <div class="w-full h-2.5 sm:h-3 bg-slate-100 rounded-full overflow-hidden p-0.5">
                            <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full transition-all duration-700" style="width: 62%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= SECTION: KENAPA KAMU HARUS GABUNG ================= -->
        <section id="kenapa-gabung" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="inline-block px-3.5 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-bold uppercase tracking-wider border border-purple-200/80 mb-3">
                    {{ $settings['why_join_badge'] ?? 'KEUNGGULAN KAMI' }}
                </span>
                <h2 class="text-2xl sm:text-4xl font-black text-slate-900 leading-tight">
                    {{ $settings['why_join_title'] ?? 'Kenapa Kamu Harus Gabung Kahfi Engagement?' }}
                </h2>
                <p class="text-sm sm:text-base text-slate-600 mt-3 leading-relaxed font-medium">
                    {{ $settings['why_join_subtitle'] ?? 'Kami merevolusi cara brand berpromosi: dari iklan berbayar yang kian mahal ke gelombang konten organik yang autentik dan dipercaya audiens.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($whyJoins as $idx => $item)
                    <div class="p-6 sm:p-7 rounded-2xl bg-white/95 border border-slate-200 shadow-sm hover:shadow-xl hover:border-purple-300 hover:shadow-purple-500/5 transition duration-200 flex flex-col justify-between group">
                        <div>
                            <!-- Icon Ceklis Kecil & Rapi (Matching Image 1) -->
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center mb-3.5 shadow-xs group-hover:scale-105 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.8" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 group-hover:text-purple-600 transition-colors">
                                {{ $item->title }}
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600 mt-2.5 leading-relaxed font-medium">
                                {{ $item->description }}
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-indigo-600">
                            <span>Fitur Terverifikasi</span>
                            <span class="text-slate-400 font-mono">0{{ $idx + 1 }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- ================= SECTION 4: BUKAN UNTUK SEMUA ORANG ================= -->
        <section id="bukan-untuk-semua" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
            <div class="p-6 sm:p-12 rounded-3xl bg-white/95 border-2 border-rose-200/80 shadow-sm">
                <div class="text-center max-w-3xl mx-auto mb-10">
                    <span class="inline-block px-3 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-bold uppercase tracking-wider border border-rose-200 mb-3">
                        {{ $settings['not_for_badge'] ?? 'KUALIFIKASI KLIEN' }}
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-900 leading-tight">
                        {{ $settings['not_for_title'] ?? 'Bukan Untuk Semua Orang' }}
                    </h2>
                    <h3 class="text-xl sm:text-2xl font-extrabold text-rose-600 mt-2">
                        {{ $settings['not_for_subtitle'] ?? 'Jangan Daftar Kalau Kamu:' }}
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2 font-medium">
                        {{ $settings['not_for_description'] ?? 'Kami hanya bekerja dengan brand dan pebisnis yang serius ingin membangun aset awareness jangka panjang dan siap scale up kapasitas penjualan.' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($notFors as $item)
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 flex items-start gap-4 hover:border-rose-300 transition">
                            <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-base shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">
                                    {{ $item->title }}
                                </h4>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed font-medium">
                                    {{ $item->description }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ================= SECTION: KLIEN BRAND NASIONAL (DI ATAS PORTO) ================= -->
        <section id="klien" class="relative z-10 pt-8 pb-3 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Sleek Card Container -->
                <div class="relative max-w-5xl mx-auto rounded-3xl bg-[#0a0e1c] border border-indigo-950/80 shadow-2xl p-5 sm:p-7 overflow-hidden text-center">
                    <!-- Subtle Central Glow -->
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-32 bg-indigo-600/10 blur-[80px] pointer-events-none"></div>

                    <!-- Header with Clean Lines -->
                    <div class="flex items-center justify-center gap-3 sm:gap-6 mb-4 sm:mb-5">
                        <div class="h-[1px] w-8 sm:w-24 bg-slate-800"></div>
                        <span class="text-[10px] sm:text-xs font-bold uppercase tracking-[0.2em] text-slate-400">
                            {{ $settings['client_badge'] ?? 'DIPERCAYA BRAND & PRODUCTION HOUSE NASIONAL' }}
                        </span>
                        <div class="h-[1px] w-8 sm:w-24 bg-slate-800"></div>
                    </div>

                    <!-- Brand Logos Infinite Carousel -->
                    <div class="relative w-full overflow-hidden py-1">
                        <div class="absolute left-0 top-0 bottom-0 w-12 sm:w-20 bg-gradient-to-r from-[#0a0e1c] to-transparent z-10 pointer-events-none"></div>
                        <div class="absolute right-0 top-0 bottom-0 w-12 sm:w-20 bg-gradient-to-l from-[#0a0e1c] to-transparent z-10 pointer-events-none"></div>

                        <div class="animate-marquee gap-8 sm:gap-12 items-center py-1">
                            @php
                                $allClients = ($clients && $clients->isNotEmpty()) ? $clients->concat($clients)->concat($clients) : ($clients ?? collect());
                            @endphp
                            @foreach($allClients as $client)
                                <div class="h-10 sm:h-12 px-3 sm:px-4 py-1 flex items-center justify-center shrink-0 brightness-0 invert opacity-75 hover:opacity-100 transition duration-300">
                                    @if($client->image_url)
                                        <img src="{{ $client->image_url }}" alt="{{ $client->title ?? 'Client' }}" class="h-6 sm:h-8 max-w-[120px] object-contain">
                                    @else
                                        <span class="text-xs sm:text-sm font-extrabold text-white tracking-wide">{{ $client->title }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= SECTION: PORTOFOLIO KAMI (DUAL CAROUSEL IN ENCLOSED CARD) ================= -->
        <section id="portofolio" class="relative z-10 pt-3 pb-12 sm:pb-16 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Enclosed Box / Card matching Reference Screenshot -->
                <div class="relative max-w-5xl mx-auto rounded-3xl bg-[#0a0e1c] border border-indigo-950/80 shadow-2xl p-5 sm:p-8 overflow-hidden text-center"
                     style="background-image: linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px), linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px); background-size: 24px 24px;">
                    
                    <!-- Ambient Glow -->
                    <div class="absolute top-10 left-1/2 -translate-x-1/2 w-80 h-36 bg-lime-500/10 blur-[85px] pointer-events-none"></div>

                    <!-- Header -->
                    <div class="relative z-10 max-w-2xl mx-auto">
                        <h2 class="text-2xl sm:text-4xl font-black text-[#76ff03] tracking-tight">
                            {{ $settings['portfolio_title'] ?? 'Portofolio Kami' }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-300 mt-1 sm:mt-2 font-medium">
                            {{ $settings['portfolio_subtitle'] ?? 'Jangkau Jutaan Audiens Bareng Kahfi Engagement!' }}
                        </p>

                        <!-- Big Highlight Views Counter (as in Screenshot) -->
                        <div class="mt-4 mb-5 sm:mb-7">
                            <div class="text-3xl sm:text-5xl lg:text-6xl font-black text-[#76ff03] tracking-tight font-mono">
                                {{ $totalViews > 10000000 ? number_format($totalViews, 0, ',', '.') . '+' : '680.225.190+' }}
                            </div>
                            <div class="text-[10px] sm:text-xs font-black uppercase tracking-[0.25em] text-slate-400 mt-1">
                                TOTAL VIEWS
                            </div>
                        </div>
                    </div>

                    @php
                        $allPortfoliosList = $portfolios;
                        if ($allPortfoliosList->isEmpty()) {
                            $dummyList = collect([
                                (object)['id' => 1, 'title' => 'Sihir Tanah Kubur', 'image_url' => null, 'subtitle' => '121.109.098', 'extra_meta' => ['views' => '121.109.098']],
                                (object)['id' => 2, 'title' => 'Visinema Jumbo', 'image_url' => null, 'subtitle' => '120.569.023', 'extra_meta' => ['views' => '120.569.023']],
                                (object)['id' => 3, 'title' => 'Timur Movie Viral', 'image_url' => null, 'subtitle' => '90.233.887', 'extra_meta' => ['views' => '90.233.887']],
                                (object)['id' => 4, 'title' => 'Cerita Horor FYP', 'image_url' => null, 'subtitle' => '68.897.085', 'extra_meta' => ['views' => '68.897.085']],
                                (object)['id' => 5, 'title' => 'F&B Brand Campaign', 'image_url' => null, 'subtitle' => '101.452.120', 'extra_meta' => ['views' => '101.452.120']],
                                (object)['id' => 6, 'title' => 'Skincare Launch Boom', 'image_url' => null, 'subtitle' => '84.112.550', 'extra_meta' => ['views' => '84.112.550']],
                                (object)['id' => 7, 'title' => 'Consumer Brand Organic', 'image_url' => null, 'subtitle' => '77.840.900', 'extra_meta' => ['views' => '77.840.900']],
                                (object)['id' => 8, 'title' => 'Fashion OOTD Trends', 'image_url' => null, 'subtitle' => '95.620.000', 'extra_meta' => ['views' => '95.620.000']],
                            ]);
                            $allPortfoliosList = $dummyList;
                        }

                        $topItems = collect();
                        $bottomItems = collect();
                        foreach ($allPortfoliosList as $k => $item) {
                            if ($k % 2 === 0) {
                                $topItems->push($item);
                            } else {
                                $bottomItems->push($item);
                            }
                        }
                        if ($topItems->isEmpty()) $topItems = $allPortfoliosList;
                        if ($bottomItems->isEmpty()) $bottomItems = $allPortfoliosList;

                        while ($topItems->count() < 8) {
                            $topItems = $topItems->concat($topItems);
                        }
                        $topLoop = $topItems->concat($topItems);

                        while ($bottomItems->count() < 8) {
                            $bottomItems = $bottomItems->concat($bottomItems);
                        }
                        $bottomLoop = $bottomItems->concat($bottomItems);
                    @endphp

                    <!-- Dual-Row Carousel: Row 1 (Top, Left-moving) -->
                    <div class="relative w-full overflow-hidden py-1.5">
                        <div class="absolute left-0 top-0 bottom-0 w-12 sm:w-20 bg-gradient-to-r from-[#0a0e1c] to-transparent z-20 pointer-events-none"></div>
                        <div class="absolute right-0 top-0 bottom-0 w-12 sm:w-20 bg-gradient-to-l from-[#0a0e1c] to-transparent z-20 pointer-events-none"></div>

                        <div class="animate-marquee-portfolio gap-3 sm:gap-4 items-center">
                            @foreach($topLoop as $portfolio)
                                @php
                                    $displayViews = !empty($portfolio->extra_meta['views']) ? $portfolio->extra_meta['views'] : (!empty($portfolio->subtitle) ? $portfolio->subtitle : '120.569.023');
                                @endphp
                                <div class="w-36 sm:w-44 shrink-0 rounded-2xl bg-slate-900 border border-slate-700/60 overflow-hidden shadow-lg hover:border-[#76ff03]/80 transition duration-300 group relative">
                                    <div class="relative w-full aspect-[2/3] bg-slate-950 overflow-hidden flex items-center justify-center">
                                        @if($portfolio->image_url)
                                            <img src="{{ $portfolio->image_url }}" alt="{{ $portfolio->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-b from-slate-800 to-slate-950 flex flex-col items-center justify-center p-3 text-center text-white">
                                                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-lime-500 to-emerald-600 flex items-center justify-center mb-1 text-slate-950 font-black shadow">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                </div>
                                                <span class="text-xs font-bold text-white line-clamp-2">{{ $portfolio->title ?: 'Portofolio' }}</span>
                                            </div>
                                        @endif

                                        <!-- Trending Upward Arrow Glass Overlay (Matching Reference Screenshot) -->
                                        <div class="absolute inset-0 flex flex-col items-center justify-center p-2 pointer-events-none">
                                            <div class="backdrop-blur-md bg-black/45 border border-white/20 rounded-xl px-2.5 py-2 flex flex-col items-center shadow-lg">
                                                <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                                </svg>
                                                <div class="flex items-center gap-1 text-[10px] sm:text-[11px] text-white font-extrabold mt-1">
                                                    <svg class="w-3 h-3 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    <span>{{ $displayViews }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bottom White Pill Badge (Matching Reference Screenshot) -->
                                        <div class="absolute bottom-2 left-1/2 -translate-x-1/2 px-2.5 py-0.5 rounded-md bg-white text-slate-950 font-black text-[10px] sm:text-xs tracking-tight shadow-md whitespace-nowrap z-10 border border-white">
                                            {{ $displayViews }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Dual-Row Carousel: Row 2 (Bottom, Right-moving) -->
                    <div class="relative w-full overflow-hidden py-1.5 mt-1 sm:mt-2">
                        <div class="absolute left-0 top-0 bottom-0 w-12 sm:w-20 bg-gradient-to-r from-[#0a0e1c] to-transparent z-20 pointer-events-none"></div>
                        <div class="absolute right-0 top-0 bottom-0 w-12 sm:w-20 bg-gradient-to-l from-[#0a0e1c] to-transparent z-20 pointer-events-none"></div>

                        <div class="animate-marquee-reverse gap-3 sm:gap-4 items-center">
                            @foreach($bottomLoop as $portfolio)
                                @php
                                    $displayViews = !empty($portfolio->extra_meta['views']) ? $portfolio->extra_meta['views'] : (!empty($portfolio->subtitle) ? $portfolio->subtitle : '90.233.887');
                                @endphp
                                <div class="w-36 sm:w-44 shrink-0 rounded-2xl bg-slate-900 border border-slate-700/60 overflow-hidden shadow-lg hover:border-[#76ff03]/80 transition duration-300 group relative">
                                    <div class="relative w-full aspect-[2/3] bg-slate-950 overflow-hidden flex items-center justify-center">
                                        @if($portfolio->image_url)
                                            <img src="{{ $portfolio->image_url }}" alt="{{ $portfolio->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-b from-slate-800 to-slate-950 flex flex-col items-center justify-center p-3 text-center text-white">
                                                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-lime-500 to-emerald-600 flex items-center justify-center mb-1 text-slate-950 font-black shadow">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                </div>
                                                <span class="text-xs font-bold text-white line-clamp-2">{{ $portfolio->title ?: 'Portofolio' }}</span>
                                            </div>
                                        @endif

                                        <!-- Trending Upward Arrow Glass Overlay (Matching Reference Screenshot) -->
                                        <div class="absolute inset-0 flex flex-col items-center justify-center p-2 pointer-events-none">
                                            <div class="backdrop-blur-md bg-black/45 border border-white/20 rounded-xl px-2.5 py-2 flex flex-col items-center shadow-lg">
                                                <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                                </svg>
                                                <div class="flex items-center gap-1 text-[10px] sm:text-[11px] text-white font-extrabold mt-1">
                                                    <svg class="w-3 h-3 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    <span>{{ $displayViews }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bottom White Pill Badge (Matching Reference Screenshot) -->
                                        <div class="absolute bottom-2 left-1/2 -translate-x-1/2 px-2.5 py-0.5 rounded-md bg-white text-slate-950 font-black text-[10px] sm:text-xs tracking-tight shadow-md whitespace-nowrap z-10 border border-white">
                                            {{ $displayViews }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- WhatsApp CTA Button (Matching Reference Screenshot) -->
                    <div class="mt-6 sm:mt-8 flex justify-center relative z-20">
                        <a href="https://wa.me/{{ $settings['contact_whatsapp'] ?? '6281234567890' }}?text={{ urlencode('Halo Admin ' . ($settings['brand_name'] ?? 'Kahfi Engagement') . ', saya tertarik dengan portofolio Anda dan ingin konsultasi campaign') }}" target="_blank" class="inline-flex items-center gap-2.5 px-7 py-3 sm:py-3.5 rounded-full bg-[#76ff03] hover:bg-[#64dd17] text-slate-950 font-black text-xs sm:text-sm shadow-xl shadow-lime-500/20 hover:scale-105 active:scale-95 transition-all">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.071.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.579 4.178 1.594 5.91l-1.693 6.183 6.356-1.667c1.677.915 3.599 1.438 5.643 1.438 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                            <span>Chat WhatsApp</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= SECTION 6: BAGAIMANA KAMI BEKERJA ================= -->
        <section id="cara-kerja" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="inline-block px-3.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider border border-blue-200 mb-3">
                    {{ $settings['how_badge'] ?? 'PROSES KERJA' }}
                </span>
                <h2 class="text-2xl sm:text-4xl font-black text-slate-900 leading-tight">
                    {{ $settings['how_title'] ?? 'Bagaimana Kami Bekerja' }}
                </h2>
                <p class="text-sm sm:text-base text-slate-600 mt-3 leading-relaxed font-medium">
                    {{ $settings['how_subtitle'] ?? 'Sistem 4 langkah teruji yang memastikan konten Anda viral tepat sasaran tanpa membuang waktu dan energi Anda.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($howSteps as $idx => $step)
                    <div class="p-6 rounded-2xl bg-white/95 border border-slate-200 shadow-sm hover:shadow-xl hover:border-purple-300 hover:shadow-purple-500/5 transition duration-200 flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-mono font-bold text-xs">
                                    {{ $step->subtitle ?: 'LANGKAH 0'.($idx+1) }}
                                </span>
                                <span class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                                    0{{ $idx + 1 }}
                                </span>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 leading-snug">
                                {{ $step->title }}
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed font-medium">
                                {{ $step->description }}
                            </p>
                        </div>
                        <div class="mt-5 pt-3 border-t border-slate-100">
                            <span class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>
                                Tahap {{ $idx + 1 }} dari 4
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- ================= SECTION 7: SIAP BANJIR JUTAAN VIEWS? (CTA CONVERSION) ================= -->
        <section class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="p-8 sm:p-14 lg:p-16 rounded-[2.5rem] bg-gradient-to-br from-[#080D1D] via-[#0E163B] to-[#1D1042] text-white border border-indigo-500/30 shadow-2xl shadow-indigo-950/60 relative overflow-hidden text-center">
                <!-- Ambient Internal Glows (Ungu Biru) -->
                <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-purple-600/30 blur-[100px] pointer-events-none"></div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full bg-blue-600/30 blur-[100px] pointer-events-none"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[300px] rounded-full bg-indigo-500/15 blur-[110px] pointer-events-none"></div>

                <div class="relative z-10 max-w-3xl mx-auto">
                    <!-- Badge (No Emojis, Clean SVG Sparkle Icon) -->
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gradient-to-r from-blue-500/20 via-purple-500/20 to-indigo-500/20 border border-purple-400/40 text-purple-200 text-xs font-bold uppercase tracking-widest mb-5 backdrop-blur-md shadow-sm">
                        <svg class="w-3.5 h-3.5 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        <span>{{ preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F700}-\x{1F77F}\x{1F780}-\x{1F7FF}\x{1F800}-\x{1F8FF}\x{1F900}-\x{1F9FF}\x{1FA00}-\x{1FA6F}\x{1FA70}-\x{1FAFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{FE00}-\x{FE0F}\x{1F1E6}-\x{1F1FF}]/u', '', $settings['cta_badge'] ?? 'AMBIL KESEMPATAN') }}</span>
                    </span>

                    <!-- Main CTA Heading (Clean Montserrat, Beautiful Ungu-Biru Gradient) -->
                    <h2 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.15] text-white">
                        {{ $settings['cta_title'] ?? 'Siap Banjir Jutaan Views?' }}
                    </h2>

                    <p class="text-sm sm:text-base text-slate-300/90 mt-5 max-w-2xl mx-auto leading-relaxed font-medium">
                        {{ $settings['cta_subtitle'] ?? 'Slot campaign bulanan kami dibatasi agar setiap brand mendapatkan alokasi kreator terbaik dan engagement maksimal. Hubungi kami sekarang sebelum kuota penuh.' }}
                    </p>

                    <!-- Feature Chips (NO EMOJIS! Replaced with clean, sleek SVG icons) -->
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                        @foreach($ctaChips as $chip)
                            @php
                                $cleanTitle = preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F700}-\x{1F77F}\x{1F780}-\x{1F7FF}\x{1F800}-\x{1F8FF}\x{1F900}-\x{1F9FF}\x{1FA00}-\x{1FA6F}\x{1FA70}-\x{1FAFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{FE00}-\x{FE0F}\x{1F1E6}-\x{1F1FF}]/u', '', $chip->title);
                                $cleanTitle = trim($cleanTitle);
                                $lowerTitle = strtolower($cleanTitle);
                            @endphp
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/[0.08] backdrop-blur-md border border-white/15 text-xs sm:text-sm font-semibold text-white/95 hover:bg-white/[0.14] hover:border-purple-400/40 transition duration-150 shadow-sm">
                                @if(str_contains($lowerTitle, 'slot') || str_contains($lowerTitle, 'terbatas'))
                                    <!-- Lightning Icon -->
                                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                @elseif(str_contains($lowerTitle, 'dashboard') || str_contains($lowerTitle, 'pantau') || str_contains($lowerTitle, 'realtime'))
                                    <!-- Chart Bar Analytics Icon -->
                                    <svg class="w-4 h-4 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                @elseif(str_contains($lowerTitle, 'garansi') || str_contains($lowerTitle, 'distribusi'))
                                    <!-- Shield Check Icon -->
                                    <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                @elseif(str_contains($lowerTitle, 'kreator') || str_contains($lowerTitle, 'aktif'))
                                    <!-- Users Community Icon -->
                                    <svg class="w-4 h-4 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                @elseif(str_contains($lowerTitle, 'niche') || str_contains($lowerTitle, 'bisnis'))
                                    <!-- Sparkles Icon -->
                                    <svg class="w-4 h-4 text-pink-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                                @endif
                                <span>{{ $cleanTitle }}</span>
                            </span>
                        @endforeach
                    </div>

                    <!-- CTA Button (Ungu Biru Gradient Glow) -->
                    <div class="mt-9">
                        <a href="{{ $settings['cta_button_url'] ?? 'https://wa.me/'.($settings['contact_whatsapp'] ?? '6281234567890') }}" target="_blank" class="inline-flex items-center justify-center px-8 sm:px-10 py-4 sm:py-5 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 hover:from-blue-500 hover:via-indigo-500 hover:to-purple-500 text-white rounded-2xl text-xs sm:text-sm font-black shadow-xl shadow-indigo-600/40 hover:shadow-2xl hover:shadow-indigo-600/50 hover:scale-[1.02] transition duration-200 group">
                            <span>{{ $settings['cta_button_text'] ?? 'Amankan Slot Campaign Anda Sekarang' }}</span>
                            <svg class="w-4 h-4 ml-2.5 group-hover:translate-x-1.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
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
        <footer class="relative z-10 border-t border-slate-200/80 bg-white/80 backdrop-blur-md py-10 px-4 sm:px-6 lg:px-8 text-slate-600">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-5">
                <div class="flex items-center gap-3">
                    @if($brandLogoUrl)
                        <img src="{{ $brandLogoUrl }}" alt="{{ $settings['brand_name'] ?? 'Kahfi Engagement' }}" class="h-8 w-auto max-w-[120px] object-contain rounded-lg">
                    @else
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 to-purple-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                            K
                        </div>
                    @endif
                    <span class="font-black text-slate-900 text-sm">
                        {{ $settings['brand_name'] ?? 'Kahfi Engagement' }}
                    </span>
                    <span class="text-xs text-slate-400 hidden sm:inline">| {{ $settings['brand_tagline'] ?? 'Influencer Campaign & Growth Management' }}</span>
                </div>

                <div class="flex items-center gap-6 text-xs font-bold text-slate-700">
                    <a href="#kenapa-gabung" class="hover:text-purple-600 transition">Kenapa Kami</a>
                    <a href="#portofolio" class="hover:text-purple-600 transition">Portofolio</a>
                    <a href="#klien" class="hover:text-purple-600 transition">Klien</a>
                    <a href="#cara-kerja" class="hover:text-purple-600 transition">Cara Kerja</a>
                    <a href="{{ route('login') }}" class="hover:text-purple-600 transition">Login</a>
                </div>

                <div class="text-xs text-slate-500 font-medium">
                    &copy; {{ date('Y') }} {{ $settings['brand_name'] ?? 'Kahfi Engagement' }}. All rights reserved.
                </div>
            </div>
        </footer>

    </body>
</html>
