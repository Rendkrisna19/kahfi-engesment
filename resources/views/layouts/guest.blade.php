<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Kahfi Engagement') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-primary antialiased bg-body relative overflow-hidden min-h-screen">
        
        <!-- Animated Background Decor -->
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-brand-blue/10 blur-[100px] animate-pulse pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-brand-purple/10 blur-[100px] animate-pulse pointer-events-none" style="animation-delay: 2s;"></div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10">
            <div class="mb-8 text-center">
                <a href="/" class="flex flex-col items-center gap-3 group">
                    <div class="w-16 h-16 rounded-2xl bg-brand-gradient flex items-center justify-center shadow-lg shadow-brand-blue/30 group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h1 class="text-2xl font-extrabold bg-clip-text text-transparent bg-brand-gradient">
                        Kahfi Engagement
                    </h1>
                </a>
            </div>

            <div class="w-full sm:max-w-4xl mx-4 bg-surface shadow-2xl shadow-gray-200/50 rounded-2xl border border-border overflow-hidden">
                {{ $slot }}
            </div>
            
            <p class="mt-8 text-sm text-muted">
                &copy; {{ date('Y') }} Kahfi Engagement. All rights reserved.
            </p>
        </div>
    </body>
</html>
