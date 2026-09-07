@php
    $settings = $settings ?? (\Illuminate\Support\Facades\Schema::hasTable('landing_settings') ? \App\Models\LandingSetting::allKeyValues() : []);
    $clients = $clients ?? (\Illuminate\Support\Facades\Schema::hasTable('landing_items') ? \App\Models\LandingItem::ofType('client_logo')->active()->ordered()->get() : collect());
    $whyJoins = $whyJoins ?? (\Illuminate\Support\Facades\Schema::hasTable('landing_items') ? \App\Models\LandingItem::ofType('why_join')->active()->ordered()->get() : collect());
    $notFors = $notFors ?? (\Illuminate\Support\Facades\Schema::hasTable('landing_items') ? \App\Models\LandingItem::ofType('not_for')->active()->ordered()->get() : collect());
    $howSteps = $howSteps ?? (\Illuminate\Support\Facades\Schema::hasTable('landing_items') ? \App\Models\LandingItem::ofType('how_step')->active()->ordered()->get() : collect());
    $efficiencyCards = $efficiencyCards ?? (\Illuminate\Support\Facades\Schema::hasTable('landing_items') ? \App\Models\LandingItem::ofType('efficiency_card')->active()->ordered()->get() : collect());
    $ctaChips = $ctaChips ?? (\Illuminate\Support\Facades\Schema::hasTable('landing_items') ? \App\Models\LandingItem::ofType('cta_chip')->active()->ordered()->get() : collect());
    $portfolios = $portfolios ?? (\Illuminate\Support\Facades\Schema::hasTable('landing_items') ? \App\Models\LandingItem::ofType('portfolio')->active()->ordered()->get() : collect());
    $paymentProofs = $paymentProofs ?? (\Illuminate\Support\Facades\Schema::hasTable('landing_items') ? \App\Models\LandingItem::ofType('payment_proof')->active()->ordered()->get() : collect());
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $settings['brand_name'] ?? 'Kahfi Engagement' }} - {{ $settings['brand_tagline'] ?? 'Agency Engagement & Influencer Campaign Management' }}</title>
        
        <!-- Google Fonts: Montserrat -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        
        <!-- Vite Styles & Alpine JS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @php
            $brandLogoUrl = \App\Models\LandingSetting::brandLogoUrl();
        @endphp
        @if($brandLogoUrl)
            <link rel="icon" type="image/png" href="{{ $brandLogoUrl }}">
        @endif
        
        <style>
            body {
                font-family: 'Montserrat', sans-serif;
            }
            .bg-grid-pattern {
                background-size: 48px 48px;
                background-image: radial-gradient(circle, rgba(37, 99, 235, 0.07) 1px, transparent 1px);
            }
            @keyframes marquee {
                0% { transform: translateX(0%); }
                100% { transform: translateX(-50%); }
            }
            .animate-marquee {
                display: flex;
                width: max-content;
                animation: marquee 28s linear infinite;
            }
            .animate-marquee:hover {
                animation-play-state: paused;
            }
            .glass-panel {
                background: rgba(255, 255, 255, 0.82);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
            }
        </style>
    </head>
    <body class="bg-[#F8FAFC] text-slate-800 antialiased min-h-screen relative overflow-x-hidden bg-grid-pattern selection:bg-blue-600 selection:text-white">
        
        <!-- Background Ambient Glows -->
        <div class="fixed top-0 right-0 w-[550px] h-[550px] rounded-full bg-blue-500/10 blur-[130px] pointer-events-none z-0"></div>
        <div class="fixed top-[30%] -left-32 w-[600px] h-[600px] rounded-full bg-indigo-500/10 blur-[140px] pointer-events-none z-0"></div>
        <div class="fixed bottom-0 right-[20%] w-[500px] h-[500px] rounded-full bg-blue-600/10 blur-[130px] pointer-events-none z-0"></div>

        <!-- ================= NAVBAR ================= -->
        <header class="sticky top-0 z-50 w-full px-6 py-4 lg:px-12 flex items-center justify-between border-b border-slate-200/80 glass-panel shadow-sm transition-all duration-200">
            <a href="/" class="flex items-center gap-3 group">
                @if($brandLogoUrl)
                    <img src="{{ $brandLogoUrl }}" alt="{{ $settings['brand_name'] ?? 'Kahfi Engagement' }}" class="h-10 w-auto max-w-[150px] object-contain rounded-xl shadow-sm group-hover:scale-105 transition-transform">
                @else
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                @endif
                <div class="flex flex-col">
                    <span class="text-xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700">
                        {{ $settings['brand_name'] ?? 'Kahfi Engagement' }}
                    </span>
                    <span class="text-[10px] font-semibold text-slate-600 tracking-wider -mt-1 hidden sm:block">GROWTH & ENGAGEMENT</span>
                </div>
            </a>
            
            <nav class="hidden xl:flex items-center gap-7 text-xs font-bold text-slate-700 uppercase tracking-wide">
                <a href="#kenapa-gabung" class="hover:text-blue-600 transition-colors">Kenapa Kami</a>
                <a href="#bukan-untuk-semua" class="hover:text-blue-600 transition-colors">Kualifikasi</a>
                <a href="#portofolio" class="hover:text-blue-600 transition-colors">Portofolio</a>
                <a href="#klien" class="hover:text-blue-600 transition-colors">Klien</a>
                <a href="#cara-kerja" class="hover:text-blue-600 transition-colors">Cara Kerja</a>
                <a href="#efisiensi" class="hover:text-blue-600 transition-colors">Efisiensi Biaya</a>
                <a href="#bukti-payment" class="hover:text-blue-600 transition-colors">Bukti Payment</a>
            </nav>
            
            <div class="flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-xs sm:text-sm font-bold shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:-translate-y-0.5 active:translate-y-0 transition duration-200 flex items-center gap-2">
                            <span>Dashboard</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs sm:text-sm font-bold text-slate-700 hover:text-blue-600 transition px-3 py-2">
                            Masuk
                        </a>
                        <a href="{{ $settings['hero_primary_cta_url'] ?? 'https://wa.me/'.($settings['contact_whatsapp'] ?? '6281234567890') }}" target="_blank" class="px-4 sm:px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-blue-500/25 hover:shadow-blue-500/40 hover:-translate-y-0.5 transition duration-200 flex items-center gap-1.5">
                            <span>Konsultasi</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    @endauth
                @endif
            </div>
        </header>

        <!-- ================= SECTION 1: HERO SECTION ================= -->
        <section class="relative z-10 max-w-7xl mx-auto px-6 pt-16 pb-20 lg:pt-24 lg:pb-28 text-center">
            <!-- Badge Hook Pill -->
            <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-blue-50 border border-blue-200/80 text-blue-700 text-xs sm:text-sm font-bold shadow-sm mb-6 animate-fade-in">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></span>
                <span>{{ $settings['hero_badge'] ?? '🚀 Sistem Distribusi & Engagement Organik Terbesar' }}</span>
            </div>

            <!-- Headline -->
            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-black text-slate-900 leading-[1.12] tracking-tight max-w-5xl mx-auto">
                @php
                    $headline = $settings['hero_headline'] ?? 'Banjir Jutaan Views & Ribuan Followers Tanpa Iklan Mahal';
                    // Stylize with gradient accent
                    $words = explode(' ', $headline);
                    $total = count($words);
                    $firstPart = implode(' ', array_slice($words, 0, max(1, (int)($total * 0.5))));
                    $secondPart = implode(' ', array_slice($words, max(1, (int)($total * 0.5))));
                @endphp
                {{ $firstPart }}
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600">
                    {{ $secondPart }}
                </span>
            </h1>

            <!-- Subheadline -->
            <p class="mt-6 text-base sm:text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed font-medium">
                {{ $settings['hero_subheadline'] ?? 'Tingkatkan brand awareness, trust, dan penjualan Anda secara eksponensial melalui jaringan ratusan kreator organik terverifikasi. Terpantau realtime dalam 1 dashboard.' }}
            </p>

            <!-- Dual CTA Buttons -->
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ $settings['hero_primary_cta_url'] ?? '#' }}" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl text-base font-extrabold shadow-xl shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-0.5 active:translate-y-0 transition duration-200 group">
                    <span>{{ $settings['hero_primary_cta_text'] ?? 'Mulai Campaign Sekarang' }}</span>
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
                <a href="{{ $settings['hero_secondary_cta_url'] ?? '#portofolio' }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-white border-2 border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300 rounded-2xl text-base font-bold shadow-sm transition duration-200">
                    {{ $settings['hero_secondary_cta_text'] ?? 'Lihat Portofolio Kami' }}
                </a>
            </div>

            <!-- Stats Bar -->
            <div class="mt-16 max-w-4xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-6 p-6 sm:p-8 rounded-3xl bg-white border border-slate-200 shadow-xl shadow-slate-200/40">
                <div class="text-center sm:border-r border-slate-100">
                    <div class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                        {{ $settings['hero_stats_creators'] ?? '500+' }}
                    </div>
                    <div class="text-xs sm:text-sm font-semibold text-slate-500 mt-1">
                        {{ $settings['hero_stats_creators_label'] ?? 'Kreator Aktif' }}
                    </div>
                </div>
                <div class="text-center sm:border-r border-slate-100">
                    <div class="text-3xl sm:text-4xl font-black bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 tracking-tight">
                        {{ $settings['hero_stats_views'] ?? '150M+' }}
                    </div>
                    <div class="text-xs sm:text-sm font-semibold text-slate-500 mt-1">
                        {{ $settings['hero_stats_views_label'] ?? 'Total Views Terdistribusi' }}
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                        {{ $settings['hero_stats_brands'] ?? '120+' }}
                    </div>
                    <div class="text-xs sm:text-sm font-semibold text-slate-500 mt-1">
                        {{ $settings['hero_stats_brands_label'] ?? 'Brand Puas' }}
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= SECTION 2: CLIENT / BRAND PARTNERS MARQUEE ================= -->
        <section id="klien" class="relative z-10 py-12 border-y border-slate-200 bg-white/70 overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 mb-6 text-center">
                <p class="text-xs font-extrabold uppercase tracking-widest text-blue-600">
                    {{ $settings['client_badge'] ?? 'BRAND TERPERCAYA' }}
                </p>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">
                    {{ $settings['client_title'] ?? 'Dipercaya Oleh Brand & Pebisnis Terkemuka' }}
                </h3>
            </div>

            <div class="relative w-full overflow-hidden">
                <!-- Gradients Mask Left & Right -->
                <div class="absolute left-0 top-0 bottom-0 w-24 bg-gradient-to-r from-white via-white/80 to-transparent z-10 pointer-events-none"></div>
                <div class="absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-l from-white via-white/80 to-transparent z-10 pointer-events-none"></div>

                <div class="animate-marquee gap-8 items-center py-3">
                    @php
                        // Duplicate array to ensure smooth infinite loop
                        $allClients = ($clients && $clients->isNotEmpty()) ? $clients->concat($clients) : ($clients ?? collect());
                    @endphp
                    @foreach($allClients as $client)
                        <div class="px-6 py-3 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-3.5 shrink-0 hover:border-blue-400 hover:shadow-md transition">
                            @if($client->image_url)
                                <img src="{{ $client->image_url }}" alt="{{ $client->title }}" class="h-8 max-w-[120px] object-contain">
                            @else
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 font-black text-xs flex items-center justify-center">
                                    {{ strtoupper(substr($client->title, 0, 2)) }}
                                </div>
                            @endif
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-800 whitespace-nowrap">{{ $client->title }}</span>
                                @if($client->subtitle)
                                    <span class="text-[10px] text-slate-600 font-medium whitespace-nowrap">{{ $client->subtitle }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ================= SECTION 3: KENAPA KAMU HARUS GABUNG ================= -->
        <section id="kenapa-gabung" class="relative z-10 max-w-7xl mx-auto px-6 py-20 lg:py-28">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-3.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-extrabold uppercase tracking-wider border border-blue-100 mb-3">
                    {{ $settings['why_join_badge'] ?? 'KEUNGGULAN KAMI' }}
                </span>
                <h2 class="text-3xl sm:text-5xl font-black text-slate-900 leading-tight">
                    {{ $settings['why_join_title'] ?? 'Kenapa Kamu Harus Gabung Kahfi Engagement?' }}
                </h2>
                <p class="text-base sm:text-lg text-slate-600 mt-4 leading-relaxed font-medium">
                    {{ $settings['why_join_subtitle'] ?? 'Kami merevolusi cara brand berpromosi: dari iklan berbayar yang kian mahal ke gelombang konten organik yang autentik dan dipercaya audiens.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach($whyJoins as $idx => $item)
                    <div class="p-8 rounded-3xl bg-white border border-slate-200 shadow-lg shadow-slate-100 hover:shadow-xl hover:border-blue-300 hover:-translate-y-1 transition duration-300 flex flex-col justify-between group">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-black text-lg mb-6 shadow-md shadow-blue-500/25 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h3 class="text-xl font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors">
                                {{ $item->title }}
                            </h3>
                            <p class="text-sm text-slate-600 mt-3 leading-relaxed font-medium">
                                {{ $item->description }}
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-blue-600">
                            <span>Fitur Terverifikasi</span>
                            <span class="text-slate-600 font-mono">0{{ $idx + 1 }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- ================= SECTION 4: BUKAN UNTUK SEMUA ORANG ================= -->
        <section id="bukan-untuk-semua" class="relative z-10 max-w-7xl mx-auto px-6 py-20 lg:py-24">
            <div class="p-8 sm:p-14 rounded-3xl bg-gradient-to-b from-rose-50/70 to-slate-50 border-2 border-rose-200/80 shadow-xl shadow-rose-100/40">
                <div class="text-center max-w-3xl mx-auto mb-12">
                    <span class="inline-block px-3.5 py-1 rounded-full bg-rose-100 text-rose-700 text-xs font-extrabold uppercase tracking-wider border border-rose-200 mb-3">
                        {{ $settings['not_for_badge'] ?? 'KUALIFIKASI KLIEN' }}
                    </span>
                    <h2 class="text-3xl sm:text-5xl font-black text-slate-900 leading-tight">
                        {{ $settings['not_for_title'] ?? 'Bukan Untuk Semua Orang' }}
                    </h2>
                    <h3 class="text-2xl sm:text-3xl font-black text-rose-600 mt-2">
                        {{ $settings['not_for_subtitle'] ?? 'Jangan Daftar Kalau Kamu:' }}
                    </h3>
                    <p class="text-sm sm:text-base text-slate-600 mt-3 font-medium">
                        {{ $settings['not_for_description'] ?? 'Kami hanya bekerja dengan brand dan pebisnis yang serius ingin membangun aset awareness jangka panjang dan siap scale up kapasitas penjualan.' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($notFors as $item)
                        <div class="p-6 rounded-2xl bg-white border border-rose-200 shadow-sm flex items-start gap-4 hover:border-rose-300 transition">
                            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-black text-lg shrink-0 shadow-inner">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-rose-950">
                                    {{ $item->title }}
                                </h4>
                                <p class="text-xs sm:text-sm text-slate-600 mt-1.5 leading-relaxed font-medium">
                                    {{ $item->description }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ================= SECTION 5: PORTOFOLIO KAMI ================= -->
        <section id="portofolio" class="relative z-10 max-w-7xl mx-auto px-6 py-20 lg:py-28">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="inline-block px-3.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-extrabold uppercase tracking-wider border border-blue-100 mb-3">
                    {{ $settings['portfolio_badge'] ?? 'HASIL NYATA' }}
                </span>
                <h2 class="text-3xl sm:text-5xl font-black text-slate-900 leading-tight">
                    {{ $settings['portfolio_title'] ?? 'Portofolio Kami' }}
                </h2>
                <p class="text-base sm:text-lg text-slate-600 mt-4 leading-relaxed font-medium">
                    {{ $settings['portfolio_subtitle'] ?? 'Bukti distribusi video organik dengan jutaan views di TikTok dan Instagram Reels tanpa biaya ads.' }}
                </p>

                <!-- Total Views Highlight Pill -->
                <div class="mt-8 inline-flex flex-col sm:flex-row items-center gap-3 px-6 py-3.5 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-xl shadow-blue-500/25">
                    <span class="text-2xl sm:text-3xl font-black tracking-tight">
                        {{ $settings['portfolio_total_views'] ?? '150.000.000+' }}
                    </span>
                    <span class="text-xs sm:text-sm font-bold text-blue-100">
                        {{ $settings['portfolio_views_label'] ?? 'Total Views Berhasil Kami Hasilkan untuk Klien' }}
                    </span>
                </div>
            </div>

            <!-- Portfolio Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($portfolios as $portfolio)
                    <div class="rounded-3xl bg-white border border-slate-200 overflow-hidden shadow-lg shadow-slate-100 hover:shadow-2xl hover:border-blue-300 hover:-translate-y-1.5 transition duration-300 flex flex-col group">
                        <!-- Thumbnail / Image Container -->
                        <div class="h-56 bg-slate-900 relative overflow-hidden flex items-center justify-center">
                            @if($portfolio->image_url)
                                <img src="{{ $portfolio->image_url }}" alt="{{ $portfolio->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-tr from-slate-900 via-indigo-950 to-blue-950 flex flex-col items-center justify-center p-6 text-center text-white relative">
                                    <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center mb-3">
                                        <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <span class="text-xs font-bold text-blue-300 uppercase tracking-widest">{{ $portfolio->extra_meta['platform'] ?? 'Video Campaign' }}</span>
                                    <span class="text-base font-bold mt-1 max-w-[200px] truncate">{{ $portfolio->title }}</span>
                                </div>
                            @endif

                            <!-- Floating Views Count Badge -->
                            <div class="absolute top-3 right-3 px-3 py-1 rounded-xl bg-black/75 backdrop-blur-md text-white font-extrabold text-xs shadow-lg border border-white/20 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>{{ $portfolio->extra_meta['views'] ?? 'Viral' }}</span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <p class="text-xs font-bold text-blue-600 uppercase tracking-wider">
                                    {{ $portfolio->subtitle }}
                                </p>
                                <h3 class="text-lg font-extrabold text-slate-900 mt-1 group-hover:text-blue-600 transition-colors">
                                    {{ $portfolio->title }}
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 mt-2.5 leading-relaxed font-medium">
                                    {{ $portfolio->description }}
                                </p>
                            </div>

                            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-700 flex items-center gap-1">
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
        <section id="cara-kerja" class="relative z-10 max-w-7xl mx-auto px-6 py-20 lg:py-28">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-3.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-extrabold uppercase tracking-wider border border-blue-100 mb-3">
                    {{ $settings['how_badge'] ?? 'PROSES KERJA' }}
                </span>
                <h2 class="text-3xl sm:text-5xl font-black text-slate-900 leading-tight">
                    {{ $settings['how_title'] ?? 'Bagaimana Kami Bekerja' }}
                </h2>
                <p class="text-base sm:text-lg text-slate-600 mt-4 leading-relaxed font-medium">
                    {{ $settings['how_subtitle'] ?? 'Sistem 4 langkah teruji yang memastikan konten Anda viral tepat sasaran tanpa membuang waktu dan energi Anda.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 relative">
                @foreach($howSteps as $idx => $step)
                    <div class="p-8 rounded-3xl bg-white border border-slate-200 shadow-lg shadow-slate-100 hover:shadow-xl hover:border-blue-300 transition duration-300 flex flex-col justify-between relative group">
                        <!-- Step Badge -->
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <span class="px-3 py-1 rounded-xl bg-blue-50 text-blue-700 font-mono font-black text-xs">
                                    {{ $step->subtitle ?: 'LANGKAH 0'.($idx+1) }}
                                </span>
                                <span class="text-3xl font-black text-slate-200 group-hover:text-blue-600 transition-colors">
                                    0{{ $idx + 1 }}
                                </span>
                            </div>
                            <h3 class="text-lg font-extrabold text-slate-900 leading-snug">
                                {{ $step->title }}
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600 mt-3 leading-relaxed font-medium">
                                {{ $step->description }}
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100">
                            <span class="text-[11px] font-bold text-slate-600 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                Tahap {{ $idx + 1 }} dari 4
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- ================= SECTION 7: ANALISIS EFISIENSI (DISTRIBUSI ORGANIK VS AGENCY) ================= -->
        <section id="efisiensi" class="relative z-10 max-w-7xl mx-auto px-6 py-20 lg:py-28">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-3.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-extrabold uppercase tracking-wider border border-blue-100 mb-3">
                    {{ $settings['efficiency_badge'] ?? 'PERBANDINGAN BIAYA' }}
                </span>
                <h2 class="text-3xl sm:text-5xl font-black text-slate-900 leading-tight">
                    {{ $settings['efficiency_title'] ?? 'ANALISIS EFISIENSI: DISTRIBUSI ORGANIK VS AGENCY' }}
                </h2>
                <p class="text-base sm:text-lg text-slate-600 mt-4 leading-relaxed font-medium">
                    {{ $settings['efficiency_subtitle'] ?? 'Bandingkan sendiri mengapa brand-brand cerdas mulai beralih dari bakar uang iklan konvensional ke distribusi engagement organik bersama Kahfi Engagement.' }}
                </p>
            </div>

            <!-- Comparison Cards Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
                @foreach($efficiencyCards as $card)
                    @php
                        $isWinner = !empty($card->extra_meta['is_winner']);
                    @endphp
                    <div class="rounded-3xl p-8 flex flex-col justify-between transition-all duration-300 relative {{ $isWinner ? 'bg-gradient-to-b from-blue-900 via-slate-900 to-indigo-950 text-white shadow-2xl shadow-blue-500/20 ring-4 ring-blue-500/30 -translate-y-2 lg:-translate-y-4' : 'bg-white border border-slate-200 text-slate-800 shadow-lg shadow-slate-100' }}">
                        
                        <div>
                            @if($isWinner)
                                <div class="inline-block px-3.5 py-1 rounded-full bg-gradient-to-r from-blue-500 to-emerald-400 text-white text-xs font-black uppercase tracking-wider mb-4 shadow-md">
                                    {{ $card->extra_meta['badge'] ?? '⭐ REKOMENDASI PEMENANG' }}
                                </div>
                            @endif

                            <h3 class="text-2xl font-black {{ $isWinner ? 'text-white' : 'text-slate-900' }}">
                                {{ $card->title }}
                            </h3>
                            <p class="text-xs sm:text-sm font-semibold {{ $isWinner ? 'text-blue-200' : 'text-slate-500' }} mt-1">
                                {{ $card->subtitle }}
                            </p>

                            <!-- Cost Box -->
                            <div class="my-6 p-5 rounded-2xl {{ $isWinner ? 'bg-white/10 border border-white/15 backdrop-blur-md' : 'bg-slate-50 border border-slate-100' }}">
                                <span class="text-xs font-bold uppercase tracking-wider {{ $isWinner ? 'text-blue-200' : 'text-slate-500' }}">Estimasi Biaya</span>
                                <div class="text-2xl sm:text-3xl font-black mt-1 {{ $isWinner ? 'text-white' : 'text-slate-900' }}">
                                    {{ $card->extra_meta['cost_range'] ?? '-' }}
                                </div>
                                <div class="mt-2 text-xs font-extrabold {{ $isWinner ? 'text-emerald-300' : 'text-blue-600' }}">
                                    Estimasi CPM: {{ $card->extra_meta['cpm'] ?? '-' }}
                                </div>
                            </div>

                            <p class="text-xs sm:text-sm leading-relaxed font-medium mb-6 {{ $isWinner ? 'text-slate-300' : 'text-slate-600' }}">
                                {{ $card->description }}
                            </p>

                            <!-- Pros -->
                            @if(!empty($card->extra_meta['pros']))
                                <div class="space-y-2.5 mb-5">
                                    <div class="text-[11px] font-black uppercase tracking-wider {{ $isWinner ? 'text-emerald-400' : 'text-emerald-600' }}">
                                        Kelebihan:
                                    </div>
                                    @foreach($card->extra_meta['pros'] as $pro)
                                        <div class="flex items-start gap-2 text-xs sm:text-sm font-medium {{ $isWinner ? 'text-slate-200' : 'text-slate-700' }}">
                                            <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            <span>{{ $pro }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Cons -->
                            @if(!empty($card->extra_meta['cons']))
                                <div class="space-y-2.5">
                                    <div class="text-[11px] font-black uppercase tracking-wider text-rose-500">
                                        Kekurangan:
                                    </div>
                                    @foreach($card->extra_meta['cons'] as $con)
                                        <div class="flex items-start gap-2 text-xs sm:text-sm font-medium text-slate-500">
                                            <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            <span>{{ $con }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- CTA Button in Card -->
                        <div class="mt-8 pt-6 border-t {{ $isWinner ? 'border-white/10' : 'border-slate-100' }}">
                            @if($isWinner)
                                <a href="{{ $settings['hero_primary_cta_url'] ?? '#' }}" target="_blank" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white font-extrabold text-sm text-center block shadow-lg shadow-blue-500/30 transition">
                                    Pilih Distribusi Organik Sekarang
                                </a>
                            @else
                                <div class="text-center text-xs font-semibold text-slate-600">
                                    Metode Konvensional
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Footnote -->
            <p class="text-center text-xs text-slate-600 mt-10 max-w-2xl mx-auto italic">
                {{ $settings['efficiency_footnote'] ?? '*Perhitungan berdasarkan data rata-rata CPM industri Q1 2026. Konten organik tetap aktif selamanya di feed kreator (Evergreen Traffic).' }}
            </p>
        </section>

        <!-- ================= SECTION 8: BUKTI PAYMENT ================= -->
        <section id="bukti-payment" class="relative z-10 max-w-7xl mx-auto px-6 py-20 lg:py-28" x-data="{
            currentIndex: 0,
            total: {{ count($paymentProofs) }},
            next() {
                this.currentIndex = (this.currentIndex + 1) % Math.max(1, this.total - 1);
            },
            prev() {
                this.currentIndex = (this.currentIndex - 1 + Math.max(1, this.total - 1)) % Math.max(1, this.total - 1);
            }
        }">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
                <div>
                    <span class="inline-block px-3.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-extrabold uppercase tracking-wider border border-blue-100 mb-3">
                        {{ $settings['payment_badge'] ?? 'TRANSPARANSI KREATOR' }}
                    </span>
                    <h2 class="text-3xl sm:text-5xl font-black text-slate-900 leading-tight">
                        {{ $settings['payment_title'] ?? 'Bukti Payment' }}
                    </h2>
                    <p class="text-base text-slate-600 mt-2 font-medium max-w-xl">
                        {{ $settings['payment_subtitle'] ?? 'Bukti nyata komitmen kami membayar ratusan kreator tepat waktu, transparan, dan profesional.' }}
                    </p>
                </div>

                <!-- Carousel Controls -->
                <div class="flex items-center gap-3">
                    <button @click="prev()" class="w-11 h-11 rounded-2xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300 flex items-center justify-center shadow-sm active:scale-95 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button @click="next()" class="w-11 h-11 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:opacity-90 flex items-center justify-center shadow-md shadow-blue-500/25 active:scale-95 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Proof Cards Slider -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($paymentProofs as $proof)
                    <div class="rounded-3xl bg-white border border-slate-200 overflow-hidden shadow-lg shadow-slate-100 hover:shadow-xl hover:border-blue-300 transition duration-300 flex flex-col justify-between">
                        <!-- Image / Proof Visual -->
                        <div class="h-48 bg-slate-900 relative overflow-hidden flex items-center justify-center">
                            @if($proof->image_url)
                                <img src="{{ $proof->image_url }}" alt="{{ $proof->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-tr from-slate-950 via-slate-900 to-indigo-950 flex flex-col items-center justify-center p-6 text-center text-white">
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <span class="text-xs font-bold text-slate-300">{{ $proof->subtitle }}</span>
                                </div>
                            @endif

                            <div class="absolute bottom-3 left-3 px-3 py-1 rounded-xl bg-emerald-600/90 backdrop-blur-md text-white font-mono font-black text-xs shadow-md">
                                {{ $proof->extra_meta['amount'] ?? 'Lunas' }}
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <h4 class="font-extrabold text-slate-900 text-sm">
                                    {{ $proof->title }}
                                </h4>
                                <p class="text-xs text-slate-500 mt-1 font-medium">
                                    {{ $proof->subtitle }}
                                </p>
                                <p class="text-xs text-slate-600 mt-2 line-clamp-2">
                                    {{ $proof->description }}
                                </p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-500">
                                <span>{{ $proof->extra_meta['date'] ?? '2026' }}</span>
                                <span class="text-emerald-600 font-bold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    {{ $proof->extra_meta['status'] ?? 'Berhasil' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- ================= SECTION 9: SIAP BANJIR JUTAAN VIEWS? (CTA CONVERSION) ================= -->
        <section class="relative z-10 max-w-7xl mx-auto px-6 py-20 lg:py-24">
            <div class="p-8 sm:p-16 rounded-3xl bg-gradient-to-b from-slate-900 via-[#0B132B] to-slate-950 text-white border border-slate-800 shadow-2xl relative overflow-hidden text-center">
                <!-- Background decorative glow -->
                <div class="absolute top-0 right-1/4 w-96 h-96 rounded-full bg-blue-600/20 blur-[100px] pointer-events-none"></div>
                <div class="absolute bottom-0 left-1/4 w-96 h-96 rounded-full bg-indigo-600/20 blur-[100px] pointer-events-none"></div>

                <div class="relative z-10 max-w-3xl mx-auto">
                    <span class="inline-block px-4 py-1.5 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-extrabold uppercase tracking-widest mb-4">
                        {{ $settings['cta_badge'] ?? 'AMBIL KESEMPATAN' }}
                    </span>

                    <h2 class="text-4xl sm:text-6xl font-black tracking-tight leading-tight">
                        {{ $settings['cta_title'] ?? 'Siap Banjir Jutaan Views?' }}
                    </h2>

                    <p class="text-base sm:text-lg text-slate-300 mt-4 leading-relaxed font-medium">
                        {{ $settings['cta_subtitle'] ?? 'Slot campaign bulanan kami dibatasi agar setiap brand mendapatkan alokasi kreator terbaik dan engagement maksimal. Hubungi kami sekarang sebelum kuota penuh.' }}
                    </p>

                    <!-- Feature Chips -->
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                        @foreach($ctaChips as $chip)
                            <span class="px-4 py-2 rounded-full bg-white/10 border border-white/10 text-xs sm:text-sm font-bold text-white backdrop-blur-md">
                                {{ $chip->title }}
                            </span>
                        @endforeach
                    </div>

                    <!-- CTA Button -->
                    <div class="mt-10">
                        <a href="{{ $settings['cta_button_url'] ?? 'https://wa.me/'.($settings['contact_whatsapp'] ?? '6281234567890') }}" target="_blank" class="inline-flex items-center justify-center px-9 py-4 bg-gradient-to-r from-blue-500 via-indigo-500 to-blue-600 hover:from-blue-600 hover:to-indigo-700 text-white rounded-2xl text-base sm:text-lg font-extrabold shadow-2xl shadow-blue-500/40 hover:-translate-y-1 transition duration-200 group">
                            <span>{{ $settings['cta_button_text'] ?? 'Amankan Slot Campaign Anda Sekarang' }}</span>
                            <svg class="w-5 h-5 ml-2.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= FLOATING WHATSAPP CTA ================= -->
        <aside aria-label="WhatsApp Contact" class="fixed bottom-6 right-6 z-50 flex items-center gap-3 group">
            <a href="https://wa.me/{{ $settings['contact_whatsapp'] ?? '6281234567890' }}?text=Halo%20Admin%20{{ urlencode($settings['brand_name'] ?? 'Kahfi Engagement') }},%20saya%20tertarik%20bekerjasama" target="_blank" class="flex items-center gap-3 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full shadow-2xl shadow-emerald-600/40 hover:scale-105 transition-all duration-300">
                <div class="relative">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.071.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.579 4.178 1.594 5.91l-1.693 6.183 6.356-1.667c1.677.915 3.599 1.438 5.643 1.438 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-white border-2 border-emerald-600 animate-ping"></span>
                </div>
                <span class="text-xs sm:text-sm font-extrabold pr-1">{{ $settings['floating_wa_text'] ?? 'Konsultasi Campaign Sekarang' }}</span>
            </a>
        </aside>

        <!-- ================= FOOTER ================= -->
        <footer class="relative z-10 border-t border-slate-200 bg-white py-12 px-6 lg:px-12 text-slate-600">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    @if($brandLogoUrl)
                        <img src="{{ $brandLogoUrl }}" alt="{{ $settings['brand_name'] ?? 'Kahfi Engagement' }}" class="h-8 w-auto max-w-[120px] object-contain rounded-lg shadow-sm">
                    @else
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-black text-sm">
                            K
                        </div>
                    @endif
                    <span class="font-extrabold text-slate-900 text-base">
                        {{ $settings['brand_name'] ?? 'Kahfi Engagement' }}
                    </span>
                    <span class="text-xs text-slate-600 hidden sm:inline">| {{ $settings['brand_tagline'] ?? 'Influencer Campaign & Growth Management' }}</span>
                </div>

                <div class="flex items-center gap-6 text-xs font-bold text-slate-700">
                    <a href="#kenapa-gabung" class="hover:text-blue-600 transition">Kenapa Kami</a>
                    <a href="#portofolio" class="hover:text-blue-600 transition">Portofolio</a>
                    <a href="#efisiensi" class="hover:text-blue-600 transition">Efisiensi Biaya</a>
                    <a href="{{ route('login') }}" class="hover:text-blue-600 transition">Login Creator/Client</a>
                </div>

                <div class="text-xs text-slate-600 font-medium">
                    &copy; {{ date('Y') }} {{ $settings['brand_name'] ?? 'Kahfi Engagement' }}. All rights reserved.
                </div>
            </div>
        </footer>

    </body>
</html>
