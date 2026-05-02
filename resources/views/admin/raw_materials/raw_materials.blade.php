@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-bahan-baku', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')

    {{-- breadcrumb --}}
    <section class="mb-5">
        {{-- breadcrumb --}}
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Bahan Baku</a>
        </div>
    </section>
    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        {{-- top bar --}}
        <form method="GET" class="mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 items-end">

                {{-- Search --}}
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Kode
                    </label>
                    <input type="text" name="code" value="{{ request('code') }}" placeholder="Code"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Nama
                    </label>
                    <input type="text" name="name" value="{{ request('name') }}" placeholder="Nama"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>

                {{-- Status --}}
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Status
                    </label>
                    <select name="status"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none">
                        <option value="">Semua Status</option>
                        @foreach ($statuses as $st)
                            <option value="{{ $st }}" @selected(request('status') == $st)>
                                {{ $st }}
                            </option>
                        @endforeach
                    </select>
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

                <a href="{{ route('procurements') }}"
                    class="rounded-md bg-red-600 px-5 py-2 text-sm font-semibold text-white hover:bg-red-800 transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </section>

    @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
            class="mb-4 rounded-lg bg-green-100 border border-green-400 px-4 py-3 text-green-800 font-semibold">
            ✅ {{ session('success') }}
        </div>
    @endif



    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        {{-- top bar --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between mb-3">
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

                @can('tambah bahan baku')
                    <a href="{{ route('admin.add-bahan-baku') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#2D2ACD] px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <span class="text-lg leading-none">+</span>
                        Tambah Baru
                    </a>
                @endcan
            </div>
        </div>

        {{-- table --}}
        <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-900">
                    <thead class="bg-[#5aba6f]/70 text-gray-900">
                        <tr class="[&>th]:border-b [&>th]:border-gray-500">
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Kode Barang</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Bahan Baku</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Aksi</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Status</th>
                        </tr>
                    </thead>

                    <tbody class="bg-gray-200 divide-y divide-gray-500">
                        @forelse ($materials as $m)
                            <tr class="hover:bg-gray-300">
                                <td class="px-6 py-4 font-semibold">{{ $m->code }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $m->name }}</td>

                                <td class="px-6 py-4 font-semibold">
                                    @canany(['edit bahan baku', 'baca bahan baku'])
                                        <a href="{{ route('admin.gudang-bahan-baku.edit', $m->id) }}"
                                            class="text-[#2E7E3F] hover:underline">
                                            Sunting
                                        </a>
                                    @endcanany
                                    @can('hapus bahan baku')
                                        |
                                        <form action="{{ route('admin.gudang-bahan-baku.destroy', ['id' => $m->id]) }}"
                                            method="POST" class="inline-block form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">
                                                Hapus
                                            </button>
                                        </form>
                                    @endcan
                                </td>

                                <td class="px-6 py-4 font-semibold">
                                    @if ($m->status == 'active')
                                        <span class="text-[#2E7E3F]">Active</span>
                                    @else
                                        <span class="text-[#EC0000]">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-6 text-center font-semibold text-gray-600">
                                    Data produk belum ada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>


                </table>
            </div>
            {{ $materials->links('vendor.pagination.pagination') }}

        </div>
    </section>
@endsection
@section('addJs')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
            });
        @endif
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
            });
        @endif
        document.querySelectorAll('.form-delete').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Anda yakin?',
                    text: 'Data yang dihapus tidak bisa dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
