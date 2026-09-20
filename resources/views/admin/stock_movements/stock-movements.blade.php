@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-stok-movement', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')

    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Stok Movement</a>
        </div>
    </section>

    {{-- Category Tabs --}}
    <div class="mb-5 flex border-b border-gray-300">
        @can('baca history stok bahan baku')
            <a href="{{ route('admin.gudang-stok-movement', array_merge(request()->query(), ['category' => 'raw_material'])) }}"
                class="px-6 py-3 font-semibold text-sm transition border-b-2 {{ $category === 'raw_material' ? 'border-[#275931] text-[#275931] bg-white rounded-t-lg' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                📦 Mutasi Bahan Baku
            </a>
        @endcan
        @can('baca history stok produk')
            <a href="{{ route('admin.gudang-stok-movement', array_merge(request()->query(), ['category' => 'product'])) }}"
                class="px-6 py-3 font-semibold text-sm transition border-b-2 {{ $category === 'product' ? 'border-[#275931] text-[#275931] bg-white rounded-t-lg' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                🏷️ Mutasi Produk Jadi
            </a>
        @endcan
    </div>

    {{-- Filter Form --}}
    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        <form action="" method="get">
            <input type="hidden" name="category" value="{{ $category }}" />

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        {{ $category === 'product' ? 'SKU' : 'Kode Barang' }}
                    </label>
                    <input type="text" name="code" value="{{ request('code') }}"
                        placeholder="{{ $category === 'product' ? 'SKU' : 'Kode Barang' }}"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>

                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        {{ $category === 'product' ? 'Nama Produk' : 'Nama Bahan' }}
                    </label>
                    <input type="text" name="name" value="{{ request('name') }}"
                        placeholder="{{ $category === 'product' ? 'Nama Produk' : 'Nama Bahan' }}"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>

                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Keterangan
                    </label>
                    <input type="text" name="note" value="{{ request('note') }}"
                        placeholder="Keterangan / Kode Mutasi"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>

                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Gudang
                    </label>
                    <select name="warehouse_id"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none">
                        <option value="">Semua Gudang</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" @selected(request('warehouse_id') == $warehouse->id)>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Tipe Mutasi
                    </label>
                    <select name="type"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none">
                        <option value="">Semua Tipe</option>
                        <option value="in" @selected(request('type') === 'in' || request('type') === 'In')>Masuk (In)</option>
                        <option value="out" @selected(request('type') === 'out' || request('type') === 'Out')>Keluar (Out)</option>
                    </select>
                </div>

                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Tanggal Mulai
                    </label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>

                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Tanggal Akhir
                    </label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>

                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Tampilkan
                    </label>
                    <select name="per_page"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none"
                        onchange="this.form.submit()">
                        @foreach ([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" @selected((int) request('per_page', 10) === $n)>
                                {{ $n }} / halaman
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2 col-span-1 sm:col-span-2 md:col-span-3 lg:col-span-4 justify-end pt-1">
                    <button type="submit"
                        class="w-full sm:w-auto px-6 py-2 rounded-md bg-green-600 text-sm font-semibold text-white hover:bg-green-800 transition">
                        Filter
                    </button>

                    <a href="{{ route('admin.gudang-stok-movement', ['category' => $category]) }}"
                        class="w-full sm:w-auto px-6 py-2 rounded-md bg-red-600 text-sm font-semibold text-white hover:bg-red-800 transition text-center">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </section>

    {{-- Table Section --}}
    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        {{-- Top Bar --}}
        <div class="mb-5 flex items-center justify-between">
            <a href="{{ route('admin.gudang-stok-movement.export', request()->query()) }}"
                class="inline-flex items-center gap-2 rounded-lg bg-[#2E7E3F] px-5 py-2 text-sm font-semibold text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-300">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v6h6M20 20v-6h-6M20 8a8 0 00-14.9-3M4 16a8 0 0014.9 3" />
                </svg>
                Export .xlsx
            </a>

            <div class="text-sm font-semibold text-gray-600">
                Total Mutasi: <span class="text-gray-900 font-bold">{{ $movements->total() }}</span> record
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-900">
                    <thead class="bg-[#5aba6f]/70 text-gray-900">
                        <tr class="[&>th]:border-b [&>th]:border-gray-500">
                            <th scope="col" class="px-5 py-4 font-extrabold text-left">Waktu</th>
                            <th scope="col" class="px-5 py-4 font-extrabold text-left">Gudang</th>
                            @if ($category === 'product')
                                <th scope="col" class="px-5 py-4 font-extrabold text-left">SKU</th>
                                <th scope="col" class="px-5 py-4 font-extrabold text-left">Produk</th>
                            @else
                                <th scope="col" class="px-5 py-4 font-extrabold text-left">Kode Barang</th>
                                <th scope="col" class="px-5 py-4 font-extrabold text-left">Bahan Baku</th>
                            @endif
                            <th scope="col" class="px-5 py-4 font-extrabold text-center">Tipe</th>
                            <th scope="col" class="px-5 py-4 font-extrabold text-right">Jumlah</th>
                            @if ($category === 'raw_material')
                                <th scope="col" class="px-5 py-4 font-extrabold text-left">Dicatat Oleh</th>
                            @endif
                            <th scope="col" class="px-5 py-4 font-extrabold text-left">Keterangan</th>
                        </tr>
                    </thead>

                    <tbody class="bg-gray-200 divide-y divide-gray-500">
                        @forelse ($movements as $m)
                            @php
                                $isMasuk = strtolower($m->type) === 'in';
                            @endphp
                            <tr class="hover:bg-gray-300">
                                <td class="px-5 py-4 font-semibold text-gray-800 whitespace-nowrap">
                                    {{ $m->created_at ? $m->created_at->timezone(config('app.timezone', 'Asia/Jakarta'))->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-5 py-4 font-semibold">
                                    {{ $m->warehouse->name ?? '-' }}
                                </td>

                                @if ($category === 'product')
                                    <td class="px-5 py-4 font-semibold text-gray-800">
                                        {{ $m->productStock?->productVariant?->sku ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 font-semibold">
                                        {{ $m->productStock?->productVariant?->name ?? '-' }}
                                    </td>
                                @else
                                    <td class="px-5 py-4 font-semibold text-gray-800">
                                        {{ $m->rawMaterial->code ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 font-semibold">
                                        {{ $m->rawMaterial->name ?? '-' }}
                                    </td>
                                @endif

                                <td class="px-5 py-4 text-center">
                                    @if ($isMasuk)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-400">
                                            📥 Masuk
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-400">
                                            📤 Keluar
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 font-bold text-right whitespace-nowrap {{ $isMasuk ? 'text-green-700' : 'text-red-700' }}">
                                    @if ($category === 'product')
                                        {{ $isMasuk ? '+' : '-' }}{{ $m->quantity }} {{ $m->productStock?->productVariant?->unit ?? 'unit' }}
                                    @else
                                        {{ $isMasuk ? '+' : '-' }}{{ $m->stock }} {{ $m->rawMaterial?->unit ?? '' }}
                                    @endif
                                </td>

                                @if ($category === 'raw_material')
                                    <td class="px-5 py-4 font-semibold text-gray-700">
                                        {{ $m->responsible->name ?? '-' }}
                                    </td>
                                @endif

                                <td class="px-5 py-4 text-sm text-gray-700">
                                    {{ $m->note ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $category === 'raw_material' ? 8 : 7 }}" class="px-6 py-6 text-center font-semibold text-gray-600">
                                    Belum ada riwayat mutasi stok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- footer / pagination --}}
            {{ $movements->links('vendor.pagination.pagination') }}
        </div>
    </section>
@endsection
