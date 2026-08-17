<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                Campaign Saya
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Pilih campaign yang telah ditugaskan kepada Anda.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">

            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Header --}}
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800">
                    Daftar Campaign
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Campaign yang dapat Anda kelola berdasarkan hak akses.
                </p>
            </div>

            {{-- Campaign --}}
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">

                @forelse ($campaigns as $campaign)

                    <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-200">

                        <div class="p-6">

                            {{-- Nama Campaign --}}
                            <h4 class="text-lg font-semibold text-gray-800">
                                {{ $campaign->nama_campaign }}
                            </h4>

                            {{-- Client --}}
                            <div class="mt-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Client
                                </p>

                                <p class="mt-1 text-sm text-gray-700">
                                    {{ $campaign->client->name ?? '-' }}
                                </p>
                            </div>

                            {{-- Platform --}}
                            <div class="mt-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Platform
                                </p>

                                <p class="mt-1 text-sm text-gray-700">
                                    {{ $campaign->platform }}
                                </p>
                            </div>

                            {{-- Periode --}}
                            <div class="mt-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Periode
                                </p>

                                <p class="mt-1 text-sm text-gray-700">
                                    {{ \Carbon\Carbon::parse($campaign->tanggal_mulai)->format('d/m/Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($campaign->tanggal_selesai)->format('d/m/Y') }}
                                </p>
                            </div>

                            {{-- Status --}}
                            <div class="mt-4">

                                @if ($campaign->status === 'Aktif')

                                    <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Aktif
                                    </span>

                                @elseif ($campaign->status === 'Draft')

                                    <span class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                        Draft
                                    </span>

                                @elseif ($campaign->status === 'Selesai')

                                    <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                        Selesai
                                    </span>

                                @else

                                    <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                        Arsip
                                    </span>

                                @endif

                            </div>

                            {{-- Pilih Campaign --}}
                            <div class="mt-6">

                                <a
                                    href="{{ route('admin.campaigns.show', $campaign) }}"
                                    class="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
                                >
                                    Pilih Campaign
                                </a>

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="col-span-full rounded-lg bg-white px-6 py-12 text-center shadow-sm ring-1 ring-gray-200">

                        <h4 class="text-sm font-semibold text-gray-700">
                            Belum ada campaign
                        </h4>

                        <p class="mt-1 text-sm text-gray-400">
                            Anda belum diberikan akses ke campaign oleh Admin Master.
                        </p>

                    </div>

                @endforelse

            </div>

        </div>
    </div>

</x-app-layout>