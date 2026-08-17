<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Kahfi Engagement - Modern Creator Campaign Management</title>
        
        <!-- Google Fonts: Montserrat -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        
        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Montserrat', sans-serif;
            }
            .bg-grid-pattern {
                background-size: 40px 40px;
                background-image: radial-gradient(circle, rgba(99, 102, 241, 0.05) 1px, transparent 1px);
            }
        </style>
    </head>
    <body class="bg-[#F8FAFC] text-slate-800 antialiased min-h-screen relative overflow-x-hidden bg-grid-pattern">
        
        <!-- Background Decorative Glows -->
        <div class="absolute top-0 right-0 w-[50%] h-[500px] rounded-full bg-blue-500/10 blur-[120px] pointer-events-none z-0"></div>
        <div class="absolute top-[400px] left-0 w-[40%] h-[600px] rounded-full bg-indigo-500/10 blur-[130px] pointer-events-none z-0"></div>
        
        <!-- Navbar -->
        <header class="relative z-10 w-full px-6 py-5 lg:px-16 flex items-center justify-between border-b border-slate-100 bg-white/70 backdrop-blur-md sticky top-0">
            <a href="/" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <span class="text-xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                    Kahfi Engagement
                </span>
            </a>
            
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#features" class="hover:text-blue-600 transition-colors">Fitur Utama</a>
                <a href="#stats" class="hover:text-blue-600 transition-colors">Statistik</a>
                <a href="#about" class="hover:text-blue-600 transition-colors font-medium">Tentang Platform</a>
            </nav>
            
            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:-translate-y-0.5 active:translate-y-0 transition duration-200">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-slate-700 hover:text-blue-600 transition-colors px-4 py-2">
                            Masuk
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2.5 bg-slate-900 text-white rounded-xl text-sm font-bold shadow-md hover:bg-slate-800 transition">
                                Daftar
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative z-10 max-w-7xl mx-auto px-6 pt-16 pb-24 lg:px-8 flex flex-col lg:flex-row items-center gap-12">
            <div class="flex-1 text-center lg:text-left space-y-6">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-100">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    Sistem Manajemen Campaign Terintegrasi
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 leading-tight tracking-tight">
                    Analisis Konten Kreator Lebih <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">Mudah & Akurat</span>
                </h1>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-medium">
                    Pantau kinerja campaign influencer Anda secara realtime. Tarik metrik interaksi (views, likes, comments) secara otomatis dari platform TikTok dan Instagram tanpa batas.
                </p>
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-bold shadow-xl shadow-blue-500/25 hover:shadow-blue-500/40 hover:-translate-y-0.5 active:translate-y-0 transition duration-200">
                            Masuk Dashboard
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-bold shadow-xl shadow-blue-500/25 hover:shadow-blue-500/40 hover:-translate-y-0.5 active:translate-y-0 transition duration-200">
                            Mulai Sekarang
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                        <a href="#features" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl font-bold shadow-sm transition">
                            Pelajari Fitur
                        </a>
                    @endauth
                </div>
            </div>
            
            <!-- Graphic Element / Right Side -->
            <div class="flex-1 w-full max-w-lg lg:max-w-none relative">
                <div class="absolute inset-0 bg-gradient-to-tr from-blue-600/20 to-indigo-600/20 rounded-3xl blur-[30px] -z-10"></div>
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-2xl relative overflow-hidden">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                        <div class="flex gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-red-400"></span>
                            <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                            <span class="w-3 h-3 rounded-full bg-green-400"></span>
                        </div>
                        <span class="text-xs text-slate-400 font-semibold uppercase">Realtime Analytics Preview</span>
                    </div>
                    
                    <!-- Stats Card inside Graphic -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Total Views</p>
                            <h4 class="text-2xl font-black text-blue-600 mt-1">1.84M</h4>
                            <p class="text-[10px] text-emerald-500 font-semibold mt-1 flex items-center gap-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                +14.2% bulan ini
                            </p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Total Engagement</p>
                            <h4 class="text-2xl font-black text-indigo-600 mt-1">294.6K</h4>
                            <p class="text-[10px] text-emerald-500 font-semibold mt-1 flex items-center gap-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                +8.7% bulan ini
                            </p>
                        </div>
                    </div>
                    
                    <!-- Dummy Progress Rows -->
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs font-bold">
                                <span class="text-slate-700">TikTok Campaign (Active)</span>
                                <span class="text-blue-600">84%</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full" style="width: 84%"></div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs font-bold">
                                <span class="text-slate-700">Instagram Campaign (Processing)</span>
                                <span class="text-indigo-600">62%</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500 rounded-full" style="width: 62%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section id="stats" class="relative z-10 max-w-7xl mx-auto px-6 py-12 lg:px-8 border-y border-slate-200/60 bg-white/40 backdrop-blur-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="space-y-2">
                    <h3 class="text-4xl lg:text-5xl font-black text-blue-600">99.8%</h3>
                    <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Akurasi Data Scraping</p>
                </div>
                <div class="space-y-2 border-y md:border-y-0 md:border-x border-slate-200 py-6 md:py-0">
                    <h3 class="text-4xl lg:text-5xl font-black text-indigo-600">&lt; 3 Detik</h3>
                    <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Lama Pemrosesan Konten</p>
                </div>
                <div class="space-y-2">
                    <h3 class="text-4xl lg:text-5xl font-black text-slate-900">100%</h3>
                    <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Keamanan Data & Hak Akses</p>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="relative z-10 max-w-7xl mx-auto px-6 py-24 lg:px-8 space-y-16">
            <div class="text-center space-y-4">
                <h2 class="text-xs text-blue-600 font-bold uppercase tracking-widest">Fitur Unggulan</h2>
                <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900">
                    Dirancang untuk Kebutuhan Agensi Modern
                </h3>
                <p class="text-slate-500 max-w-xl mx-auto">
                    Kombinasi teknologi mutakhir untuk mempercepat validasi engagement influencer Anda secara profesional.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 hover:shadow-xl hover:-translate-y-1 transition duration-300 space-y-5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900">Apify Scraper Integration</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Tarik langsung data followers, likes, views, comment count, dan metadata konten media sosial dalam hitungan detik secara otomatis.
                    </p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 hover:shadow-xl hover:-translate-y-1 transition duration-300 space-y-5">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900">PDF & Excel Reporting</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Cetak dan ekspor laporan campaign khusus milik Anda ke format PDF dan Excel. Kirimkan kepada klien dengan rapi dan profesional.
                    </p>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 hover:shadow-xl hover:-translate-y-1 transition duration-300 space-y-5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900">Secure RBAC Protection</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Perlindungan hak akses berlapis untuk peran Admin Master, Admin, dan Client agar data campaign tidak bocor dan tersimpan aman.
                    </p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="relative z-10 max-w-5xl mx-auto px-6 pb-24">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl p-10 lg:p-16 text-center text-white space-y-6 shadow-2xl shadow-blue-500/25">
                <h3 class="text-3xl lg:text-4xl font-extrabold">Siap Mengoptimalkan Campaign Anda?</h3>
                <p class="text-blue-100 max-w-xl mx-auto">
                    Masuk ke dashboard sekarang juga untuk mendaftarkan link konten creator Anda dan melihat analisis data yang presisi.
                </p>
                <div class="pt-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-8 py-4 bg-white text-blue-600 font-extrabold rounded-xl shadow-lg hover:bg-slate-50 transition duration-200 inline-block">
                            Kembali ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-8 py-4 bg-white text-blue-600 font-extrabold rounded-xl shadow-lg hover:bg-slate-50 transition duration-200 inline-block">
                            Login Sekarang
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="relative z-10 border-t border-slate-200 bg-white py-8 text-center text-sm text-slate-500 font-medium">
            <p>&copy; {{ date('Y') }} Kahfi Engagement. All rights reserved.</p>
        </footer>
    </body>
</html>
