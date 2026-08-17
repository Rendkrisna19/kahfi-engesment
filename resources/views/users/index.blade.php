<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-2xl text-primary leading-tight">
                Kelola User & Hak Akses
            </h2>
            <p class="text-sm text-secondary mt-1">Atur pengguna sistem dan tentukan peran (Role/RBAC) masing-masing.</p>
        </div>
    </x-slot>

    <x-toast />

    <div class="space-y-6" x-data="{ isLoaded: false }" x-init="setTimeout(() => isLoaded = true, 500)">
        <div class="bg-surface rounded-2xl border border-border overflow-hidden shadow-sm">
            <div class="p-6 border-b border-border flex justify-between items-center gap-4">
                <h3 class="text-lg font-bold text-primary">Daftar Pengguna</h3>
                @can('users.create')
                <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-brand-blue border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-blue-hover active:bg-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2 transition shadow-md shadow-brand-blue/30 whitespace-nowrap">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah User
                </a>
                @endcan
            </div>

            <div x-show="!isLoaded">
                <x-skeleton count="5" />
            </div>

            <div x-show="isLoaded" style="display: none;" class="overflow-x-auto">
                <table class="w-full text-sm text-left text-primary">
                    <thead class="text-xs text-secondary uppercase bg-body/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">User</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Role / Hak Akses</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Status</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" class="w-10 h-10 rounded-full" alt="avatar">
                                    <div>
                                        <p class="font-bold text-primary">{{ $user->name }}</p>
                                        <p class="text-xs text-secondary">{{ $user->email }} ({{ $user->username }})</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->role === 'Admin Master')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-brand-blue/10 text-brand-blue">Admin Master</span>
                                @elseif($user->role === 'Admin')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">Admin</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Client</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($user->status === 'Aktif')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-status-success/10 text-status-success">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-status-danger/10 text-status-danger">Nonaktif</span>
                                @endif
                            </td>
                             <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @can('users.edit')
                                    <a href="{{ route('users.edit', $user) }}" class="text-secondary hover:text-brand-blue p-2 rounded-lg hover:bg-brand-blue/10 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    @endcan
                                    @can('users.delete')
                                    @if(Auth::id() !== $user->id)
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus user ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-secondary hover:text-status-danger p-2 rounded-lg hover:bg-status-danger/10 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                    @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-secondary">Belum ada user.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>