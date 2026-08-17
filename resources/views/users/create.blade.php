<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('users.index') }}" class="text-secondary hover:text-primary transition-colors bg-surface border border-border p-2 rounded-lg hover:shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="font-semibold text-2xl text-primary leading-tight">
                    Tambah User Baru
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <form method="POST" action="{{ route('users.store') }}" class="bg-surface rounded-2xl border border-border p-6 shadow-sm space-y-6">
            @csrf

            <!-- Validation Errors -->
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
                <!-- Name -->
                <div>
                    <x-input-label for="name" value="Nama Lengkap *" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name') }}" required />
                </div>
                <!-- Username -->
                <div>
                    <x-input-label for="username" value="Username *" />
                    <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" value="{{ old('username') }}" required />
                </div>
                <!-- Email -->
                <div>
                    <x-input-label for="email" value="Email *" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email') }}" required />
                </div>
                <!-- Password -->
                <div>
                    <x-input-label for="password" value="Password *" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                </div>
                <!-- Role -->
                <div>
                    <x-input-label for="role" value="Role / Hak Akses (RBAC Spatie) *" />
                    <select id="role" name="role" class="mt-1 block w-full border-border bg-body text-primary rounded-lg focus:border-brand-blue shadow-sm" required>
                        <option value="">-- Pilih Hak Akses --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Status -->
                <div>
                    <x-input-label for="status" value="Status *" />
                    <select id="status" name="status" class="mt-1 block w-full border-border bg-body text-primary rounded-lg focus:border-brand-blue shadow-sm" required>
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-border mt-6">
                <x-primary-button>Simpan User</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>