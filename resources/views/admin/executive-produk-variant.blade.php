@extends('admin.layout.master')

@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-produk-variant', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')

    {{-- Breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span>Executive</span>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600">Produk Varian</span>
        </div>

        {{-- flash message --}}
        @if (session('success'))
            <div class="mt-3 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('success') }}
            </div>
        @endif
    </section>

    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">

        {{-- TOP BAR --}}
        {{-- FILTER --}}
        <form method="GET" action="{{ route('admin.executive-produk-variant') }}" class="mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 items-end">

                {{-- SKU --}}
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">SKU</label>
                    <input type="text" name="sku" value="{{ request('sku') }}" placeholder="SKU"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>

                {{-- Nama --}}
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">Nama Produk</label>
                    <input type="text" name="name" value="{{ request('name') }}" placeholder="Nama Produk"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>

                {{-- Status --}}
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">Status</label>
                    <select name="status"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                        <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
                    </select>
                </div>

                {{-- Per Page --}}
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">Tampilkan</label>
                    <select name="per_page"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none"
                        onchange="this.form.submit()">
                        @foreach ([10, 25, 50, 100, 500] as $n)
                            <option value="{{ $n }}" @selected((int) request('per_page', 10) === $n)>
                                {{ $n }} / halaman
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Button --}}
                <button type="submit"
                    class="rounded-md bg-green-600 px-5 py-2 text-sm font-semibold text-white hover:bg-green-800 transition">
                    Filter
                </button>

                <a href="{{ route('admin.executive-produk-variant') }}"
                    class="rounded-md bg-red-600 px-5 py-2 text-sm font-semibold text-white hover:bg-red-800 transition text-center">
                    Reset
                </a>
            </div>
        </form>
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between mb-3">
            <div class="flex items-center gap-2 justify-end">
                {{-- Export (placeholder) --}}
                <a href="{{ route('admin.executive-produk-variant.export') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2E7E3F] px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-300">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v6h6M20 20v-6h-6M20 8a8 0 00-14.9-3M4 16a8 0 0014.9 3" />
                    </svg>
                    Export .xlsx
                </a>

                @can('tambah produk varian')
                    <a href="{{ route('admin.add-executive-produk-variant') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#2D2ACD] px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <span class="text-lg leading-none">+</span>
                        Tambah Produk
                    </a>
                @endcan
            </div>
        </div>

        {{-- TABLE --}}
        <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-800">
                    <thead class="bg-[#5aba6f]/70 text-gray-900">
                        <tr class="[&>th]:border-b [&>th]:border-gray-500">
                            <th class="px-6 py-4 font-extrabold whitespace-nowrap">SKU</th>
                            <th class="px-6 py-4 font-extrabold whitespace-nowrap">Nama Produk</th>
                            <th class="px-6 py-4 font-extrabold whitespace-nowrap">Ukuran Kemasan</th>
                            <th class="px-6 py-4 font-extrabold whitespace-nowrap">Satuan</th>
                            <th class="px-6 py-4 font-extrabold whitespace-nowrap">Harga</th>
                            <th class="px-6 py-4 font-extrabold whitespace-nowrap">Status</th>
                            <th class="px-6 py-4 font-extrabold whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="bg-gray-200 divide-y divide-gray-500">
                        @forelse ($variants as $v)
                            <tr class="hover:bg-gray-300 transition">
                                <td class="px-6 py-4 font-semibold whitespace-nowrap">{{ $v->sku }}</td>

                                <td class="px-6 py-4 font-semibold">
                                    {{ $v->name }}
                                </td>

                                <td class="px-6 py-4 font-semibold whitespace-nowrap">
                                    {{ number_format($v->pack_size) }}
                                </td>

                                <td class="px-6 py-4 font-semibold whitespace-nowrap">
                                    {{ $v->unit }}
                                </td>

                                <td class="px-6 py-4 font-semibold whitespace-nowrap">
                                    Rp {{ number_format($v->price, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($v->status === 'aktif')
                                        <span
                                            class="inline-flex items-center rounded-full bg-green-200 px-3 py-1 text-xs font-extrabold text-green-900">
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-red-200 px-3 py-1 text-xs font-extrabold text-red-900">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                {{-- AKSI --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-4 font-semibold">
                                        @canany(['edit produk varian', 'baca produk varian'])
                                            <a href="{{ route('admin.executive-produk-variant.edit', $v->id) }}"
                                                class="text-[#2D2ACD] hover:underline">
                                                Sunting
                                            </a>
                                        @endcanany
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-6 text-center font-semibold text-gray-600">
                                    Data produk varian belum ada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            {{-- Pagination (pakai style custom kamu) --}}
            @if ($variants->hasPages())
                {{ $variants->links('vendor.pagination.pagination') }}
            @endif

        </div>

    </section>
@endsection
