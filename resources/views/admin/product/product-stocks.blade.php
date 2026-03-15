@extends('admin.layout.master')

{{-- sidebar active (sesuaikan menu kamu) --}}
@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-produk-stock', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('content')

    {{-- breadcrumb --}}
    <section class="mb-5">
        {{-- breadcrumb --}}
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Executive</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Pengadaan Barang</a>
        </div>
    </section>
    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        {{-- top bar --}}
        <form method="GET" class="mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5  gap-4 items-end">

                {{-- Search --}}
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Nama
                    </label>
                    <input type="text" name="name" value="{{ request('name') }}" placeholder="Nama"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>

                {{-- Province --}}
                {{-- <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Provinsi
                    </label>
                    <select name="province"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none">
                        <option value="">Semua Provinsi</option>
                        @foreach ($provinces as $prov)
                            <option value="{{ $prov }}" @selected(request('province') == $prov)>
                                {{ $prov }}
                            </option>
                        @endforeach
                    </select>
                </div> --}}


                {{-- Per Page --}}
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

                <button type="submit"
                    class="rounded-md bg-green-600 px-5 py-2 text-sm font-semibold text-white hover:bg-green-800 transition">
                    Filter
                </button>

                <a href="{{ route('shippments') }}"
                    class="rounded-md bg-red-600 px-5 py-2 text-sm font-semibold text-white hover:bg-red-800 transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </section>
    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        <div class="mb-5 flex items-center gap-5">
            <a href="{{ route('procurements.export', request()->query()) }}"
                class="inline-flex items-center
                gap-2 rounded-lg bg-[#2E7E3F] px-5 py-2 text-sm font-semibold text-white hover:bg-green-800
                focus:outline-none focus:ring-2 focus:ring-green-300">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v6h6M20 20v-6h-6M20 8a8 0 00-14.9-3M4 16a8 0 0014.9 3" />
                </svg>
                Export .xlsx
            </a>

            <a href="{{ route('create-shippment') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-[#2D2ACD] px-6 py-2 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                <span class="text-lg leading-none">+</span>
                Tambah Baru
            </a>
        </div>
        {{-- table --}}
        <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-900">
                    <thead class="bg-[#5aba6f]/70 text-gray-900">
                        <tr class="[&>th]:border-b [&>th]:border-gray-500">
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Product</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Province</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Stock</th>
                        </tr>
                    </thead>

                    <tbody class="bg-gray-200 divide-y divide-gray-500">
                        @forelse ($productStocks as $productStock)
                            <tr class="[&>td]:border-b [&>td]:border-gray-400 hover:bg-gray-100">
                                <td class="px-6 py-4 font-medium">{{ $productStock->productVariant->name }}</td>
                                <td class="px-6 py-4 font-medium">{{ $productStock->province }}</td>
                                <td class="px-6 py-4 font-medium">{{ $productStock->stock }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Data Tidak Ada</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- footer / pagination (mobile + ipad aman) --}}
            <div
                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between
                       bg-gray-200 px-3 sm:px-4 md:px-5 py-3 sm:py-4 border-t border-gray-400">

                <div class="text-xs sm:text-sm font-semibold text-gray-800">
                    Showing {{ $productStocks->firstItem() ?? 0 }}–{{ $productStocks->lastItem() ?? 0 }} of
                    {{ $productStocks->total() }}
                </div>

                <div class="w-full sm:w-auto overflow-x-auto">
                    <div class="pagination">
                        {{ $productStocks->links() }}
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
