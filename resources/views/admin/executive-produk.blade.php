@extends('admin.layout.master')

@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-produk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    <section class="mb-5">
        {{-- breadcrumb --}}
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Executive</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Produk</a>
        </div>

        {{-- flash message --}}
        @if (session('success'))
            <div class="mb-3 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('success') }}
            </div>
        @endif
    </section>

    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        <form method="GET" action="{{ route('admin.executive-produk') }}" class="mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 items-end">

                {{-- Code --}}
                <div class="flex flex-col w-full">
                    <label class="text-xs font-semibold text-gray-700 mb-1">Kode Produk</label>
                    <input type="text" name="code" value="{{ request('code') }}" placeholder="Kode Produk"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                </div>

                {{-- Name --}}
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

                <a href="{{ route('admin.executive-produk') }}"
                    class="rounded-md bg-red-600 px-5 py-2 text-sm font-semibold text-white hover:bg-red-800 transition text-center">
                    Reset
                </a>
            </div>
        </form>
        {{-- top bar: search + actions --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between mb-3">
            <div class="flex items-center gap-2 justify-end">
                {{-- Export (placeholder) --}}
                <a href="{{ route('admin.executive-produk.export') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2E7E3F] px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-300">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v6h6M20 20v-6h-6M20 8a8 0 00-14.9-3M4 16a8 0 0014.9 3" />
                    </svg>
                    Export .xlsx
                </a>

                @can('tambah produk')
                    <a href="{{ route('admin.add-executive-produk-baru') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#2D2ACD] px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <span class="text-lg leading-none">+</span>
                        Tambah Produk
                    </a>
                @endcan
            </div>
        </div>

        {{-- table --}}
        <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-800">
                    <thead class="bg-[#5aba6f]/70 text-gray-900">
                        <tr class="[&>th]:border-b [&>th]:border-gray-500">
                            <th class="px-6 py-4 font-extrabold whitespace-nowrap">ID Produk (Code)</th>
                            <th class="px-6 py-4 font-extrabold whitespace-nowrap">Nama Produk</th>
                            <th class="px-6 py-4 font-extrabold whitespace-nowrap">Status</th>
                            <th class="px-6 py-4 font-extrabold whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="bg-gray-200 divide-y divide-gray-500">
                        @forelse ($products as $p)
                            <tr class="hover:bg-gray-300">
                                <td class="px-6 py-4 font-semibold whitespace-nowrap">{{ $p->code }}</td>
                                <td class="px-6 py-4 font-semibold min-w-[220px]">{{ $p->name }}</td>

                                <td class="px-6 py-4 font-semibold whitespace-nowrap">
                                    @if ($p->status === 'aktif')
                                        <span
                                            class="inline-flex items-center rounded-full bg-green-200 px-3 py-1 text-xs font-extrabold text-green-900">
                                            Active
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-red-200 px-3 py-1 text-xs font-extrabold text-red-900">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-start gap-6 font-semibold">
                                        @canany(['baca produk', 'edit produk'])
                                            <a href="{{ route('admin.edit-executive-produk', $p->id) }}"
                                                class="text-[#2D2ACD] hover:underline">Sunting</a>
                                        @endcanany

                                        {{-- Hapus (placeholder kalau belum ada fitur delete) --}}
                                        {{-- <button type="button"
                                            onclick="openDeleteModal('{{ route('admin.executive-produk.destroy', $p->id) }}', '{{ $p->name }}')"
                                            class="text-[#EC0000] hover:underline">
                                            Hapus
                                        </button> --}}

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-6 text-center font-semibold text-gray-600">
                                    Data produk belum ada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination (pakai style custom kamu) --}}
            @if ($products->hasPages())
                {{ $products->links('vendor.pagination.pagination') }}
            @endif


        </div>
    </section>

    {{-- MODAL HAPUS --}}
    <div id="deleteModal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm px-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md animate-scale-in">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Hapus Produk?</h3>
            </div>

            <div class="px-6 py-4 text-sm text-gray-700 leading-relaxed">
                Kamu yakin mau hapus produk:
                <span id="deleteProductName" class="font-semibold text-gray-900">-</span> ?
                <div class="mt-2 text-xs text-gray-600">
                    Produk akan dipindahkan ke status terhapus (soft delete) dan bisa dikembalikan kalau kamu buat fitur
                    restore.
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <button type="button" onclick="closeDeleteModal()"
                    class="w-full sm:w-auto px-4 py-2 rounded-lg text-sm font-semibold bg-gray-200 hover:bg-gray-300">
                    Batal
                </button>

                <form id="deleteForm" method="POST" class="w-full sm:w-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full sm:w-auto px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 text-white hover:bg-red-700">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        @keyframes scaleIn {
            from {
                transform: scale(.95);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-scale-in {
            animation: scaleIn .15s ease-out;
        }
    </style>

    <script>
        const deleteModal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const deleteProductName = document.getElementById('deleteProductName');

        function openDeleteModal(actionUrl, productName) {
            deleteForm.action = actionUrl;
            deleteProductName.textContent = productName ?? '-';

            deleteModal.classList.remove('hidden');
            deleteModal.classList.add('flex');
        }

        function closeDeleteModal() {
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
            deleteForm.action = '';
            deleteProductName.textContent = '-';
        }

        // Klik overlay untuk tutup
        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal) closeDeleteModal();
        });

        // ESC untuk tutup
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && deleteModal.classList.contains('flex')) closeDeleteModal();
        });
    </script>

@endsection
