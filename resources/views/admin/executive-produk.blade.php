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
        {{-- top bar: search + actions --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between mb-3">
            <div class="w-full lg:max-w-[560px]">
                <form method="GET" action="{{ route('admin.executive-produk') }}">
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        <input type="text" name="q" value="{{ $q ?? request('q') }}"
                            class="block w-full rounded-lg border border-gray-400 bg-gray-100 pl-10 pr-20 py-2.5 text-sm text-gray-900 focus:border-gray-400 focus:ring-0"
                            placeholder="Search: Code / Nama / Status / Deskripsi">

                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-2">
                            @if (!empty($q))
                                <a href="{{ route('admin.executive-produk') }}"
                                    class="rounded-md px-3 py-2 text-xs font-bold bg-gray-200 hover:bg-gray-300">
                                    Reset
                                </a>
                            @endif
                            {{-- <button type="submit"
                                class="rounded-md px-3 py-2 text-xs font-bold bg-gray-800 text-white hover:bg-black">
                                Cari
                            </button> --}}
                        </div>
                    </div>
                </form>
            </div>

            <div class="flex items-center gap-2 justify-end">
                {{-- Export (placeholder) --}}
                <a href="#"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2E7E3F] px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-300">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v6h6M20 20v-6h-6M20 8a8 0 00-14.9-3M4 16a8 0 0014.9 3" />
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
                            <th class="px-6 py-4 font-extrabold whitespace-nowrap">ID Produk (Code)</th>
                            <th class="px-6 py-4 font-extrabold whitespace-nowrap">Nama Produk</th>
                            <th class="px-6 py-4 font-extrabold whitespace-nowrap">Status</th>
                            <th class="px-6 py-4 font-extrabold whitespace-nowrap">Deskripsi</th>
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
                                <td class="px-6 py-4 font-semibold min-w-[320px]">
                                    {{ \Illuminate\Support\Str::limit($p->description, 90) }}
                                </td>


                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-start gap-6 font-semibold">
                                        <a href="{{ route('admin.edit-executive-produk', $p->id) }}"
                                            class="text-[#2D2ACD] hover:underline">Sunting</a>

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
