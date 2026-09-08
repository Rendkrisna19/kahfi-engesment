<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-primary leading-tight flex items-center gap-2">
                    <span>Kelola Konten Landing Page (CMS)</span>
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-brand-blue/10 text-brand-blue border border-brand-blue/20">
                        Live Editor
                    </span>
                </h2>
                <p class="text-sm text-secondary mt-1">
                    Sesuaikan seluruh teks, gambar, portofolio, dan section landing page dari satu dashboard terpadu.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-surface border border-border text-primary rounded-xl text-sm font-semibold hover:bg-gray-100 dark:hover:bg-gray-800 transition shadow-sm">
                    <svg class="w-4 h-4 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    Lihat Landing Page
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 shadow-sm">
            <div class="font-bold text-sm mb-1">Ada kesalahan penginputan:</div>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div x-data="{ 
        tab: '{{ request('tab', 'hero') }}',
        editModalOpen: false,
        addModalOpen: false,
        modalType: '',
        currentItem: {},
        openAddModal(type) {
            this.modalType = type;
            this.currentItem = { type: type, title: '', subtitle: '', description: '', link: '', sort_order: 0, is_active: 1, extra_meta: {} };
            this.addModalOpen = true;
        },
        openEditModal(item) {
            this.currentItem = JSON.parse(JSON.stringify(item));
            this.editModalOpen = true;
        }
    }" class="space-y-6">

        <!-- Navigation Tabs -->
        <div class="bg-surface border border-border rounded-2xl p-2 shadow-sm overflow-x-auto">
            <div class="flex items-center gap-1.5 min-w-max">
                <button @click="tab = 'hero'" :class="tab === 'hero' ? 'bg-brand-blue text-white shadow-md' : 'text-secondary hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary'" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Hero & Brand
                </button>
                <button @click="tab = 'why_join'" :class="tab === 'why_join' ? 'bg-brand-blue text-white shadow-md' : 'text-secondary hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary'" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Kenapa Gabung
                </button>
                <button @click="tab = 'not_for'" :class="tab === 'not_for' ? 'bg-brand-blue text-white shadow-md' : 'text-secondary hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary'" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                    Bukan Untuk Semua Orang
                </button>
                <button @click="tab = 'portfolios'" :class="tab === 'portfolios' ? 'bg-brand-blue text-white shadow-md' : 'text-secondary hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary'" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Portofolio
                </button>
                <button @click="tab = 'clients'" :class="tab === 'clients' ? 'bg-brand-blue text-white shadow-md' : 'text-secondary hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary'" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Client / Mitra
                </button>
                <button @click="tab = 'how_it_works'" :class="tab === 'how_it_works' ? 'bg-brand-blue text-white shadow-md' : 'text-secondary hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary'" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Cara Kerja
                </button>
                <button @click="tab = 'cta'" :class="tab === 'cta' ? 'bg-brand-blue text-white shadow-md' : 'text-secondary hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary'" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                    Siap Banjir Views & CTA
                </button>
            </div>
        </div>

        <!-- TAB 1: HERO & BRAND -->
        <div x-show="tab === 'hero'" class="space-y-6">
            <form action="{{ route('admin.cms.settings.update') }}" method="POST" enctype="multipart/form-data" class="bg-surface border border-border rounded-2xl p-6 shadow-sm space-y-6">
                @csrf
                <input type="hidden" name="active_tab" value="hero">

                <div class="border-b border-border pb-4">
                    <h3 class="text-lg font-bold text-primary">Pengaturan Hero & Brand Identity</h3>
                    <p class="text-xs text-secondary">Ubah nama brand, hook utama, badge hero, subheadline, tombol CTA, dan nomor WhatsApp.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Brand Logo Upload (Menggantikan Icon Petir ⚡) -->
                    <div class="md:col-span-2 p-5 rounded-2xl bg-body border border-border shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <label class="block text-xs font-bold uppercase tracking-wider text-primary flex items-center gap-2">
                                    <span>Logo Brand (Menggantikan Icon Petir ⚡)</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100">Upload Gambar</span>
                                </label>
                                <p class="text-xs text-secondary">
                                    Upload gambar logo brand Anda (PNG transparan disarankan, JPG, SVG, WEBP maks 4MB). Logo ini akan ditampilkan di navbar, footer, sidebar dashboard, dan halaman login.
                                </p>
                            </div>
                            <div class="flex items-center gap-4 shrink-0">
                                @php
                                    $brandLogoUrl = \App\Models\LandingSetting::brandLogoUrl();
                                @endphp
                                @if($brandLogoUrl)
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 rounded-xl bg-surface border border-border shadow-sm">
                                            <img src="{{ $brandLogoUrl }}" alt="Logo Brand" class="h-10 w-auto max-w-[120px] object-contain">
                                        </div>
                                        <label class="text-xs text-rose-500 hover:text-rose-700 font-bold flex items-center gap-1.5 cursor-pointer bg-rose-50 dark:bg-rose-950/40 px-3 py-1.5 rounded-lg border border-rose-200">
                                            <input type="checkbox" name="remove_brand_logo" value="1" class="rounded border-border text-rose-600 focus:ring-rose-500">
                                            Hapus Logo
                                        </label>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-surface border border-border text-xs font-semibold text-secondary">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        </div>
                                        <span>Icon Petir (Default)</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-border">
                            <input type="file" name="brand_logo" accept="image/*" class="w-full text-xs text-secondary file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-blue file:text-white hover:file:opacity-90 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary mb-2">Nama Brand</label>
                        <input type="text" name="brand_name" value="{{ $settings['brand_name'] ?? 'Kahfi Engagement' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm focus:border-brand-blue focus:ring-brand-blue">
                        <p class="text-[11px] text-secondary mt-1">Muncul di logo navbar, judul utama, dan footer.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary mb-2">Tagline Brand</label>
                        <input type="text" name="brand_tagline" value="{{ $settings['brand_tagline'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm focus:border-brand-blue focus:ring-brand-blue">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary mb-2">Hero Badge (Pill Hook Atas)</label>
                        <input type="text" name="hero_badge" value="{{ $settings['hero_badge'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm focus:border-brand-blue focus:ring-brand-blue">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary mb-2">Headline Utama Hero</label>
                        <textarea name="hero_headline" rows="2" class="w-full rounded-xl border-border bg-body text-primary text-sm focus:border-brand-blue focus:ring-brand-blue">{{ $settings['hero_headline'] ?? '' }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary mb-2">Subheadline / Deskripsi Hero</label>
                        <textarea name="hero_subheadline" rows="3" class="w-full rounded-xl border-border bg-body text-primary text-sm focus:border-brand-blue focus:ring-brand-blue">{{ $settings['hero_subheadline'] ?? '' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary mb-2">Teks Tombol CTA Utama</label>
                        <input type="text" name="hero_primary_cta_text" value="{{ $settings['hero_primary_cta_text'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm focus:border-brand-blue focus:ring-brand-blue">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary mb-2">URL / Link Tombol CTA Utama (WhatsApp / URL)</label>
                        <input type="text" name="hero_primary_cta_url" value="{{ $settings['hero_primary_cta_url'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm focus:border-brand-blue focus:ring-brand-blue">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary mb-2">Teks Tombol Sekunder</label>
                        <input type="text" name="hero_secondary_cta_text" value="{{ $settings['hero_secondary_cta_text'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm focus:border-brand-blue focus:ring-brand-blue">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary mb-2">URL Tombol Sekunder (cth: #portofolio)</label>
                        <input type="text" name="hero_secondary_cta_url" value="{{ $settings['hero_secondary_cta_url'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm focus:border-brand-blue focus:ring-brand-blue">
                    </div>

                    <!-- Real-Time Hero Stats Sync Notification -->
                    <div class="md:col-span-2 p-4 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 text-blue-900 dark:text-blue-200 text-xs flex items-center gap-3">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <span class="font-bold">Statistik Hero Real-Time:</span> Angka <strong>Kreator Aktif</strong>, <strong>Total Views Terdistribusi</strong>, dan <strong>Brand & Campaign</strong> sekarang otomatis dihitung dari database (mengikuti data link & kampanye aktual di dashboard Admin Master) sehingga selalu akurat tanpa perlu diinput manual.
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary mb-2">Nomor WhatsApp Official (Format: 628xxx)</label>
                        <input type="text" name="contact_whatsapp" value="{{ $settings['contact_whatsapp'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-border">
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:-translate-y-0.5 transition">
                        Simpan Perubahan Hero
                    </button>
                </div>
            </form>
        </div>

        <!-- TAB 2: KENAPA GABUNG -->
        <div x-show="tab === 'why_join'" class="space-y-6">
            <!-- Header Settings Form -->
            <form action="{{ route('admin.cms.settings.update') }}" method="POST" class="bg-surface border border-border rounded-2xl p-6 shadow-sm space-y-4">
                @csrf
                <input type="hidden" name="active_tab" value="why_join">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-bold text-primary">Judul & Subjudul Section "Kenapa Gabung"</h3>
                    <button type="submit" class="px-4 py-1.5 bg-brand-blue text-white text-xs font-bold rounded-lg shadow-sm hover:opacity-90">Simpan Header</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Badge Atas</label>
                        <input type="text" name="why_join_badge" value="{{ $settings['why_join_badge'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-secondary mb-1">Judul Section</label>
                        <input type="text" name="why_join_title" value="{{ $settings['why_join_title'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-secondary mb-1">Subjudul / Deskripsi Singkat</label>
                        <textarea name="why_join_subtitle" rows="2" class="w-full rounded-xl border-border bg-body text-primary text-sm">{{ $settings['why_join_subtitle'] ?? '' }}</textarea>
                    </div>
                </div>
            </form>

            <!-- Repeatable Items List -->
            <div class="bg-surface border border-border rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-bold text-primary">Daftar Poin Keunggulan (Kenapa Gabung)</h4>
                        <p class="text-xs text-secondary">Poin-poin checklist yang meyakinkan brand untuk berpartner.</p>
                    </div>
                    <button @click="openAddModal('why_join')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl hover:bg-emerald-700 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Poin Baru
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($whyJoins as $item)
                        <div class="p-4 rounded-xl border border-border bg-body flex flex-col justify-between group hover:border-brand-blue/40 transition">
                            <div>
                                <div class="flex items-start justify-between gap-2">
                                    <h5 class="font-bold text-primary text-sm flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full {{ $item->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        {{ $item->title }}
                                    </h5>
                                    <span class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-secondary font-mono">Urutan: {{ $item->sort_order }}</span>
                                </div>
                                <p class="text-xs text-secondary mt-2 line-clamp-2">{{ $item->description }}</p>
                            </div>
                            <div class="flex items-center justify-end gap-2 mt-4 pt-3 border-t border-border/50">
                                <button type="button" data-item="{{ base64_encode(json_encode($item)) }}" @click="openEditModal(JSON.parse(atob($el.dataset.item)))" class="text-xs font-semibold text-brand-blue hover:underline">Edit</button>
                                <form action="{{ route('admin.cms.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus poin ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-500 hover:underline">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="md:col-span-2 text-center py-8 text-secondary text-sm">Belum ada poin. Klik tombol di atas untuk menambahkan.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TAB 3: BUKAN UNTUK SEMUA ORANG -->
        <div x-show="tab === 'not_for'" class="space-y-6">
            <form action="{{ route('admin.cms.settings.update') }}" method="POST" class="bg-surface border border-border rounded-2xl p-6 shadow-sm space-y-4">
                @csrf
                <input type="hidden" name="active_tab" value="not_for">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-bold text-primary">Header Section "Bukan Untuk Semua Orang"</h3>
                    <button type="submit" class="px-4 py-1.5 bg-brand-blue text-white text-xs font-bold rounded-lg shadow-sm hover:opacity-90">Simpan Header</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Badge</label>
                        <input type="text" name="not_for_badge" value="{{ $settings['not_for_badge'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Judul Utama</label>
                        <input type="text" name="not_for_title" value="{{ $settings['not_for_title'] ?? 'Bukan Untuk Semua Orang' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Subjudul Hook (Merah)</label>
                        <input type="text" name="not_for_subtitle" value="{{ $settings['not_for_subtitle'] ?? 'Jangan Daftar Kalau Kamu:' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-secondary mb-1">Deskripsi Tambahan</label>
                        <textarea name="not_for_description" rows="2" class="w-full rounded-xl border-border bg-body text-primary text-sm">{{ $settings['not_for_description'] ?? '' }}</textarea>
                    </div>
                </div>
            </form>

            <div class="bg-surface border border-border rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-bold text-primary">Kriteria Diskualifikasi ("Jangan Daftar Kalau Kamu:")</h4>
                        <p class="text-xs text-secondary">KlipConnect style reverse-psychology filter points.</p>
                    </div>
                    <button @click="openAddModal('not_for')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl hover:bg-emerald-700 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Kriteria
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($notFors as $item)
                        <div class="p-4 rounded-xl border border-rose-200 dark:border-rose-900/40 bg-rose-50/40 dark:bg-rose-950/20 flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between gap-2">
                                    <h5 class="font-bold text-rose-700 dark:text-rose-400 text-sm flex items-center gap-2">
                                        <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        {{ $item->title }}
                                    </h5>
                                    <span class="text-xs px-2 py-0.5 rounded bg-white dark:bg-gray-800 text-secondary font-mono">Urutan: {{ $item->sort_order }}</span>
                                </div>
                                <p class="text-xs text-secondary mt-2">{{ $item->description }}</p>
                            </div>
                            <div class="flex items-center justify-end gap-2 mt-4 pt-3 border-t border-rose-200/50 dark:border-rose-800/50">
                                <button type="button" data-item="{{ base64_encode(json_encode($item)) }}" @click="openEditModal(JSON.parse(atob($el.dataset.item)))" class="text-xs font-semibold text-brand-blue hover:underline">Edit</button>
                                <form action="{{ route('admin.cms.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus kriteria ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-500 hover:underline">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="md:col-span-2 text-center py-8 text-secondary text-sm">Belum ada item filter.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TAB 4: PORTOFOLIO -->
        <div x-show="tab === 'portfolios'" class="space-y-6">
            <form action="{{ route('admin.cms.settings.update') }}" method="POST" class="bg-surface border border-border rounded-2xl p-6 shadow-sm space-y-4">
                @csrf
                <input type="hidden" name="active_tab" value="portfolios">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-bold text-primary">Header Portofolio & Counter Total Views</h3>
                    <button type="submit" class="px-4 py-1.5 bg-brand-blue text-white text-xs font-bold rounded-lg shadow-sm hover:opacity-90">Simpan Header</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Badge</label>
                        <input type="text" name="portfolio_badge" value="{{ $settings['portfolio_badge'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Judul Section</label>
                        <input type="text" name="portfolio_title" value="{{ $settings['portfolio_title'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Counter Total Views Highlight</label>
                        <input type="text" name="portfolio_total_views" value="{{ $settings['portfolio_total_views'] ?? '150.000.000+' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm font-bold text-brand-blue">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Label Counter Total Views</label>
                        <input type="text" name="portfolio_views_label" value="{{ $settings['portfolio_views_label'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-xs font-bold text-secondary mb-1">Subjudul / Deskripsi Portofolio</label>
                        <textarea name="portfolio_subtitle" rows="2" class="w-full rounded-xl border-border bg-body text-primary text-sm">{{ $settings['portfolio_subtitle'] ?? '' }}</textarea>
                    </div>
                </div>
            </form>

            <div class="bg-surface border border-border rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-bold text-primary">Item Portofolio (Dengan Upload Image)</h4>
                        <p class="text-xs text-secondary">Upload tangkapan layar video TikTok / Reels dengan jutaan views.</p>
                    </div>
                    <button @click="openAddModal('portfolio')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl hover:bg-emerald-700 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Portofolio
                    </button>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @forelse($portfolios as $item)
                        <div class="rounded-2xl border border-border bg-body overflow-hidden flex flex-col group hover:shadow-lg transition">
                            <div class="aspect-[2/3] bg-slate-900 relative overflow-hidden flex items-center justify-center">
                                @if($item->image_url)
                                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="flex flex-col items-center justify-center text-slate-500 p-4 text-center">
                                        <svg class="w-8 h-8 mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="text-[11px]">Belum ada poster</span>
                                    </div>
                                @endif
                                @if(!empty($item->extra_meta['views']) || !empty($item->subtitle))
                                    <div class="absolute bottom-2 left-1/2 -translate-x-1/2 px-2.5 py-0.5 rounded-md bg-white text-slate-900 font-extrabold text-[11px] shadow whitespace-nowrap">
                                        {{ $item->extra_meta['views'] ?? $item->subtitle }}
                                    </div>
                                @endif
                            </div>
                            <div class="p-3 flex items-center justify-between border-t border-border bg-surface">
                                <span class="text-xs font-bold text-primary truncate max-w-[100px]">{{ $item->title }}</span>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" data-item="{{ base64_encode(json_encode($item)) }}" @click="openEditModal(JSON.parse(atob($el.dataset.item)))" class="text-xs font-bold text-brand-blue hover:underline">Edit</button>
                                    <form action="{{ route('admin.cms.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus portofolio ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-rose-500 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8 text-secondary text-sm">Belum ada item portofolio.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TAB 5: CLIENT / MITRA BRAND -->
        <div x-show="tab === 'clients'" class="space-y-6">
            <form action="{{ route('admin.cms.settings.update') }}" method="POST" class="bg-surface border border-border rounded-2xl p-6 shadow-sm space-y-4">
                @csrf
                <input type="hidden" name="active_tab" value="clients">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-bold text-primary">Header Section Client / Mitra</h3>
                    <button type="submit" class="px-4 py-1.5 bg-brand-blue text-white text-xs font-bold rounded-lg shadow-sm hover:opacity-90">Simpan Header</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Badge</label>
                        <input type="text" name="client_badge" value="{{ $settings['client_badge'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-secondary mb-1">Judul Section</label>
                        <input type="text" name="client_title" value="{{ $settings['client_title'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-secondary mb-1">Subjudul Section</label>
                        <textarea name="client_subtitle" rows="2" class="w-full rounded-xl border-border bg-body text-primary text-sm">{{ $settings['client_subtitle'] ?? '' }}</textarea>
                    </div>
                </div>
            </form>

            <div class="bg-surface border border-border rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-bold text-primary">Logo Client / Brand (Mendukung Upload Logo)</h4>
                        <p class="text-xs text-secondary">Logo brand yang ditampilkan pada slider/marquee banner klien kami.</p>
                    </div>
                    <button @click="openAddModal('client_logo')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl hover:bg-emerald-700 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Client Logo
                    </button>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @forelse($clients as $item)
                        <div class="p-4 rounded-xl border border-border bg-body flex flex-col items-center text-center justify-between group hover:border-brand-blue/40 transition">
                            <div class="w-full h-16 flex items-center justify-center mb-2 bg-surface rounded-lg p-2 border border-border">
                                @if($item->image_url)
                                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="max-h-12 max-w-full object-contain">
                                @else
                                    <span class="font-bold text-xs text-primary">{{ $item->title }}</span>
                                @endif
                            </div>
                            <span class="text-[11px] font-bold text-primary truncate max-w-full mb-1">{{ $item->title }}</span>
                            <div class="flex items-center justify-center gap-3 pt-2 border-t border-border w-full">
                                <button type="button" data-item="{{ base64_encode(json_encode($item)) }}" @click="openEditModal(JSON.parse(atob($el.dataset.item)))" class="text-[11px] font-semibold text-brand-blue hover:underline">Edit</button>
                                <form action="{{ route('admin.cms.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus klien ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[11px] font-semibold text-rose-500 hover:underline">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8 text-secondary text-sm">Belum ada logo client.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TAB 6: CARA KERJA (HOW IT WORKS) -->
        <div x-show="tab === 'how_it_works'" class="space-y-6">
            <form action="{{ route('admin.cms.settings.update') }}" method="POST" class="bg-surface border border-border rounded-2xl p-6 shadow-sm space-y-4">
                @csrf
                <input type="hidden" name="active_tab" value="how_it_works">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-bold text-primary">Header Section "Bagaimana Kami Bekerja"</h3>
                    <button type="submit" class="px-4 py-1.5 bg-brand-blue text-white text-xs font-bold rounded-lg shadow-sm hover:opacity-90">Simpan Header</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Badge</label>
                        <input type="text" name="how_badge" value="{{ $settings['how_badge'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-secondary mb-1">Judul Section</label>
                        <input type="text" name="how_title" value="{{ $settings['how_title'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-secondary mb-1">Subjudul Section</label>
                        <textarea name="how_subtitle" rows="2" class="w-full rounded-xl border-border bg-body text-primary text-sm">{{ $settings['how_subtitle'] ?? '' }}</textarea>
                    </div>
                </div>
            </form>

            <div class="bg-surface border border-border rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-bold text-primary">Langkah Kerja (Step-by-Step)</h4>
                        <p class="text-xs text-secondary">Alur 4 tahap proses eksekusi campaign dari awal sampai monitoring.</p>
                    </div>
                    <button @click="openAddModal('how_step')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl hover:bg-emerald-700 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Langkah
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @forelse($howSteps as $item)
                        <div class="p-5 rounded-2xl border border-border bg-body flex flex-col justify-between group hover:border-brand-blue/40 transition relative">
                            <div>
                                <span class="inline-block px-2.5 py-1 rounded-lg bg-brand-blue/10 text-brand-blue font-mono font-bold text-xs mb-3">
                                    {{ $item->subtitle ?: 'LANGKAH' }}
                                </span>
                                <h5 class="font-bold text-primary text-sm leading-snug">{{ $item->title }}</h5>
                                <p class="text-xs text-secondary mt-2 leading-relaxed">{{ $item->description }}</p>
                            </div>
                            <div class="flex items-center justify-end gap-2 mt-4 pt-3 border-t border-border">
                                <button type="button" data-item="{{ base64_encode(json_encode($item)) }}" @click="openEditModal(JSON.parse(atob($el.dataset.item)))" class="text-xs font-semibold text-brand-blue hover:underline">Edit</button>
                                <form action="{{ route('admin.cms.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus langkah ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-500 hover:underline">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8 text-secondary text-sm">Belum ada langkah kerja.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TAB 7: CTA SECTION -->
        <div x-show="tab === 'cta'" class="space-y-6">
            <form action="{{ route('admin.cms.settings.update') }}" method="POST" class="bg-surface border border-border rounded-2xl p-6 shadow-sm space-y-4">
                @csrf
                <input type="hidden" name="active_tab" value="cta">
                <div class="border-b border-border pb-3">
                    <h3 class="text-base font-bold text-primary">Pengaturan Banner "Siap Banjir Jutaan Views?"</h3>
                    <p class="text-xs text-secondary">Section konversi penutup di bagian bawah halaman.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Badge Atas</label>
                        <input type="text" name="cta_badge" value="{{ $settings['cta_badge'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Headline CTA</label>
                        <input type="text" name="cta_title" value="{{ $settings['cta_title'] ?? 'Siap Banjir Jutaan Views?' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm font-bold">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-secondary mb-1">Subheadline CTA</label>
                        <textarea name="cta_subtitle" rows="2" class="w-full rounded-xl border-border bg-body text-primary text-sm">{{ $settings['cta_subtitle'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Teks Tombol CTA</label>
                        <input type="text" name="cta_button_text" value="{{ $settings['cta_button_text'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Link Tombol CTA (URL WhatsApp)</label>
                        <input type="text" name="cta_button_url" value="{{ $settings['cta_button_url'] ?? '' }}" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>
                </div>
                <div class="flex justify-end pt-3 border-t border-border">
                    <button type="submit" class="px-5 py-2 bg-brand-blue text-white font-bold rounded-xl text-sm shadow hover:opacity-90">Simpan Banner CTA</button>
                </div>
            </form>

            <!-- Feature Chips -->
            <div class="bg-surface border border-border rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-bold text-primary">Chips Fitur / Nilai Tambah di CTA</h4>
                        <p class="text-xs text-secondary">Tag-tag penegas di bawah headline CTA (cth: "⚡ Slot Terbatas", "📊 Dashboard Pantau").</p>
                    </div>
                    <button @click="openAddModal('cta_chip')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl hover:bg-emerald-700 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Chip
                    </button>
                </div>

                <div class="flex flex-wrap gap-3">
                    @forelse($ctaChips as $item)
                        <div class="px-3.5 py-2 rounded-xl bg-body border border-border flex items-center gap-3">
                            <span class="text-xs font-bold text-primary">{{ $item->title }}</span>
                            <form action="{{ route('admin.cms.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus chip ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold text-xs">✕</button>
                            </form>
                        </div>
                    @empty
                        <div class="text-secondary text-xs">Belum ada chip fitur.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ================= MODAL TAMBAH ITEM ================= -->
        <div x-show="addModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="addModalOpen = false" class="bg-surface border border-border rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h4 class="font-bold text-lg text-primary">Tambah Item Baru</h4>
                    <button @click="addModalOpen = false" class="text-secondary hover:text-primary text-lg font-bold">✕</button>
                </div>

                <form action="{{ route('admin.cms.items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="type" :value="modalType">

                    <!-- File Upload Image (Ditaruh di atas untuk Client Logo & Portfolio) -->
                    <div x-show="modalType === 'client_logo' || modalType === 'portfolio'" class="p-3 bg-body rounded-xl border border-border">
                        <label class="block text-xs font-bold text-primary mb-1" x-text="modalType === 'client_logo' ? 'Pilih File Gambar Logo Klien' : 'Pilih File Gambar Poster Portofolio'"></label>
                        <input type="file" name="image" accept="image/*" class="w-full text-xs text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-blue file:text-white hover:file:opacity-90">
                        <p class="text-[11px] text-secondary mt-1">Format: JPG, PNG, WEBP, SVG (Maks. 20MB).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1" x-text="modalType === 'client_logo' ? 'Nama Klien / Brand (Opsional)' : 'Judul / Nama'"></label>
                        <input type="text" name="title" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>

                    <div x-show="modalType !== 'cta_chip' && modalType !== 'client_logo'">
                        <label class="block text-xs font-bold text-secondary mb-1">Subjudul / Kategori / Badge</label>
                        <input type="text" name="subtitle" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>

                    <div x-show="modalType !== 'cta_chip' && modalType !== 'client_logo'">
                        <label class="block text-xs font-bold text-secondary mb-1">Deskripsi</label>
                        <textarea name="description" rows="3" class="w-full rounded-xl border-border bg-body text-primary text-sm"></textarea>
                    </div>

                    <!-- Extra Meta for Portfolio -->
                    <div x-show="modalType === 'portfolio'" class="space-y-3 p-3 bg-body rounded-xl border border-border">
                        <div>
                            <label class="block text-[11px] font-bold text-secondary mb-1">Views Count Pill (cth: 120.569.023 atau 5.2M Views)</label>
                            <input type="text" name="extra_meta[views]" class="w-full rounded-lg border-border bg-surface text-primary text-xs" placeholder="cth: 120.569.023">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-secondary mb-1">Nomor Urutan</label>
                            <input type="number" name="sort_order" value="0" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                        </div>
                        <div class="flex items-center gap-2 pt-6">
                            <input type="checkbox" name="is_active" value="1" checked id="add_is_active" class="rounded border-border text-brand-blue focus:ring-brand-blue">
                            <label for="add_is_active" class="text-xs font-bold text-primary">Aktif Ditampilkan</label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-border">
                        <button type="button" @click="addModalOpen = false" class="px-4 py-2 bg-surface border border-border text-secondary text-sm font-semibold rounded-xl hover:bg-gray-100">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-brand-blue text-white text-sm font-bold rounded-xl hover:opacity-90 shadow">Simpan Item</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL EDIT ITEM ================= -->
        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="editModalOpen = false" class="bg-surface border border-border rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h4 class="font-bold text-lg text-primary">Edit Item</h4>
                    <button @click="editModalOpen = false" class="text-secondary hover:text-primary text-lg font-bold">✕</button>
                </div>

                <form action="{{ route('admin.cms.items.update_direct') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="id" :value="currentItem.id" x-model="currentItem.id">
                    <input type="hidden" name="type" :value="currentItem.type" x-model="currentItem.type">

                    <!-- File Upload Image -->
                    <div x-show="currentItem.type === 'client_logo' || currentItem.type === 'portfolio'" class="p-3 bg-body rounded-xl border border-border">
                        <label class="block text-xs font-bold text-primary mb-1" x-text="currentItem.type === 'client_logo' ? 'Ganti File Gambar Logo' : 'Ganti File Gambar Poster'"></label>
                        <template x-if="currentItem.image">
                            <div class="mb-2 p-2 border border-border rounded-xl flex items-center justify-between bg-surface">
                                <span class="text-xs text-secondary truncate max-w-[280px]" x-text="currentItem.image"></span>
                                <label class="text-xs text-rose-500 font-bold flex items-center gap-1 cursor-pointer">
                                    <input type="checkbox" name="remove_image" value="1" class="rounded border-border text-rose-600">
                                    Hapus
                                </label>
                            </div>
                        </template>
                        <input type="file" name="image" accept="image/*" class="w-full text-xs text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-blue file:text-white hover:file:opacity-90">
                        <p class="text-[11px] text-secondary mt-1">Pilih file baru jika ingin mengganti gambar.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1" x-text="currentItem.type === 'client_logo' ? 'Nama Klien / Brand (Opsional)' : 'Judul / Nama'"></label>
                        <input type="text" name="title" x-model="currentItem.title" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>

                    <div x-show="currentItem.type !== 'cta_chip' && currentItem.type !== 'client_logo'">
                        <label class="block text-xs font-bold text-secondary mb-1">Subjudul / Kategori / Badge</label>
                        <input type="text" name="subtitle" x-model="currentItem.subtitle" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                    </div>

                    <div x-show="currentItem.type !== 'cta_chip' && currentItem.type !== 'client_logo'">
                        <label class="block text-xs font-bold text-secondary mb-1">Deskripsi</label>
                        <textarea name="description" rows="3" x-model="currentItem.description" class="w-full rounded-xl border-border bg-body text-primary text-sm"></textarea>
                    </div>

                    <!-- Extra Meta for Portfolio -->
                    <div x-show="currentItem.type === 'portfolio'" class="space-y-3 p-3 bg-body rounded-xl border border-border">
                        <div>
                            <label class="block text-[11px] font-bold text-secondary mb-1">Views Count Pill (cth: 120.569.023 atau 5.2M Views)</label>
                            <input type="text" name="extra_meta[views]" :value="currentItem.extra_meta?.views ?? ''" class="w-full rounded-lg border-border bg-surface text-primary text-xs" placeholder="cth: 120.569.023">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-secondary mb-1">Nomor Urutan</label>
                            <input type="number" name="sort_order" x-model="currentItem.sort_order" class="w-full rounded-xl border-border bg-body text-primary text-sm">
                        </div>
                        <div class="flex items-center gap-2 pt-6">
                            <input type="checkbox" name="is_active" value="1" :checked="currentItem.is_active" id="edit_is_active" class="rounded border-border text-brand-blue focus:ring-brand-blue">
                            <label for="edit_is_active" class="text-xs font-bold text-primary">Aktif Ditampilkan</label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-border">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-surface border border-border text-secondary text-sm font-semibold rounded-xl hover:bg-gray-100">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-brand-blue text-white text-sm font-bold rounded-xl hover:opacity-90 shadow">Update Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
