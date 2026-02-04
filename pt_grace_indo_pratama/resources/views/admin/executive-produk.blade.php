@extends('admin.layout.master')

@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
{{-- sesuaikan kalau menu produk kamu beda --}}
@section('menu-executive-produk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    @php
        // =========================
        // DUMMY DATA PRODUK (nanti ganti dari DB)
        // =========================
        $products = [
            [
                'id_produk' => 'BHOS001',
                'sku' => 'SKU-001',
                'nama_produk' => 'BHOS Ekstra',
                'stok_tersedia' => '150 Ltr',
            ],
            [
                'id_produk' => 'BHOS002',
                'sku' => 'SKU-002',
                'nama_produk' => 'BHOS Turbo',
                'stok_tersedia' => '150 Kg',
            ],
            [
                'id_produk' => 'BHOS003',
                'sku' => 'SKU-003',
                'nama_produk' => 'BHOS Ultra',
                'stok_tersedia' => '80 Ltr',
            ],
        ];
    @endphp

    <section class="mb-5">
        {{-- breadcrumb --}}
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Executive</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Produk</a>
        </div>
    </section>

    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        {{-- top bar: search + actions --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between mb-3">
            <div class="w-full lg:max-w-[560px]">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text"
                        class="block w-full rounded-lg border border-gray-400 bg-gray-100 pl-10 pr-3 py-2.5 text-sm text-gray-900 focus:border-gray-400 focus:ring-0"
                        placeholder="Search for ID / SKU / Nama Produk">
                </div>
            </div>

            <div class="flex items-center gap-2 justify-end">
                <a href="#"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2E7E3F] px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-300">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v6h6M20 20v-6h-6M20 8a8 0 00-14.9-3M4 16a8 8 0 0014.9 3" />
                    </svg>
                    Export .xlsx
                </a>

                <a href="{{ route('admin.add-executive-produk-baru') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2D2ACD] px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <span class="text-lg leading-none">+</span>
                    Tambah Produk
                </a>
            </div>
        </div>

        {{-- table --}}
        <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-800">
                    <thead class="bg-[#5aba6f]/70 text-gray-900">
                        <tr class="[&>th]:border-b [&>th]:border-gray-500">
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">ID Produk</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Stock Keeping Unit</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Nama Produk</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Stok Tersedia</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="bg-gray-200 divide-y divide-gray-500">
                        @foreach ($products as $p)
                            <tr class="hover:bg-gray-300">
                                <td class="px-6 py-4 text-left font-semibold">{{ $p['id_produk'] }}</td>
                                <td class="px-6 py-4 text-left font-semibold">{{ $p['sku'] }}</td>
                                <td class="px-6 py-4 text-left font-semibold leading-tight">{{ $p['nama_produk'] }}</td>
                                <td class="px-6 py-4 text-left font-semibold">{{ $p['stok_tersedia'] }}</td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-start gap-6 font-semibold">
                                        <a href="{{ route('admin.edit-executive-produk') }}"
                                            class="text-[#2D2ACD] hover:underline">Sunting</a>
                                        <a href="#" class="text-[#EC0000] hover:underline">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- footer / pagination (mobile + ipad aman) --}}
            <div
                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between
                       bg-gray-200 px-3 sm:px-4 md:px-5 py-3 sm:py-4 border-t border-gray-400">

                <div class="text-xs sm:text-sm font-semibold text-gray-700">
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

        {{-- ✅ MOBILE + iPAD CARD LIST (biar ga cuma scroll table) --}}
        {{-- table tetap ada untuk desktop/tablet yang nyaman scroll, tapi ini bikin pengalaman mobile lebih enak --}}
        <div class="mt-4 space-y-3 lg:hidden">
            @foreach ($products as $p)
                <section class="rounded-xl border border-gray-400 bg-gray-200 p-4 shadow-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <div class="text-[11px] font-extrabold text-gray-700">ID Produk</div>
                            <div class="text-sm font-semibold text-gray-900 break-all">{{ $p['id_produk'] }}</div>
                        </div>

                        <div>
                            <div class="text-[11px] font-extrabold text-gray-700">Stock Keeping Unit</div>
                            <div class="text-sm font-semibold text-gray-900 break-all">{{ $p['sku'] }}</div>
                        </div>

                        <div class="sm:col-span-2">
                            <div class="text-[11px] font-extrabold text-gray-700">Nama Produk</div>
                            <div class="text-sm font-semibold text-gray-900">{{ $p['nama_produk'] }}</div>
                        </div>

                        <div>
                            <div class="text-[11px] font-extrabold text-gray-700">Stok Tersedia</div>
                            <div class="text-sm font-semibold text-gray-900 break-all">{{ $p['stok_tersedia'] }}</div>
                        </div>
                    </div>

                    <div class="mt-3 flex items-center justify-end gap-3">
                        <a href="#"
                            class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-4 py-2 text-xs font-bold text-white hover:bg-blue-800">
                            Sunting
                        </a>
                        <a href="#"
                            class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700">
                            Hapus
                        </a>
                    </div>
                </section>
            @endforeach
        </div>
    </section>
@endsection
