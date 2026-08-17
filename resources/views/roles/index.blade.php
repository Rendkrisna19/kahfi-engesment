<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-2xl text-primary leading-tight">
                Manajemen Roles & Hak Akses
            </h2>
            <p class="text-sm text-secondary mt-1">Kelola peranan pengguna dan batas akses modul (RBAC) pada sistem.</p>
        </div>
    </x-slot>

    <x-toast />

    <div class="py-6 space-y-6">
        <div class="bg-surface border border-border shadow-sm rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-border flex justify-between items-center gap-4">
                <h3 class="text-lg font-bold text-primary">Daftar Roles</h3>
                @can('roles.create')
                <a href="{{ route('roles.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-brand-blue border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-blue-hover active:bg-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2 transition shadow-md shadow-brand-blue/30 whitespace-nowrap">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Role
                </a>
                @endcan
            </div>
            
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-secondary whitespace-nowrap">
                        <thead class="text-xs text-secondary uppercase bg-body/50 border-b border-border">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold">Nama Role</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Daftar Hak Akses (Permissions)</th>
                                <th scope="col" class="px-6 py-4 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                                <tr class="border-b border-border hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-primary">
                                        {{ $role->name }}
                                    </td>
                                    <td class="px-6 py-4 max-w-lg whitespace-normal">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($role->permissions as $perm)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                    {{ $perm->name }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-gray-500 italic">Belum ada hak akses.</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            @can('roles.edit')
                                            <a href="{{ route('roles.edit', $role) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 rounded-lg hover:bg-yellow-500/20 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                Edit
                                            </a>
                                            @endcan
                                            
                                            @can('roles.delete')
                                            @if(!in_array($role->name, ['Admin Master', 'Admin', 'Client']))
                                            <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus role ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-500/10 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-500/20 transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    Hapus
                                                </button>
                                            </form>
                                            @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-secondary">
                                        Belum ada role yang ditambahkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
