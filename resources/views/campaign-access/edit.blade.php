<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                Kelola Hak Akses Campaign
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Atur pengguna yang dapat mengakses campaign ini.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-4xl px-6 lg:px-8">

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">

                {{-- Informasi Campaign --}}
                <div class="border-b border-gray-200 px-8 py-6">

                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ $campaign->nama_campaign }}
                    </h3>

                    <div class="mt-3 grid grid-cols-1 gap-4 text-sm md:grid-cols-2">

                        <div>
                            <span class="text-gray-500">
                                Client
                            </span>

                            <div class="mt-1 font-medium text-gray-800">
                                {{ $campaign->client->name ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <span class="text-gray-500">
                                Platform
                            </span>

                            <div class="mt-1 font-medium text-gray-800">
                                {{ $campaign->platform }}
                            </div>
                        </div>

                        <div>
                            <span class="text-gray-500">
                                Periode
                            </span>

                            <div class="mt-1 font-medium text-gray-800">
                                {{ \Carbon\Carbon::parse($campaign->tanggal_mulai)->format('d/m/Y') }}
                                s/d
                                {{ \Carbon\Carbon::parse($campaign->tanggal_selesai)->format('d/m/Y') }}
                            </div>
                        </div>

                        <div>
                            <span class="text-gray-500">
                                Status
                            </span>

                            <div class="mt-1 font-medium text-gray-800">
                                {{ $campaign->status }}
                            </div>
                        </div>

                    </div>

                </div>


                {{-- Form Hak Akses --}}
                <form
                    method="POST"
                    action="{{ route('campaign-access.update', $campaign) }}"
                >

                    @csrf
                    @method('PUT')

                    <div class="px-8 py-6">

                        <div class="mb-5">

                            <h4 class="text-base font-semibold text-gray-800">
                                Pengguna yang memiliki akses
                            </h4>

                            <p class="mt-1 text-sm text-gray-500">
                                Centang Admin atau Client yang diperbolehkan mengakses campaign ini.
                            </p>

                        </div>


                        @if ($users->count() > 0)

                            <div class="space-y-3">

                                @foreach ($users as $user)

                                    <label
                                        for="user_{{ $user->id }}"
                                        class="flex cursor-pointer items-center justify-between rounded-lg border border-gray-200 p-4 transition hover:bg-gray-50"
                                    >

                                        <div class="flex items-center gap-4">

                                            <input
                                                id="user_{{ $user->id }}"
                                                type="checkbox"
                                                name="user_ids[]"
                                                value="{{ $user->id }}"
                                                {{ in_array($user->id, $accessUserIds) ? 'checked' : '' }}
                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            >

                                            <div>

                                                <div class="font-medium text-gray-800">
                                                    {{ $user->name }}
                                                </div>

                                                <div class="text-sm text-gray-500">
                                                    {{ $user->username }}
                                                    •
                                                    {{ $user->email }}
                                                </div>

                                            </div>

                                        </div>


                                        <span
                                            class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                            {{ $user->role === 'Admin'
                                                ? 'bg-indigo-100 text-indigo-700'
                                                : 'bg-green-100 text-green-700' }}"
                                        >
                                            {{ $user->role }}
                                        </span>

                                    </label>

                                @endforeach

                            </div>

                        @else

                            <div class="rounded-lg bg-gray-50 px-6 py-8 text-center">

                                <p class="text-sm font-medium text-gray-600">
                                    Belum ada Admin atau Client aktif.
                                </p>

                                <p class="mt-1 text-sm text-gray-400">
                                    Tambahkan user aktif terlebih dahulu melalui Kelola User.
                                </p>

                            </div>

                        @endif


                        <x-input-error
                            :messages="$errors->get('user_ids')"
                            class="mt-3"
                        />

                    </div>


                    {{-- Tombol --}}
                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 bg-gray-50 px-8 py-5">

                        <a
                            href="{{ route('campaign-access.index') }}"
                            class="inline-flex items-center rounded-md bg-gray-200 px-5 py-2.5 text-sm font-semibold uppercase tracking-wider text-gray-700 hover:bg-gray-300"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-semibold uppercase tracking-wider text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Simpan Hak Akses
                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>