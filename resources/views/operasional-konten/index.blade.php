<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full">
            <div>
                <h2 class="font-semibold text-2xl text-primary leading-tight">
                    Operasional Konten
                </h2>
                <p class="text-sm text-secondary mt-1">Input dan kelola link konten kreator (TikTok/Instagram) untuk diproses Scraping.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('operasional-konten.test-apify') }}" class="inline-flex items-center justify-center px-4 py-2 bg-body border border-border rounded-xl font-semibold text-xs text-primary uppercase tracking-widest hover:bg-gray-100 dark:hover:bg-gray-800 active:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-status-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Tes Koneksi Apify
                </a>
            </div>
        </div>
    </x-slot>

    <x-toast />

    <div class="space-y-6" x-data="{ 
        tab: 'single', 
        isLoaded: false,
        selectedIds: [],
        selectAll: false,
        toggleAll() {
            this.selectAll = !this.selectAll;
            if (this.selectAll) {
                this.selectedIds = Array.from(document.querySelectorAll('.link-checkbox')).map(cb => cb.value);
            } else {
                this.selectedIds = [];
            }
        }
    }" x-init="setTimeout(() => isLoaded = true, 1200)">
        
        @can('operasional-konten.create')
        <!-- Tabs & Form Section -->
        <div class="bg-surface rounded-2xl border border-border overflow-hidden shadow-sm">
            
            <!-- Tab Navigation -->
            <div class="flex border-b border-border bg-gray-50/50 dark:bg-gray-800/50 px-4 pt-4 gap-2">
                <button @click="tab = 'single'" :class="{'bg-surface text-brand-blue border-t border-x border-border font-semibold': tab === 'single', 'text-secondary hover:text-primary': tab !== 'single'}" class="px-5 py-3 rounded-t-xl transition-colors text-sm">
                    Single Input
                </button>
                <button @click="tab = 'bulk'" :class="{'bg-surface text-brand-blue border-t border-x border-border font-semibold': tab === 'bulk', 'text-secondary hover:text-primary': tab !== 'bulk'}" class="px-5 py-3 rounded-t-xl transition-colors text-sm">
                    Bulk Input (Teks)
                </button>
                <button @click="tab = 'csv'" :class="{'bg-surface text-brand-blue border-t border-x border-border font-semibold': tab === 'csv', 'text-secondary hover:text-primary': tab !== 'csv'}" class="px-5 py-3 rounded-t-xl transition-colors text-sm">
                    Upload CSV/Excel
                </button>
            </div>

            @if ($errors->any())
                <div class="m-6 bg-status-danger/10 text-status-danger p-4 rounded-xl text-sm border border-status-danger/20">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="p-6">
                @if($campaigns->isEmpty())
                    <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300 text-sm font-semibold flex items-center gap-3 shadow-xs">
                        <svg class="w-5 h-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Maaf, kamu masih belum ditugaskan campaign. Hubungi Admin Master untuk memberikan akses penugasan campaign.</span>
                    </div>
                @endif

                <!-- Single Input Form -->
                <form x-show="tab === 'single'" method="POST" action="{{ route('operasional-konten.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="type" value="single">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="url" value="URL Konten (TikTok / Instagram)" />
                            <x-text-input id="url" name="url" type="url" class="mt-1 block w-full" placeholder="https://www.tiktok.com/@user/video/..." required />
                        </div>
                        
                        <div>
                            <x-input-label for="campaign_id" value="Pilih Campaign" />
                            <select id="campaign_id" name="campaign_id" class="mt-1 block w-full border-border bg-body text-primary rounded-lg focus:border-brand-blue focus:ring-brand-blue shadow-sm" required {{ $campaigns->isEmpty() ? 'disabled' : '' }}>
                                @forelse($campaigns as $camp)
                                    @if($loop->first)
                                        <option value="">-- Pilih Campaign --</option>
                                    @endif
                                    <option value="{{ $camp->id }}">{{ $camp->nama_campaign }}</option>
                                @empty
                                    <option value="" disabled selected>-- Maaf kamu masih blm ditugaskan campign --</option>
                                @endforelse
                            </select>
                        </div>

                        <div>
                            <x-input-label for="kategori_konten" value="Kategori Konten" />
                            <select id="kategori_konten" name="kategori_konten_id" class="mt-1 block w-full border-border bg-body text-primary rounded-lg focus:border-brand-blue focus:ring-brand-blue shadow-sm" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoriKonten as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="kategori_creator" value="Kategori Creator" />
                            <select id="kategori_creator" name="kategori_creator_id" class="mt-1 block w-full border-border bg-body text-primary rounded-lg focus:border-brand-blue focus:ring-brand-blue shadow-sm" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoriCreator as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-border mt-6">
                        <x-primary-button :disabled="$campaigns->isEmpty()">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Simpan & Antrean Scraping
                        </x-primary-button>
                    </div>
                </form>

                <!-- Bulk Input Form -->
                <form x-show="tab === 'bulk'" style="display: none;" method="POST" action="{{ route('operasional-konten.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="type" value="bulk">
                    
                    <div>
                        <x-input-label for="urls" value="List URL Konten (Pisahkan dengan Enter/Baris Baru)" />
                        <textarea id="urls" name="urls" rows="6" class="mt-1 block w-full border-border bg-body text-primary rounded-lg focus:border-brand-blue focus:ring-brand-blue shadow-sm" placeholder="https://tiktok.com/...&#10;https://instagram.com/..." required></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="bulk_campaign_id" value="Pilih Campaign" />
                            <select id="bulk_campaign_id" name="campaign_id" class="mt-1 block w-full border-border bg-body text-primary rounded-lg focus:border-brand-blue shadow-sm" required {{ $campaigns->isEmpty() ? 'disabled' : '' }}>
                                @forelse($campaigns as $camp)
                                    @if($loop->first)
                                        <option value="">-- Pilih Campaign --</option>
                                    @endif
                                    <option value="{{ $camp->id }}">{{ $camp->nama_campaign }}</option>
                                @empty
                                    <option value="" disabled selected>-- Maaf kamu masih blm ditugaskan campign --</option>
                                @endforelse
                            </select>
                        </div>
                        <div>
                            <x-input-label for="bulk_kategori_konten" value="Kategori Konten" />
                            <select id="bulk_kategori_konten" name="kategori_konten_id" class="mt-1 block w-full border-border bg-body text-primary rounded-lg focus:border-brand-blue shadow-sm" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoriKonten as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="bulk_kategori_creator" value="Kategori Creator" />
                            <select id="bulk_kategori_creator" name="kategori_creator_id" class="mt-1 block w-full border-border bg-body text-primary rounded-lg focus:border-brand-blue shadow-sm" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoriCreator as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex justify-end pt-4 border-t border-border mt-6">
                        <x-primary-button :disabled="$campaigns->isEmpty()">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Proses Bulk Link
                        </x-primary-button>
                    </div>
                </form>

                <!-- CSV Upload Form -->
                <form x-show="tab === 'csv'" style="display: none;" method="POST" action="{{ route('operasional-konten.store') }}" enctype="multipart/form-data" class="space-y-6" x-data="{ fileName: '', fileSize: '' }">
                    @csrf
                    <input type="hidden" name="type" value="csv">
                    
                    <!-- Excel/Spreadsheet Style Format Preview -->
                    <div class="bg-surface border border-border rounded-2xl overflow-hidden shadow-xs">
                        <!-- Excel Window Header Bar -->
                        <div class="bg-gray-100 dark:bg-gray-800/80 px-4 py-2.5 border-b border-border flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                                    XLS
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-primary flex items-center gap-2">
                                        Format Kolom Excel / CSV
                                        <span class="px-2 py-0.2 rounded-full text-[10px] font-semibold bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">Wajib Kolom A</span>
                                    </h4>
                                </div>
                            </div>
                            <div class="text-[11px] text-secondary font-medium hidden sm:block">
                                Baris 1: Header (<span class="font-bold text-emerald-600 dark:text-emerald-400">URL Konten</span>) &bull; Baris 2+: Link TikTok / Instagram
                            </div>
                        </div>

                        <!-- Spreadsheet Grid Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs border-collapse font-sans">
                                <thead>
                                    <tr class="bg-gray-200/80 dark:bg-gray-700/80 text-secondary text-[11px] border-b border-border">
                                        <th class="w-10 px-2 py-1.5 text-center font-semibold border-r border-border bg-gray-200 dark:bg-gray-700 select-none"></th>
                                        <th class="px-3 py-1.5 font-bold text-center border-r border-border w-2/5 min-w-[280px]">A</th>
                                        <th class="px-3 py-1.5 font-bold text-center border-r border-border w-1/5 min-w-[100px] select-none text-muted">B</th>
                                        <th class="px-3 py-1.5 font-bold text-center border-r border-border w-1/5 min-w-[100px] select-none text-muted">C</th>
                                        <th class="px-3 py-1.5 font-bold text-center border-r border-border w-1/5 min-w-[100px] select-none text-muted">D</th>
                                        <th class="px-3 py-1.5 font-bold text-center border-r border-border select-none text-muted hidden md:table-cell">E</th>
                                        <th class="px-3 py-1.5 font-bold text-center select-none text-muted hidden md:table-cell">F</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border font-mono text-[11px]">
                                    <!-- Row 1: Header Row -->
                                    <tr class="bg-surface">
                                        <td class="px-2 py-1.5 text-center font-bold text-secondary bg-gray-100 dark:bg-gray-800/80 border-r border-border select-none">1</td>
                                        <td class="px-3 py-1.5 font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-500/10 border-2 border-emerald-500 dark:border-emerald-400 border-r border-border">
                                            <div class="flex items-center justify-between">
                                                <span>URL Konten</span>
                                                <span class="text-[9px] uppercase px-1.5 py-0.2 rounded bg-emerald-600 text-white font-sans font-extrabold">Header</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-1.5 border-r border-border bg-gray-50/50 dark:bg-gray-800/20"></td>
                                        <td class="px-3 py-1.5 border-r border-border bg-gray-50/50 dark:bg-gray-800/20"></td>
                                        <td class="px-3 py-1.5 border-r border-border bg-gray-50/50 dark:bg-gray-800/20"></td>
                                        <td class="px-3 py-1.5 border-r border-border bg-gray-50/50 dark:bg-gray-800/20 hidden md:table-cell"></td>
                                        <td class="px-3 py-1.5 bg-gray-50/50 dark:bg-gray-800/20 hidden md:table-cell"></td>
                                    </tr>
                                    <!-- Row 2: TikTok Example -->
                                    <tr class="bg-surface hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                        <td class="px-2 py-1.5 text-center font-bold text-secondary bg-gray-100 dark:bg-gray-800/80 border-r border-border select-none">2</td>
                                        <td class="px-3 py-1.5 text-primary border-r border-border truncate max-w-[320px]">
                                            https://www.tiktok.com/@creator/video/1234567890
                                        </td>
                                        <td class="px-3 py-1.5 border-r border-border"></td>
                                        <td class="px-3 py-1.5 border-r border-border"></td>
                                        <td class="px-3 py-1.5 border-r border-border"></td>
                                        <td class="px-3 py-1.5 border-r border-border hidden md:table-cell"></td>
                                        <td class="px-3 py-1.5 hidden md:table-cell"></td>
                                    </tr>
                                    <!-- Row 3: Instagram Example -->
                                    <tr class="bg-surface hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                        <td class="px-2 py-1.5 text-center font-bold text-secondary bg-gray-100 dark:bg-gray-800/80 border-r border-border select-none">3</td>
                                        <td class="px-3 py-1.5 text-primary border-r border-border truncate max-w-[320px]">
                                            https://www.instagram.com/p/Cxyz123456/
                                        </td>
                                        <td class="px-3 py-1.5 border-r border-border"></td>
                                        <td class="px-3 py-1.5 border-r border-border"></td>
                                        <td class="px-3 py-1.5 border-r border-border"></td>
                                        <td class="px-3 py-1.5 border-r border-border hidden md:table-cell"></td>
                                        <td class="px-3 py-1.5 hidden md:table-cell"></td>
                                    </tr>
                                    <!-- Row 4: Empty Row -->
                                    <tr class="bg-surface">
                                        <td class="px-2 py-1.5 text-center font-bold text-secondary bg-gray-100 dark:bg-gray-800/80 border-r border-border select-none">4</td>
                                        <td class="px-3 py-1.5 border-r border-border"></td>
                                        <td class="px-3 py-1.5 border-r border-border"></td>
                                        <td class="px-3 py-1.5 border-r border-border"></td>
                                        <td class="px-3 py-1.5 border-r border-border"></td>
                                        <td class="px-3 py-1.5 border-r border-border hidden md:table-cell"></td>
                                        <td class="px-3 py-1.5 hidden md:table-cell"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Dropzone File Upload -->
                    <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-44 border-2 border-border border-dashed rounded-xl cursor-pointer bg-body hover:bg-gray-50/80 dark:hover:bg-gray-800/50 transition-all duration-200 group relative overflow-hidden" :class="{'border-status-success bg-status-success/5': fileName}">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                <template x-if="!fileName">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 mb-3 rounded-2xl bg-brand-blue/10 text-brand-blue flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        </div>
                                        <p class="mb-1 text-sm text-primary font-medium"><span class="font-bold text-brand-blue hover:underline">Klik untuk pilih file CSV</span> atau tarik file ke sini</p>
                                        <p class="text-xs text-secondary">Format file yang didukung: <span class="font-semibold text-primary">.CSV</span> (Maksimal 5MB)</p>
                                    </div>
                                </template>
                                
                                <template x-if="fileName">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 mb-2 rounded-2xl bg-status-success/15 text-status-success flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <p class="text-sm font-bold text-status-success truncate max-w-md" x-text="fileName"></p>
                                        <p class="text-xs text-secondary mt-0.5">Ukuran File: <span class="font-semibold text-primary" x-text="fileSize"></span></p>
                                        <span class="mt-2 text-[11px] text-brand-blue font-semibold hover:underline">Klik untuk mengganti file</span>
                                    </div>
                                </template>
                            </div>
                            <input id="dropzone-file" type="file" name="file" class="hidden" accept=".csv" required @change="
                                if ($event.target.files.length > 0) {
                                    fileName = $event.target.files[0].name;
                                    fileSize = ($event.target.files[0].size / 1024).toFixed(1) + ' KB';
                                }
                            " />
                        </label>
                    </div>

                    <!-- Target Configuration Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="csv_campaign_id" value="Pilih Target Campaign" />
                            <select id="csv_campaign_id" name="campaign_id" class="mt-1 block w-full border-border bg-body text-primary rounded-lg focus:border-brand-blue shadow-sm" required {{ $campaigns->isEmpty() ? 'disabled' : '' }}>
                                @forelse($campaigns as $camp)
                                    @if($loop->first)
                                        <option value="">-- Pilih Campaign --</option>
                                    @endif
                                    <option value="{{ $camp->id }}">{{ $camp->nama_campaign }}</option>
                                @empty
                                    <option value="" disabled selected>-- Maaf kamu masih blm ditugaskan campign --</option>
                                @endforelse
                            </select>
                        </div>
                        <div>
                            <x-input-label for="csv_kategori_konten" value="Kategori Konten" />
                            <select id="csv_kategori_konten" name="kategori_konten_id" class="mt-1 block w-full border-border bg-body text-primary rounded-lg focus:border-brand-blue shadow-sm" required>
                                <option value="">-- Pilih Kategori Konten --</option>
                                @foreach($kategoriKonten as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="csv_kategori_creator" value="Kategori Creator" />
                            <select id="csv_kategori_creator" name="kategori_creator_id" class="mt-1 block w-full border-border bg-body text-primary rounded-lg focus:border-brand-blue shadow-sm" required>
                                <option value="">-- Pilih Kategori Creator --</option>
                                @foreach($kategoriCreator as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div> 
                    
                    <!-- Action Buttons Footer -->
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 pt-4 border-t border-border mt-6">
                        <a href="{{ route('operasional-konten.template') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-xl bg-body border border-border text-brand-blue hover:bg-brand-blue/10 transition-colors shadow-2xs">
                            <svg class="w-4 h-4 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Download Template CSV Resmi</span>
                        </a>
                        <x-primary-button :disabled="$campaigns->isEmpty()">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Upload File & Proses Batch</span>
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
        @endcan

        @if(($pendingCount ?? 0) > 0)
        <!-- Auto Refresh / Auto Scraping Banner -->
        <div x-data="autoRefreshScraper()" x-init="initAutoRefresh()" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="relative flex h-3.5 w-3.5 shrink-0">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-500 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-blue-600"></span>
                </div>
                <div>
                    <h4 class="font-semibold text-sm text-blue-700 dark:text-blue-300 flex items-center gap-2">
                        <span x-text="statusMessage">Auto-Scraping Sedang Berjalan...</span>
                    </h4>
                    <p class="text-xs text-secondary mt-0.5">Terdapat <strong class="text-primary font-bold">{{ $pendingCount }}</strong> link dengan status Pending. Sistem sedang otomatis memproses data melalui Apify.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button @click="triggerScrape()" :disabled="isLoading" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-blue-600 hover:bg-blue-700 text-white transition disabled:opacity-50 shadow-xs">
                    <svg x-show="isLoading" class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span x-text="isLoading ? 'Memproses Apify...' : 'Proses Sekarang'"></span>
                </button>
            </div>
        </div>

        <script>
            function autoRefreshScraper() {
                return {
                    isLoading: false,
                    statusMessage: 'Memulai auto-scraping data...',
                    initAutoRefresh() {
                        setTimeout(() => {
                            this.triggerScrape();
                        }, 1000);
                    },
                    async triggerScrape() {
                        if (this.isLoading) return;
                        this.isLoading = true;
                        this.statusMessage = 'Menghubungkan ke API Apify & mengambil metrik...';

                        try {
                            const res = await fetch("{{ route('operasional-konten.refresh') }}", {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            const data = await res.json();
                            if (data.success || (data.processed_count && data.processed_count > 0)) {
                                this.statusMessage = 'Data berhasil di-scrape! Memperbarui tabel...';
                            } else {
                                this.statusMessage = 'Proses scraping selesai. Reloading...';
                            }
                            setTimeout(() => {
                                window.location.reload();
                            }, 1200);
                        } catch (e) {
                            console.error("Auto scrape error:", e);
                            this.statusMessage = 'Gagal auto-scrape, akan dicoba kembali...';
                            this.isLoading = false;
                        }
                    }
                }
            }
        </script>
        @endif

        <!-- Table Data & Filter Section -->
        <div class="bg-surface rounded-2xl border border-border overflow-hidden shadow-sm mt-8">
            <div class="p-6 border-b border-border flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div>
                        <h3 class="text-lg font-bold text-primary">Riwayat Link Terbaru</h3>
                        <p class="text-xs text-secondary mt-0.5">Daftar link konten dan metrik engagement (Views, Likes, Comments, Shares, Saves, ER).</p>
                    </div>

                    @can('operasional-konten.delete')
                    <div x-show="selectedIds.length > 0" style="display: none;" class="flex items-center gap-2 bg-status-danger/10 text-status-danger px-3 py-1.5 rounded-xl border border-status-danger/20">
                        <span class="text-xs font-bold" x-text="selectedIds.length + ' dipilih'"></span>
                        <form method="POST" action="{{ route('operasional-konten.destroy-bulk') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua link terpilih?');">
                            @csrf
                            @method('DELETE')
                            <template x-for="id in selectedIds" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="inline-flex items-center gap-1 text-xs font-bold bg-status-danger text-white px-2.5 py-1 rounded-lg hover:bg-status-danger/90 transition shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Hapus Terpilih
                            </button>
                        </form>
                    </div>
                    @endcan
                </div>
                
                <!-- Filter & Search Form -->
                <form method="GET" action="{{ route('operasional-konten.index') }}" class="flex flex-wrap items-center gap-3">
                    <!-- Campaign Filter -->
                    <select name="campaign_id" onchange="this.form.submit()" class="text-xs border-border bg-body text-primary rounded-xl focus:border-brand-blue focus:ring-brand-blue py-2 px-3" {{ $campaigns->isEmpty() ? 'disabled' : '' }}>
                        @forelse($campaigns as $camp)
                            @if($loop->first)
                                <option value="">-- Semua Campaign --</option>
                            @endif
                            <option value="{{ $camp->id }}" {{ request('campaign_id') == $camp->id ? 'selected' : '' }}>
                                {{ $camp->nama_campaign }}
                            </option>
                        @empty
                            <option value="" disabled selected>-- Belum Ada Campaign Ditugaskan --</option>
                        @endforelse
                    </select>

                    <!-- Platform Filter -->
                    <select name="platform" onchange="this.form.submit()" class="text-xs border-border bg-body text-primary rounded-xl focus:border-brand-blue focus:ring-brand-blue py-2 px-3">
                        <option value="">-- Semua Platform --</option>
                        <option value="TikTok" {{ request('platform') == 'TikTok' ? 'selected' : '' }}>TikTok</option>
                        <option value="Instagram" {{ request('platform') == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                    </select>

                    <!-- Date Range Filter -->
                    <div class="flex items-center gap-1 bg-body border border-border rounded-xl px-2 py-1">
                        <span class="text-[11px] text-secondary font-medium pl-1">Tgl:</span>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()" class="text-xs bg-transparent border-0 text-primary focus:ring-0 p-1" title="Tanggal Mulai">
                        <span class="text-xs text-secondary">-</span>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()" class="text-xs bg-transparent border-0 text-primary focus:ring-0 p-1" title="Tanggal Selesai">
                    </div>

                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari URL / User..." class="text-xs border-border bg-body text-primary rounded-xl pl-8 pr-3 py-2 focus:border-brand-blue focus:ring-brand-blue min-w-[150px]">
                        <svg class="w-3.5 h-3.5 absolute left-2.5 top-2.5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>

                    @if(request()->hasAny(['campaign_id', 'platform', 'search', 'start_date', 'end_date']))
                        <a href="{{ route('operasional-konten.index') }}" class="text-xs text-status-danger hover:underline font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Skeleton Loader (shows for 1.2s via Alpine) -->
            <div x-show="!isLoaded">
                <x-skeleton count="4" />
            </div>

            <!-- Actual Table (shows after 1.2s) -->
            <div x-show="isLoaded" style="display: none;" class="overflow-x-auto">
                <table class="w-full text-sm text-left text-primary">
                    <thead class="text-xs text-secondary uppercase bg-body/50 border-b border-border">
                        <tr>
                            @can('operasional-konten.delete')
                            <th scope="col" class="px-4 py-3.5 w-10 text-center">
                                <input type="checkbox" @click="toggleAll()" x-model="selectAll" class="rounded border-border text-brand-blue focus:ring-brand-blue cursor-pointer" title="Pilih Semua">
                            </th>
                            @endcan
                            <th scope="col" class="px-4 py-3.5 font-semibold">Tanggal</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold">Platform & Akun</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold">Campaign</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold text-right">Views</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold text-right">Likes</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold text-right">Comments</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold text-right">Shares</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold text-right">Saves</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold text-right">ER (%)</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold text-center">Status</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($links as $link)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors" :class="{'bg-blue-50/50 dark:bg-blue-900/10': selectedIds.includes('{{ $link->id }}')}">
                            @can('operasional-konten.delete')
                            <td class="px-4 py-3.5 text-center">
                                <input type="checkbox" value="{{ $link->id }}" x-model="selectedIds" class="link-checkbox rounded border-border text-brand-blue focus:ring-brand-blue cursor-pointer">
                            </td>
                            @endcan

                            <!-- Tanggal -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="text-xs text-primary font-semibold block">
                                    {{ $link->tanggal_upload ? \Carbon\Carbon::parse($link->tanggal_upload)->format('d/m/Y') : ($link->updated_at ? \Carbon\Carbon::parse($link->updated_at)->format('d/m/Y') : '-') }}
                                </span>
                            </td>
                            <!-- Platform & Akun -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    @if(strtolower($link->platform) === 'tiktok')
                                        <div class="w-7 h-7 rounded-lg bg-black text-white flex items-center justify-center shrink-0 shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                                        </div>
                                    @else
                                        <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-bold text-xs text-primary truncate max-w-[120px]" title="{{ $link->username }}">{{ $link->username ?? 'Sedang Scraping...' }}</p>
                                        <a href="{{ $link->url }}" target="_blank" class="text-[11px] text-brand-blue hover:underline truncate inline-block max-w-[120px]">{{ $link->url }}</a>
                                    </div>
                                </div>
                            </td>

                            <!-- Campaign -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-semibold text-xs text-primary block truncate max-w-[110px]" title="{{ $link->campaign->nama_campaign ?? '-' }}">
                                    {{ $link->campaign->nama_campaign ?? '-' }}
                                </span>
                                <span class="text-[10px] text-secondary">
                                    {{ $link->kategoriKonten->nama ?? 'Umum' }}
                                </span>
                            </td>

                            <!-- Views -->
                            <td class="px-4 py-3.5 text-right font-medium text-xs whitespace-nowrap">
                                {{ number_format($link->views ?? 0) }}
                            </td>

                            <!-- Likes -->
                            <td class="px-4 py-3.5 text-right font-medium text-xs whitespace-nowrap">
                                {{ number_format($link->likes ?? 0) }}
                            </td>

                            <!-- Comments -->
                            <td class="px-4 py-3.5 text-right font-medium text-xs whitespace-nowrap">
                                {{ number_format($link->comments ?? 0) }}
                            </td>

                            <!-- Shares -->
                            <td class="px-4 py-3.5 text-right font-medium text-xs whitespace-nowrap">
                                {{ number_format($link->shares ?? 0) }}
                            </td>

                            <!-- Saves -->
                            <td class="px-4 py-3.5 text-right font-medium text-xs whitespace-nowrap">
                                {{ number_format($link->saves ?? 0) }}
                            </td>

                            <!-- ER (%) -->
                            <td class="px-4 py-3.5 text-right font-semibold text-xs whitespace-nowrap text-brand-blue">
                                {{ number_format($link->engagement_rate ?? 0, 2) }}%
                            </td>

                            <!-- Status Scraping -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                @if($link->status_scraping === 'Pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-status-warning/10 text-status-warning">
                                        <svg class="w-3 h-3 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Pending
                                    </span>
                                @elseif(in_array($link->status_scraping, ['Completed', 'Berhasil']))
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-status-success/10 text-status-success">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-status-danger/10 text-status-danger">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Gagal
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('operasional-konten.show', $link->id) }}" class="text-secondary hover:text-brand-blue p-1.5 rounded-lg hover:bg-brand-blue/10 transition-colors" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    @can('operasional-konten.delete')
                                    <form action="{{ route('operasional-konten.destroy', $link->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus link ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-secondary hover:text-status-danger p-1.5 rounded-lg hover:bg-status-danger/10 transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="px-6 py-8 text-center text-secondary">
                                Belum ada link yang diinputkan atau sesuai dengan filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="px-6 py-4 border-t border-border bg-body/30">
                {{ $links->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
