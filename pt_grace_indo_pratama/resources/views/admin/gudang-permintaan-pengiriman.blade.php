@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-permintaan-pengiriman', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')

    {{-- breadcrumb --}}
    <section class=mb-5">
        {{-- breadcrumb --}}
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Permintaan Pengiriman</a>
        </div>
    </section>

    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        {{-- top bar --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between mb-3">
            <div class="w-full lg:max-w-[520px]">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text"
                        class="block w-full rounded-lg border border-gray-400 bg-gray-100 pl-10 pr-3 py-2.5 text-sm text-gray-900 focus:border-gray-500 focus:ring-0"
                        placeholder="Search for Name and Date">
                </div>
            </div>

            <div class="flex items-center gap-2 justify-end">
                <a href="#"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2E7E3F] px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-300">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v6h6M20 20v-6h-6M20 8a8 8 0 00-14.9-3M4 16a8 8 0 0014.9 3" />
                    </svg>
                    Export .xlsx
                </a>

                <a href="#"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2D2ACD] px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <span class="text-lg leading-none">+</span>
                    Tambah Baru
                </a>
            </div>
        </div>

        {{-- table --}}
        <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-900">
                    <thead class="bg-[#5aba6f]/70 text-gray-900">
                        <tr class="[&>th]:border-b [&>th]:border-gray-500">
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Tanggal Pemesanan</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Tanggal Pengiriman</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Nama Pemesan</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Tujuan</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Provinsi</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Aksi</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Status</th>
                        </tr>
                    </thead>

                    @php
                        $rows = [
                            [
                                '30/11/2025',
                                '01/12/2025',
                                'Bambang Pratama Putra Hadi',
                                'PT Pamor Ganda',
                                'Kalimantan Barat',
                                'Menunggu',
                                'text-gray-600',
                            ],
                            [
                                '30/11/2025',
                                '01/12/2025',
                                'Bambang Pratama Putra Hadi',
                                'PT Pamor Ganda',
                                'Riau',
                                'Ditolak',
                                'text-[#EC0000]',
                            ],
                            [
                                '30/11/2025',
                                '01/12/2025',
                                'Bambang Pratama Putra Hadi',
                                'PT Pamor Ganda',
                                'Bengkulu',
                                'Dikirim',
                                'text-[#2E7E3F]',
                            ],
                            [
                                '30/11/2025',
                                '01/12/2025',
                                'Bambang Pratama Putra Hadi',
                                'PT Pamor Ganda',
                                'Medan',
                                'Dikirim',
                                'text-[#2E7E3F]',
                            ],
                            [
                                '30/11/2025',
                                '01/12/2025',
                                'Bambang Pratama Putra Hadi',
                                'PT Pamor Ganda',
                                'Sumatera Barat',
                                'Dikirim',
                                'text-[#2E7E3F]',
                            ],
                        ];
                    @endphp

                    <tbody class="bg-gray-200 divide-y divide-gray-500">
                        @foreach ($rows as $r)
                            <tr class="hover:bg-gray-300">
                                <td class="px-6 py-4 font-semibold">{{ $r[0] }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $r[1] }}</td>
                                <td class="px-6 py-4 font-semibold leading-tight">{{ $r[2] }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $r[3] }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $r[4] }}</td>
                                <td class="px-6 py-4 font-semibold">
                                    <a href="#" class="text-[#2D2ACD] hover:underline">Lihat</a>
                                </td>
                                <td class="px-6 py-4 font-semibold {{ $r[6] }}">{{ $r[5] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- footer / pagination (mobile + ipad aman) --}}
            <div
                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between
                       bg-gray-200 px-3 sm:px-4 md:px-5 py-3 sm:py-4 border-t border-gray-400">

                <div class="text-xs sm:text-sm font-semibold text-gray-800">
                    Showing 1–10 of 100
                </div>

                <div class="w-full sm:w-auto overflow-x-auto">
                    <div class="inline-flex w-max rounded-lg border border-gray-400 overflow-hidden shadow-sm">
                        <a href="#"
                            class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold bg-gray-200 hover:bg-gray-300 border-r border-gray-400">
                            Previous
                        </a>
                        <a href="#"
                            class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold bg-gray-200 hover:bg-gray-300 border-r border-gray-400">
                            1
                        </a>
                        <a href="#"
                            class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold bg-gray-200 hover:bg-gray-300 border-r border-gray-400">
                            2
                        </a>
                        <span
                            class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold bg-gray-200 border-r border-gray-400">
                            …
                        </span>
                        <a href="#"
                            class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold bg-gray-200 hover:bg-gray-300 border-r border-gray-400">
                            10
                        </a>
                        <a href="#"
                            class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold bg-gray-200 hover:bg-gray-300">
                            Next
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
