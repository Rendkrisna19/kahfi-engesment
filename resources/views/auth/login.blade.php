<x-guest-layout>
    <div class="grid grid-cols-1 md:grid-cols-2">
        <!-- Left Side: Login Form -->
        <div class="p-8 sm:p-12 flex flex-col justify-center bg-surface">
            <!-- Session Status -->
            <x-auth-session-status class="mb-6" :status="session('status')" />

            <div class="mb-8">
                <h2 class="text-3xl font-extrabold tracking-tight text-primary">Selamat Datang</h2>
                <p class="text-sm text-secondary mt-2">Silakan masuk ke akun Anda untuk mengakses dashboard analisis campaign.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-bold text-secondary mb-2">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </span>
                        <input
                            id="username"
                            type="text"
                            name="username"
                            value="{{ old('username') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Masukkan username Anda"
                            class="block w-full pl-11 pr-4 py-3 text-sm border-border bg-body text-primary rounded-xl focus:border-brand-blue focus:ring-brand-blue shadow-sm transition duration-150"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-bold text-secondary mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </span>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="block w-full pl-11 pr-4 py-3 text-sm border-border bg-body text-primary rounded-xl focus:border-brand-blue focus:ring-brand-blue shadow-sm transition duration-150"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                        <input
                            id="remember_me"
                            type="checkbox"
                            class="rounded border-border text-brand-blue shadow-sm focus:ring-brand-blue transition-colors cursor-pointer"
                            name="remember"
                        >
                        <span class="ms-2 text-sm text-secondary group-hover:text-primary transition-colors font-medium">
                            Ingat saya
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <x-primary-button class="w-full py-3.5 flex justify-center text-sm font-bold tracking-wider uppercase rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/35 hover:-translate-y-0.5 active:translate-y-0 transition duration-150">
                        Masuk Ke Dashboard
                    </x-primary-button>
                </div>
            </form>
        </div>

        <!-- Right Side: Lottie Animation Panel -->
        <div class="hidden md:flex flex-col items-center justify-center p-12 bg-gradient-to-br from-blue-600 to-indigo-700 text-white relative overflow-hidden">
            <!-- Background pattern -->
            <div class="absolute inset-0 bg-grid-pattern opacity-10 pointer-events-none"></div>
            <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/5 blur-2xl"></div>
            <div class="absolute -bottom-12 -left-12 w-64 h-64 rounded-full bg-white/5 blur-3xl"></div>

            <div class="relative z-10 w-full max-w-sm text-center space-y-6">
                <!-- Lottie Container -->
                <div id="lottie-anim" class="w-64 h-64 mx-auto drop-shadow-2xl"></div>

                <div class="space-y-3">
                    <h3 class="text-xl font-bold tracking-tight">Real-time Scraping Engine</h3>
                    <p class="text-xs text-blue-100/80 leading-relaxed font-medium">
                        Pantau performa likes, views, dan comment creator secara akurat dengan integrasi scrap otomatis.
                    </p>
                </div>

                <!-- Indicator dots -->
                <div class="flex justify-center gap-1.5 pt-2">
                    <span class="w-5 h-1.5 rounded-full bg-white transition-all"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-white/40"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-white/40"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Lottie Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var animContainer = document.getElementById('lottie-anim');
            if (animContainer) {
                lottie.loadAnimation({
                    container: animContainer,
                    renderer: 'svg',
                    loop: true,
                    autoplay: true,
                    path: '{{ asset("images/animasi.json") }}'
                });
            }
        });
    </script>
</x-guest-layout>