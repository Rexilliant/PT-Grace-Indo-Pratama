@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-stok-bahan-baku', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')

    {{-- breadcrumb --}}
    <section class="mb-5">
        {{-- breadcrumb --}}
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Stok Bahan Baku</a>
        </div>
    </section>

    @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
            class="mb-4 rounded-lg bg-green-100 border border-green-400 px-4 py-3 text-green-800 font-semibold">
            ✅ {{ session('success') }}
        </div>
    @endif

    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        <form action="" method="get">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 justify-between items-end">
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Kode Barang
                    </label>
                    <input type="text" name="code" value="{{ request('code') }}" placeholder="Kode Barang"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Nama
                    </label>
                    <input type="text" name="name" value="{{ request('name') }}" placeholder="Nama"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Provinsi
                    </label>
                    <input type="text" name="province" value="{{ request('province') }}" placeholder="Provinsi"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>
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

                <a href="{{ route('admin.gudang-stok-bahan-baku') }}"
                    class="rounded-md bg-red-600 px-5 py-2 text-sm font-semibold text-white hover:bg-red-800 transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        {{-- table --}}
        <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-900">
                    <thead class="bg-[#5aba6f]/70 text-gray-900">
                        <tr class="[&>th]:border-b [&>th]:border-gray-500">
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Kode Barang</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Bahan Baku</th>

                            {{-- ✅ Kolom Baru Provinsi --}}
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Provinsi</th>

                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Jumlah Stok</th>
                        </tr>
                    </thead>

                    <tbody class="bg-gray-200 divide-y divide-gray-500">
                        @forelse ($stocks as $s)
                            <tr class="hover:bg-gray-300">
                                <td class="px-6 py-4 font-semibold">{{ $s->rawMaterial->code }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $s->rawMaterial->name }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $s->province }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $s->stock }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-6 text-center font-semibold text-gray-600">
                                    Data stok belum ada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>


                </table>
            </div>

            {{-- footer / pagination (mobile + ipad aman) --}}
            {{ $stocks->links('vendor.pagination.pagination') }}



        </div>
    </section>
@endsection
