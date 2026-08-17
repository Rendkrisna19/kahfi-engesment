<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-primary leading-tight">
            Edit Role: {{ $role->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-body border border-border shadow-sm sm:rounded-2xl overflow-hidden">
                <div class="p-6">
                    <form action="{{ route('roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-primary mb-2">Nama Role</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" class="w-full rounded-xl border-border bg-body text-primary focus:border-brand-blue focus:ring-brand-blue" required {{ in_array($role->name, ['Admin Master', 'Admin', 'Client']) ? 'readonly' : '' }}>
                            @if(in_array($role->name, ['Admin Master', 'Admin', 'Client']))
                                <p class="mt-1 text-xs text-secondary">Nama role bawaan sistem tidak dapat diubah.</p>
                            @endif
                            @error('name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-primary mb-4 border-b border-border pb-2">Edit Hak Akses (Permissions)</h3>
                            <div class="space-y-6">
                                @forelse($permissions as $group => $perms)
                                    <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-border">
                                        <h4 class="text-sm font-bold text-primary uppercase tracking-wider mb-3">{{ ucfirst($group) }}</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @foreach($perms as $perm)
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="rounded border-gray-300 text-brand-blue shadow-sm focus:border-brand-blue focus:ring focus:ring-brand-blue focus:ring-opacity-50"
                                                    {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                                                    <span class="ml-2 text-sm text-secondary">{{ $perm->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-secondary">Belum ada permission di sistem. Tambahkan permission terlebih dahulu.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 pt-4 border-t border-border">
                            <a href="{{ route('roles.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-brand-blue border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
