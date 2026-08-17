<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-primary leading-tight">
            Kelola Kategori Konten
        </h2>
        <p class="text-sm text-secondary mt-1">Atur master data kategori untuk konten creator.</p>
    </x-slot>

    <x-toast />

    <div class="space-y-6" x-data="{ isLoaded: false, isModalOpen: false, modalMode: 'create', modalData: {id: '', nama: ''} }" x-init="setTimeout(() => isLoaded = true, 500)">
        
        <div class="bg-surface rounded-2xl border border-border overflow-hidden shadow-sm">
            <div class="p-6 border-b border-border flex justify-between items-center">
                <h3 class="text-lg font-bold text-primary">Daftar Kategori Konten</h3>
                <button @click="modalMode = 'create'; modalData = {id: '', nama: ''}; isModalOpen = true;" class="inline-flex items-center justify-center px-4 py-2 bg-brand-blue border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-blue-hover transition shadow-md shadow-brand-blue/30">
                    + Tambah Kategori
                </button>
            </div>

            <div x-show="!isLoaded">
                <x-skeleton count="3" />
            </div>

            <div x-show="isLoaded" style="display: none;" class="overflow-x-auto">
                <table class="w-full text-sm text-left text-primary">
                    <thead class="text-xs text-secondary uppercase bg-body/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold w-16">ID</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Nama Kategori</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($kategori as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4 text-secondary">#{{ $item->id }}</td>
                            <td class="px-6 py-4 font-bold text-primary">{{ $item->nama }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="modalMode = 'edit'; modalData = {id: '{{ $item->id }}', nama: '{{ addslashes($item->nama) }}'}; isModalOpen = true;" class="text-secondary hover:text-brand-blue p-2 rounded-lg hover:bg-brand-blue/10 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form action="{{ route('kategori-konten.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kategori ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-secondary hover:text-status-danger p-2 rounded-lg hover:bg-status-danger/10 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-secondary">Belum ada kategori konten.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Alpine Modal -->
        <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/75 transition-opacity" @click="isModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="isModalOpen" x-transition.scale.origin.bottom class="inline-block align-bottom bg-surface rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form :action="modalMode === 'edit' ? '{{ url('kategori-konten') }}/' + modalData.id : '{{ route('kategori-konten.store') }}'" method="POST">
                        @csrf
                        <template x-if="modalMode === 'edit'">
                            <input type="hidden" name="_method" value="PUT">
                        </template>
                        <div class="px-6 pt-6 pb-4">
                            <h3 class="text-lg leading-6 font-bold text-primary" id="modal-title" x-text="modalMode === 'edit' ? 'Edit Kategori Konten' : 'Tambah Kategori Konten'"></h3>
                            <div class="mt-4">
                                <x-input-label for="nama" value="Nama Kategori *" />
                                <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" x-model="modalData.nama" required />
                            </div>
                        </div>
                        <div class="bg-body/50 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                            <button type="button" @click="isModalOpen = false" class="px-4 py-2 text-sm font-medium text-secondary bg-surface border border-border rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800">Batal</button>
                            <x-primary-button>Simpan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
