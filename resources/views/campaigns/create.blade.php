<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('campaigns.index') }}" class="text-secondary hover:text-primary transition-colors bg-surface border border-border p-2 rounded-lg hover:shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="font-semibold text-2xl text-primary leading-tight">
                    Tambah Campaign Baru
                </h2>
                <p class="text-sm text-secondary mt-1">Lengkapi form di bawah ini untuk membuat campaign baru.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <form method="POST" action="{{ route('campaigns.store') }}" class="bg-surface rounded-2xl border border-border p-6 shadow-sm space-y-6">
            @csrf

            <!-- Validation Errors (if any) -->
            @if ($errors->any())
                <div class="bg-status-danger/10 text-status-danger p-4 rounded-xl text-sm mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Campaign -->
                <div class="md:col-span-2">
                    <x-input-label for="nama_campaign" value="Nama Campaign *" />
                    <x-text-input id="nama_campaign" name="nama_campaign" type="text" class="mt-1 block w-full" value="{{ old('nama_campaign') }}" placeholder="Contoh: Launching Produk Q3" required />
                </div>

                <!-- Klien / PIC -->
                <div>
                    <x-input-label for="client_id" value="Klien / PIC *" />
                    <select id="client_id" name="client_id" class="mt-1 block w-full border-border bg-body text-primary rounded-lg focus:border-brand-blue focus:ring-brand-blue shadow-sm" required>
                        <option value="">-- Pilih Klien --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Platform -->
                <div>
                    <x-input-label for="platform" value="Platform *" />
                    <select id="platform" name="platform" class="mt-1 block w-full border-border bg-body text-primary rounded-lg focus:border-brand-blue focus:ring-brand-blue shadow-sm" required>
                        <option value="TikTok" {{ old('platform') == 'TikTok' ? 'selected' : '' }}>TikTok</option>
                        <option value="Instagram" {{ old('platform') == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                        <option value="Omnichannel" {{ old('platform') == 'Omnichannel' ? 'selected' : '' }}>Omnichannel (Gabungan)</option>
                    </select>
                </div>

                <!-- Tanggal Mulai -->
                <div>
                    <x-input-label for="tanggal_mulai" value="Tanggal Mulai *" />
                    <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date" class="mt-1 block w-full" value="{{ old('tanggal_mulai') }}" required />
                </div>

                <!-- Tanggal Selesai -->
                <div>
                    <x-input-label for="tanggal_selesai" value="Tanggal Selesai *" />
                    <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date" class="mt-1 block w-full" value="{{ old('tanggal_selesai') }}" required />
                </div>

                <!-- Status -->
                <div class="md:col-span-2">
                    <x-input-label for="status" value="Status Awal *" />
                    <select id="status" name="status" class="mt-1 block w-full border-border bg-body text-primary rounded-lg focus:border-brand-blue focus:ring-brand-blue shadow-sm" required>
                        <option value="Draft" {{ old('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                        <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    </select>
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <x-input-label for="deskripsi" value="Deskripsi / Keterangan Tambahan" />
                    <textarea id="deskripsi" name="deskripsi" rows="3" class="mt-1 block w-full border-border bg-body text-primary rounded-lg focus:border-brand-blue focus:ring-brand-blue shadow-sm" placeholder="Opsional...">{{ old('deskripsi') }}</textarea>
                </div>

                <!-- Penugasan Admin (Akses Engagement) -->
                <div class="md:col-span-2">
                    <x-input-label value="Penugasan Admin (Akses Engagement Campaign)" />
                    <p class="text-xs text-secondary mt-0.5 mb-2">Pilih user Admin yang ditugaskan untuk mengelola dan mengakses data engagement campaign ini.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 p-4 bg-body border border-border rounded-xl max-h-48 overflow-y-auto">
                        @forelse($admins as $admin)
                            <label class="flex items-center gap-3 cursor-pointer p-2.5 rounded-lg hover:bg-surface border border-transparent hover:border-border transition-colors">
                                <input type="checkbox" name="admin_ids[]" value="{{ $admin->id }}" {{ in_array($admin->id, old('admin_ids', [])) ? 'checked' : '' }} class="rounded border-border text-brand-blue focus:ring-brand-blue w-4 h-4">
                                <div class="truncate">
                                    <span class="block text-xs font-bold text-primary truncate">{{ $admin->name }}</span>
                                    <span class="text-[10px] text-secondary truncate">@​{{ $admin->username }} ({{ $admin->role }})</span>
                                </div>
                            </label>
                        @empty
                            <p class="text-xs text-secondary col-span-3">Belum ada user dengan role Admin terdaftar.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end pt-4 border-t border-border mt-6 gap-3">
                <a href="{{ route('campaigns.index') }}" class="px-4 py-2 text-sm font-medium text-secondary hover:text-primary transition-colors bg-body border border-border rounded-xl">
                    Batal
                </a>
                <x-primary-button>
                    Simpan Campaign
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>