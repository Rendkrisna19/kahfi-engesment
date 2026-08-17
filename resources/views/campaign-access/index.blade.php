<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                Kelola Hak Akses Campaign
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Atur pengguna yang dapat mengakses setiap campaign.
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

            {{-- Card --}}
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">

                <div class="border-b border-gray-200 px-8 py-6">

                    <h3 class="text-lg font-semibold text-gray-800">
                        Daftar Campaign
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Pilih campaign untuk mengatur hak akses pengguna.
                    </p>

                </div>

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">
                            <tr>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    No
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Campaign
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Client
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Periode
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Status
                                </th>

                                <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Aksi
                                </th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">

                            @forelse ($campaigns as $campaign)

                                <tr class="hover:bg-gray-50">

                                    {{-- No --}}
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                        {{ $loop->iteration }}
                                    </td>

                                    {{-- Campaign --}}
                                    <td class="px-6 py-4">

                                        <div class="font-semibold text-gray-800">
                                            {{ $campaign->nama_campaign }}
                                        </div>

                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ $campaign->platform }}
                                        </div>

                                    </td>

                                    {{-- Client --}}
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                        {{ $campaign->client->name ?? '-' }}
                                    </td>

                                    {{-- Periode --}}
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">

                                        <div>
                                            {{ \Carbon\Carbon::parse($campaign->tanggal_mulai)->format('d/m/Y') }}
                                        </div>

                                        <div class="text-xs text-gray-400">
                                            s/d
                                            {{ \Carbon\Carbon::parse($campaign->tanggal_selesai)->format('d/m/Y') }}
                                        </div>

                                    </td>

                                    {{-- Status --}}
                                    <td class="whitespace-nowrap px-6 py-4">

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

                                    </td>

                                    {{-- Aksi --}}
                                    <td class="whitespace-nowrap px-6 py-4 text-center">

                                        <a
                                            href="{{ route('campaign-access.edit', $campaign) }}"
                                            style="
                                                display: inline-flex;
                                                align-items: center;
                                                justify-content: center;
                                                padding: 8px 16px;
                                                border-radius: 6px;
                                                background-color: #4f46e5;
                                                color: #ffffff;
                                                font-size: 12px;
                                                font-weight: 600;
                                                text-decoration: none;
                                                text-transform: uppercase;
                                                letter-spacing: 0.05em;
                                                white-space: nowrap;
                                            "
                                        >
                                            Kelola Hak Akses
                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="6"
                                        class="px-6 py-12 text-center"
                                    >

                                        <div class="text-sm font-medium text-gray-600">
                                            Belum ada campaign.
                                        </div>

                                        <div class="mt-1 text-sm text-gray-400">
                                            Tambahkan campaign terlebih dahulu melalui menu Kelola Campaign.
                                        </div>

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