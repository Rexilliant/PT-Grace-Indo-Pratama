@extends('admin.layout.master')

@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-produk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('addCss')
    <style>
        @keyframes scaleIn {
            from {
                transform: scale(0.97);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-scale-in {
            animation: scaleIn 0.2s ease-out forwards;
        }

        /* ===== Responsive Table ===== */
        @media (max-width: 767px) {
            .product-table thead {
                display: none;
            }

            .product-table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #e5e7eb;
                border-radius: 0.75rem;
                background: #fff;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                padding: 0.75rem;
            }

            .product-table tbody td {
                display: block;
                width: 100% !important;
                padding: 0.5rem 0 !important;
                border: none !important;
                text-align: left !important;
            }

            .product-table tbody td:before {
                content: attr(data-label);
                display: block;
                font-size: 0.7rem;
                font-weight: 700;
                color: #6b7280;
                text-transform: uppercase;
                margin-bottom: 0.25rem;
            }

            .product-table tbody td:last-child {
                text-align: right !important;
                padding-top: 0.75rem !important;
                border-top: 1px solid #f3f4f6 !important;
                margin-top: 0.5rem;
            }
        }
    </style>
@endsection

@section('content')

    <section class="mb-5">
        <div class="text-base sm:text-xl font-semibold text-gray-700 flex flex-wrap items-center gap-x-1 gap-y-1">
            <span>Executive</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="hover:underline">Produk</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600 font-bold">Tambah Produk</span>
        </div>
    </section>

    <form action="{{ route('admin.add-executive-produk-baru.store') }}" method="POST" class="space-y-4">
        @csrf

        <div class="bg-white p-4 sm:p-5 shadow border border-gray-300 rounded-xl space-y-4">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 pb-2 border-b">
                <h2 class="font-bold text-gray-700 text-base sm:text-lg">Daftar Produk Baru</h2>
                <button type="button" id="addRowBtn"
                    class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg font-bold text-sm transition-all flex items-center justify-center gap-2">
                    <span>+</span> Tambah Baris
                </button>
            </div>

            <div class="overflow-x-auto -mx-1 px-1">
                <table class="w-full text-left text-sm text-gray-600 product-table">
                    <thead class="bg-gray-100 text-gray-700 font-bold uppercase text-xs">
                        <tr>
                            <th class="p-3 min-w-[160px]">ID / Kode Produk <span class="text-red-500">*</span></th>
                            <th class="p-3 min-w-[180px]">Nama Produk <span class="text-red-500">*</span></th>
                            <th class="p-3 min-w-[140px]">Status <span class="text-red-500">*</span></th>
                            <th class="p-3 text-center" style="width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="productContainer" class="divide-y divide-gray-200">
                        {{-- Baris pertama default --}}
                        <tr class="product-row">
                            <td class="p-3 align-top" data-label="ID / Kode Produk *">
                                <input name="products[0][code]" type="text" placeholder="Contoh: BHOSEXT"
                                    value="{{ old('products.0.code') }}"
                                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold focus:border-blue-600 focus:ring-0 @error('products.0.code') border-red-500 @enderror">
                                @error('products.0.code')
                                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                                @enderror
                            </td>
                            <td class="p-3 align-top" data-label="Nama Produk *">
                                <input name="products[0][name]" type="text" placeholder="Contoh: BHOS Ekstra"
                                    value="{{ old('products.0.name') }}"
                                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold focus:border-blue-600 focus:ring-0 @error('products.0.name') border-red-500 @enderror">
                                @error('products.0.name')
                                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                                @enderror
                            </td>
                            <td class="p-3 align-top" data-label="Status *">
                                <select name="products[0][status]"
                                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold focus:border-blue-600 focus:ring-0 @error('products.0.status') border-red-500 @enderror">
                                    <option value="" disabled {{ old('products.0.status') ? '' : 'selected' }}>Pilih
                                        Status</option>
                                    <option value="aktif" {{ old('products.0.status') === 'aktif' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="nonaktif"
                                        {{ old('products.0.status') === 'nonaktif' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('products.0.status')
                                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                                @enderror
                            </td>
                            <td class="p-3 align-top text-center" data-label="Aksi">
                                <button type="button"
                                    class="remove-row bg-red-100 text-red-600 hover:bg-red-200 p-2 rounded-lg font-bold text-xs"
                                    title="Hapus Baris">
                                    ✕
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 pt-2">
            <button type="button" onclick="openCancelModal()"
                class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl bg-white border border-gray-300 px-10 py-3.5 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                Batal
            </button>
            <button type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl bg-[#2D2ACD] px-10 py-3.5 text-sm font-bold text-white hover:bg-blue-800 transition-all shadow-lg shadow-blue-200">
                Simpan Semua Produk
            </button>
        </div>
    </form>

    {{-- MODAL BATAL --}}
    <div id="cancelModal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/60 backdrop-blur-sm px-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md animate-scale-in p-6 text-center">
            <h3 class="text-xl font-bold text-gray-800">Batalkan Tambah Produk?</h3>
            <p class="text-gray-500 mt-2 mb-6">Data yang sudah kamu isi tidak akan disimpan.</p>
            <div class="flex gap-3">
                <button type="button" onclick="closeCancelModal()"
                    class="flex-1 px-4 py-3 rounded-xl text-sm font-bold bg-gray-100 text-gray-700 hover:bg-gray-200">Lanjut
                    Isi</button>
                <a href="{{ route('admin.executive-produk') }}"
                    class="flex-1 text-center px-4 py-3 rounded-xl text-sm font-bold bg-red-600 text-white hover:bg-red-700">Ya,
                    Batal</a>
            </div>
        </div>
    </div>

@endsection

@section('addJs')
    <script>
        let rowIndex = 1;
        const container = document.getElementById('productContainer');
        const addBtn = document.getElementById('addRowBtn');

        addBtn.addEventListener('click', function() {
            const row = document.createElement('tr');
            row.className = 'product-row';
            row.innerHTML = `
            <td class="p-3 align-top" data-label="ID / Kode Produk *">
                <input name="products[${rowIndex}][code]" type="text" placeholder="Contoh: BHOSEXT"
                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold focus:border-blue-600 focus:ring-0">
            </td>
            <td class="p-3 align-top" data-label="Nama Produk *">
                <input name="products[${rowIndex}][name]" type="text" placeholder="Contoh: BHOS Ekstra"
                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold focus:border-blue-600 focus:ring-0">
            </td>
            <td class="p-3 align-top" data-label="Status *">
                <select name="products[${rowIndex}][status]"
                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold focus:border-blue-600 focus:ring-0">
                    <option value="" disabled selected>Pilih Status</option>
                    <option value="aktif">Active</option>
                    <option value="nonaktif">Inactive</option>
                </select>
            </td>
            <td class="p-3 align-top text-center" data-label="Aksi">
                <button type="button" class="remove-row bg-red-100 text-red-600 hover:bg-red-200 p-2 rounded-lg font-bold text-xs" title="Hapus Baris">
                    ✕
                </button>
            </td>
        `;
            container.appendChild(row);
            rowIndex++;
        });

        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row') || e.target.parentElement.classList.contains(
                    'remove-row')) {
                const rows = container.querySelectorAll('.product-row');
                if (rows.length > 1) {
                    e.target.closest('tr').remove();
                } else {
                    alert('Minimal harus ada 1 produk.');
                }
            }
        });

        function openCancelModal() {
            document.getElementById('cancelModal').classList.remove('hidden');
            document.getElementById('cancelModal').classList.add('flex');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
            document.getElementById('cancelModal').classList.remove('flex');
        }
    </script>
@endsection
