<aside :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform sm:translate-x-0 bg-surface border-r border-border flex flex-col" aria-label="Sidebar" id="logo-sidebar">
    <div class="px-6 py-8">
        <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 group">
            <div class="w-8 h-8 rounded-lg bg-brand-gradient flex items-center justify-center shadow-lg shadow-brand-blue/30 group-hover:scale-105 transition-transform">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <span class="text-xl font-extrabold text-primary tracking-tight">Kahfi<span class="text-brand-blue">Eng.</span></span>
        </a>
    </div>

    <div class="flex-1 px-4 overflow-y-auto space-y-2">
        <!-- 1. Dashboard Monitoring -->
        @can('dashboard.view')
        <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->is('dashboard*') ? 'bg-brand-blue/10 text-brand-blue dark:bg-brand-blue/20 font-semibold' : 'text-secondary hover:bg-gray-100/80 hover:text-primary dark:hover:bg-gray-800/80 dark:hover:text-gray-100' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span class="text-sm">Dashboard Monitoring</span>
        </a>
        @endcan

        @can('operasional-konten.view')
        <!-- 4. Operasional Konten -->
        <a href="{{ route('operasional-konten.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('operasional-konten.*') ? 'bg-brand-blue/10 text-brand-blue dark:bg-brand-blue/20 font-semibold' : 'text-secondary hover:bg-gray-100/80 hover:text-primary dark:hover:bg-gray-800/80 dark:hover:text-gray-100' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
            <span class="text-sm">Operasional Konten</span>
        </a>
        @endcan

        @can('update-saw.view')
        <!-- Update Campaign SAW -->
        <a href="{{ route('update-saw.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('update-saw.*') ? 'bg-brand-blue/10 text-brand-blue dark:bg-brand-blue/20 font-semibold' : 'text-secondary hover:bg-gray-100/80 hover:text-primary dark:hover:bg-gray-800/80 dark:hover:text-gray-100' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <span class="text-sm">Update Campaign SAW</span>
        </a>
        @endcan

        @can('campaigns.view')
        <!-- 2. Kelola Campaign -->
        <a href="{{ route('campaigns.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('campaigns.*') ? 'bg-brand-blue/10 text-brand-blue dark:bg-brand-blue/20 font-semibold' : 'text-secondary hover:bg-gray-100/80 hover:text-primary dark:hover:bg-gray-800/80 dark:hover:text-gray-100' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            <span class="text-sm">Kelola Campaign</span>
        </a>
        @endcan

        @canany(['users.view', 'roles.view', 'master-data.view'])
        <!-- 3. Master Data (Dropdown via Alpine JS) -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-secondary hover:bg-gray-100/80 hover:text-primary dark:hover:bg-gray-800/80 dark:hover:text-gray-100 transition-colors focus:outline-none">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    <span class="text-sm">Master Data</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="open" x-collapse style="display: none;" class="pl-11 pr-3 py-2 space-y-1">
                @can('users.view')
                <a href="{{ route('users.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'bg-brand-blue/10 text-brand-blue dark:bg-brand-blue/20 font-bold' : 'text-secondary hover:text-primary hover:bg-gray-100/80 dark:hover:bg-gray-800/80 dark:hover:text-gray-100' }}">Manajemen Users</a>
                @endcan
                @can('roles.view')
                <a href="{{ route('roles.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('roles.*') ? 'bg-brand-blue/10 text-brand-blue dark:bg-brand-blue/20 font-bold' : 'text-secondary hover:text-primary hover:bg-gray-100/80 dark:hover:bg-gray-800/80 dark:hover:text-gray-100' }}">Roles & Hak Akses</a>
                @endcan
                @can('master-data.view')
                <a href="{{ route('kategori-konten.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('kategori-konten.*') ? 'bg-brand-blue/10 text-brand-blue dark:bg-brand-blue/20 font-bold' : 'text-secondary hover:text-primary hover:bg-gray-100/80 dark:hover:bg-gray-800/80 dark:hover:text-gray-100' }}">Kategori Konten</a>
                <a href="{{ route('kategori-creator.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('kategori-creator.*') ? 'bg-brand-blue/10 text-brand-blue dark:bg-brand-blue/20 font-bold' : 'text-secondary hover:text-primary hover:bg-gray-100/80 dark:hover:bg-gray-800/80 dark:hover:text-gray-100' }}">Kategori Creator</a>
                @endcan
            </div>
        </div>
        @endcanany

        @can('laporan.view')
        <!-- 6. Laporan & Export -->
        <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('laporan.*') ? 'bg-brand-blue/10 text-brand-blue dark:bg-brand-blue/20 font-semibold' : 'text-secondary hover:bg-gray-100/80 hover:text-primary dark:hover:bg-gray-800/80 dark:hover:text-gray-100' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="text-sm">Laporan & Export</span>
        </a>
        @endcan

        @can('profile.edit')
        <!-- 7. Pengaturan Profil -->
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('profile.*') ? 'bg-brand-blue/10 text-brand-blue dark:bg-brand-blue/20 font-semibold' : 'text-secondary hover:bg-gray-100/80 hover:text-primary dark:hover:bg-gray-800/80 dark:hover:text-gray-100' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span class="text-sm">Pengaturan Profil</span>
        </a>
        @endcan
    </div>

    <!-- User Profile & Logout at Bottom -->
    <div class="p-4 border-t border-border mt-auto">
        @can('profile.edit')
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 mb-4 px-2 hover:bg-gray-100/80 dark:hover:bg-gray-800/80 p-2 rounded-xl transition-colors">
        @else
        <div class="flex items-center gap-3 mb-4 px-2">
        @endcan
            @if(Auth::user()->photo)
                <img src="{{ asset(Auth::user()->photo) }}" alt="User" class="w-10 h-10 rounded-full shadow-sm object-cover">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563eb&color=fff&rounded=true" alt="User" class="w-10 h-10 rounded-full shadow-sm">
            @endif
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-primary truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-secondary truncate">{{ Auth::user()->email }}</p>
            </div>
        @can('profile.edit')
        </a>
        @else
        </div>
        @endcan
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center justify-center gap-2 py-2.5 px-4 text-sm font-semibold text-white bg-brand-blue hover:bg-brand-blue-hover rounded-xl transition-all shadow-md shadow-brand-blue/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Sign Out
            </button>
        </form>
    </div>
</aside>
