@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-barang-masuk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Barang Masuk</a>
        </div>
    </section>
    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        <form action="" method="get">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 justify-between items-end">
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
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Tanggal Mulai
                    </label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="Tanggal Mulai"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">
                        Tanggal Akhir
                    </label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="Tanggal Akhir"
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

                <a href="{{ route('purchase-receipts') }}"
                    class="rounded-md bg-red-600 px-5 py-2 text-sm font-semibold text-white hover:bg-red-800 transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </section>
    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        {{-- top bar --}}
        <div class="mb-5 flex items-center gap-5">
            <a href="#"
                class="inline-flex items-center gap-2 rounded-lg bg-[#2E7E3F] px-5 py-2 text-sm font-semibold text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-300">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v6h6M20 20v-6h-6M20 8a8 0 00-14.9-3M4 16a8 0 0014.9 3" />
                </svg>
                Export .xlsx
            </a>

            @can('create-purchase-receipts')
                <a href="{{ route('create-purchase-receipt') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2D2ACD] px-6 py-2 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <span class="text-lg leading-none">+</span>
                    Tambah Baru
                </a>
            @endcan
        </div>

        {{-- table --}}
        <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-800">
                    <thead class="bg-[#5aba6f]/70 text-gray-900">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Tanggal Masuk</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Nama Penerima</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Provinsi</th>
                            {{-- ✅ tambah kolom --}}
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="bg-gray-200 divide-y divide-gray-500">
                        @forelse ($receipts as $receipt)
                            <tr class="hover:bg-gray-300">
                                <td class="px-6 py-4 font-semibold">{{ $receipt->received_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $receipt->receivedBy->name }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $receipt->province }}</td> {{-- ✅ isi provinsi --}}
                                <td class="px-6 py-3">
                                    <div class="flex items-center justify-start gap-6 font-semibold">
                                        <a href="{{ route('edit-barang-masuk', $receipt->id) }}"
                                            class="text-[#2E7E3F] hover:underline">Sunting</a>
                                        <form action="{{ route('purchase-receipts.destroy', $receipt->id) }}"
                                            method="post" class="delete-receipt-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn-delete-receipt text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- footer / pagination --}}
            <div
                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between bg-gray-200 px-3 sm:px-4 md:px-5 py-3 sm:py-4 border-t border-gray-400">

                <div class="text-xs sm:text-sm font-semibold text-gray-800">
                    Showing {{ $receipts->firstItem() ?? 0 }}–{{ $receipts->lastItem() ?? 0 }} of
                    {{ $receipts->total() }}
                </div>

                <div class="w-full sm:w-auto overflow-x-auto">
                    <div class="pagination">
                        {{ $receipts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@section('addJs')
    <script src="{{ asset('assets/js/sweetalert.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.btn-delete-receipt').forEach(function(button) {

                button.addEventListener('click', function() {

                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: "Data barang masuk akan dibatalkan dan stok akan direverse!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });

                });

            });

        });
    </script>
@endsection
