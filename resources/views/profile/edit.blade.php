<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-primary leading-tight">
            Pengaturan Profil & Keamanan ⚙️
        </h2>
        <p class="text-sm text-secondary mt-1">Kelola data diri, unggah foto profil, dan ubah kata sandi akun Anda.</p>
    </x-slot>

    <div class="space-y-6">
        @if (session('status') === 'profile-updated')
            <div class="p-4 bg-status-success/15 border border-status-success/30 text-status-success rounded-xl text-sm font-semibold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Profil Anda berhasil diperbarui!
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-status-danger/15 border border-status-danger/30 text-status-danger rounded-xl text-sm font-semibold space-y-1">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Terjadi kesalahan input:
                </div>
                <ul class="list-disc list-inside text-xs pl-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Card: Profile Photo / Preview -->
            <div class="bg-surface p-6 rounded-2xl border border-border shadow-sm flex flex-col items-center text-center justify-between">
                <div class="w-full flex flex-col items-center">
                    <h3 class="text-md font-bold text-primary mb-6">Foto Profil</h3>
                    
                    <div class="relative group">
                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-brand-blue shadow-lg bg-body flex items-center justify-center">
                            @if(Auth::user()->photo)
                                <img id="photoPreview" src="{{ asset(Auth::user()->photo) }}" class="w-full h-full object-cover" alt="Profile Photo">
                            @else
                                <div id="photoPlaceholder" class="w-full h-full bg-brand-blue/10 text-brand-blue flex items-center justify-center font-black text-3xl">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <img id="photoPreview" class="w-full h-full object-cover hidden" alt="Profile Photo">
                            @endif
                        </div>
                    </div>

                    <h4 class="mt-4 font-bold text-primary text-lg">{{ Auth::user()->name }}</h4>
                    <p class="text-xs text-secondary mt-1">@​{{ Auth::user()->username }} &bull; {{ Auth::user()->role }}</p>
                </div>

                <div class="mt-6 w-full text-xs text-secondary bg-body p-3 rounded-xl border border-border leading-relaxed">
                    Unggah file foto berformat JPG, PNG, atau WEBP dengan ukuran maksimal 2MB.
                </div>
            </div>

            <!-- Right Card: Form Details -->
            <div class="lg:col-span-2 bg-surface p-6 rounded-2xl border border-border shadow-sm">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('patch')

                    <h3 class="text-lg font-bold text-primary mb-4 pb-2 border-b border-border">Data Diri & Keamanan</h3>

                    <!-- Hidden Photo Input -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="photo_input" value="Unggah Foto Baru" />
                            <input type="file" id="photo_input" name="photo" accept="image/*" class="mt-1 block w-full text-sm text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-blue/10 file:text-brand-blue hover:file:bg-brand-blue/20 cursor-pointer" onchange="previewImage(this)">
                        </div>

                        <div>
                            <x-input-label for="name" value="Nama Lengkap" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                        </div>

                        <div>
                            <x-input-label for="username" value="Username" />
                            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)" required autocomplete="username" />
                        </div>

                        <div>
                            <x-input-label for="email" value="Alamat Email" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="email" />
                        </div>
                    </div>

                    <h3 class="text-lg font-bold text-primary mt-8 mb-4 pb-2 border-b border-border">Ubah Kata Sandi (Opsional)</h3>
                    <p class="text-xs text-secondary -mt-2 mb-4">Kosongkan jika Anda tidak ingin mengubah kata sandi saat ini.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="password" value="Kata Sandi Baru" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi Baru" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-border">
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-brand-gradient text-white rounded-xl font-bold text-sm hover:shadow-lg hover:shadow-brand-blue/30 transition-all duration-300">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = document.getElementById('photoPreview');
                    var placeholder = document.getElementById('photoPlaceholder');
                    
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (placeholder) {
                        placeholder.classList.add('hidden');
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-app-layout>
