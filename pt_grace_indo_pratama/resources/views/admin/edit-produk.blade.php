@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-laporan-produksi', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    @php
        // DUMMY DATA EDIT (nanti ganti dari DB)
        $produksi = (object) [
            'nama_penerima' => 'Bambang Pratama Putra Hadi',
            'tanggal_produksi' => '2025-11-30',
            'id_produk' => 'BHOS001',
            'sku' => 'BHOSEK1000',
            'nama_produk' => 'BHOS Ekstra',
            'jumlah_produksi' => '150 Ltr',
            'invoice_url' => asset('images/dummy-invoice.jpg'), // kalau sudah ada invoice lama
        ];

        $items = [
            ['CA001', 'Kalsium', '200 Kg', '150 Kg'],
            ['CL001', 'Klorida', '200 Kg', '150 Kg'],
            ['MG001', 'Magnesium', '200 Kg', '150 Kg'],
        ];
    @endphp

    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-800">
            <span class="text-gray-800">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-800 hover:underline">Produksi</a>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-800 hover:underline">Pilih Produk</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600 font-bold">Edit Produk</span>
        </div>
    </section>

    <form action="#" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- ROW 1 --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Nama Penerima</label>
                    <input type="text" name="customer_name" value="{{ $produksi->nama_penerima }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500 cursor-not-allowed" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Tanggal Produksi</label>
                    <input type="date" name="tanggal_produksi"
                        value="{{ old('tanggal_produksi', $produksi->tanggal_produksi) }}"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                </div>
            </div>
        </section>

        {{-- ROW 2 (GREEN) --}}
        <section class="bg-[#53BF6A]/55 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">ID Produk</label>
                    <input type="text" name="id_produk" value="{{ $produksi->id_produk }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700 cursor-not-allowed focus:ring-0">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Stock Keeping Unit</label>
                    <input type="text" name="sku" value="{{ $produksi->sku }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700 focus:ring-0 focus:border-gray-500 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Nama Produk</label>
                    <input type="text" name="nama_produk" value="{{ $produksi->nama_produk }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700 focus:ring-0 focus:border-gray-500 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Jumlah Produksi</label>
                    <input type="text" name="jumlah_produksi"
                        value="{{ old('jumlah_produksi', $produksi->jumlah_produksi) }}"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                </div>
            </div>
        </section>

        {{-- ITEMS (READ ONLY) --}}
        @foreach ($items as $i => $it)
            <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-2">ID Barang</label>
                        <input name="items[{{ $i }}][id_barang]" value="{{ $it[0] }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700 cursor-not-allowed focus:ring-0">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Nama Barang</label>
                        <input name="items[{{ $i }}][nama_barang]" value="{{ $it[1] }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700 cursor-not-allowed focus:ring-0">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Stok Tersedia</label>
                        <input name="items[{{ $i }}][stok_tersedia]" value="{{ $it[2] }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700 cursor-not-allowed focus:ring-0">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Jumlah Stok Digunakan</label>
                        <input name="items[{{ $i }}][stok_digunakan]" value="{{ $it[3] }}"
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-700 focus:ring-0">
                    </div>
                </div>
            </section>
        @endforeach

        {{-- INVOICE (EDITABLE) --}}
        {{-- <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                <label class="block text-sm font-bold text-gray-800">Invoice Pembelian Barang</label>

                @if (!empty($produksi->invoice_url))
                    <button type="button" onclick="openInvoiceModal()"
                        class="inline-flex items-center justify-center rounded-lg bg-gray-700 px-4 py-2 text-xs font-bold text-white hover:bg-gray-800">
                        Lihat Invoice Lama
                    </button>
                @endif
            </div>

            <div id="dropzone"
                class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-400 bg-gray-100 px-6 py-14 text-center">
                <input id="invoice" name="invoice" type="file" accept=".png,.jpg,.jpeg"
                    class="absolute inset-0 h-full w-full cursor-pointer opacity-0" />

                <div class="flex flex-col items-center gap-3 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-700" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                            d="M3 15a4 4 0 004 4h10a4 4 0 004-4m-4-4l-4-4m0 0L9 11m4-4v12" />
                    </svg>

                    <div class="text-sm text-gray-800">
                        <span class="font-bold">Click to upload</span> or drag and drop
                    </div>
                    <div class="text-xs text-gray-600">PNG, JPG, or JPEG (MAX 3 Mb)</div>

                    <div id="fileName" class="mt-2 hidden text-xs font-semibold text-gray-800"></div>
                </div>
            </div>
        </section> --}}

        {{-- ACTIONS --}}
        <div class="flex items-center justify-end gap-4 pt-2">
            <button type="button" onclick="openCancelModal()"
                class="inline-flex items-center justify-center rounded-lg bg-red-600 px-10 py-3 text-sm font-bold text-white hover:bg-red-700">
                Batal
            </button>

            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                Simpan Perubahan
            </button>
        </div>
    </form>

    {{-- MODAL BATAL --}}
    <div id="cancelModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 animate-scale-in">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Batalkan Perubahan?</h3>
            </div>

            <div class="px-6 py-4 text-sm text-gray-700 leading-relaxed">
                Perubahan yang kamu buat <span class="font-semibold">belum disimpan</span>.
                Kalau dibatalkan, semua perubahan akan hilang.
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <button type="button" onclick="closeCancelModal()"
                    class="px-4 py-2 rounded-lg text-sm font-semibold bg-gray-200 hover:bg-gray-300">
                    Tetap di Halaman
                </button>

                <a href="{{ route('admin.gudang-laporan-produksi') }}"
                    class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 text-white hover:bg-red-700">
                    Ya, Batalkan
                </a>
            </div>
        </div>
    </div>

    {{-- MODAL INVOICE LAMA --}}
    <div id="invoiceModal"
        class="fixed inset-0 z-[10000] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-2 sm:p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl mx-auto overflow-hidden animate-scale-in">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                <div class="text-sm font-bold text-gray-800">Invoice Lama</div>
                <button type="button" onclick="closeInvoiceModal()"
                    class="rounded-lg bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700">
                    Tutup
                </button>
            </div>

            <div class="p-4 bg-gray-100">
                <img src="{{ $produksi->invoice_url }}" alt="Invoice"
                    class="w-full max-h-[70vh] object-contain rounded-lg border border-gray-300 bg-white" />
            </div>
        </div>
    </div>

    <style>
        @keyframes scaleIn {
            from {
                transform: scale(.98);
                opacity: .6;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-scale-in {
            animation: scaleIn .12s ease-out;
        }
    </style>

    <script>
        const cancelModal = document.getElementById('cancelModal');
        const invoiceModal = document.getElementById('invoiceModal');

        function openCancelModal() {
            cancelModal.classList.remove('hidden');
            cancelModal.classList.add('flex');
        }

        function closeCancelModal() {
            cancelModal.classList.add('hidden');
            cancelModal.classList.remove('flex');
        }

        function openInvoiceModal() {
            if (!invoiceModal) return;
            invoiceModal.classList.remove('hidden');
            invoiceModal.classList.add('flex');
        }

        function closeInvoiceModal() {
            if (!invoiceModal) return;
            invoiceModal.classList.add('hidden');
            invoiceModal.classList.remove('flex');
        }

        // const invoiceInput = document.getElementById('invoice');
        // const fileName = document.getElementById('fileName');
        // const dropzone = document.getElementById('dropzone');

        // if (invoiceInput) {
        //     invoiceInput.addEventListener('change', () => {
        //         if (invoiceInput.files && invoiceInput.files[0]) {
        //             fileName.textContent = invoiceInput.files[0].name;
        //             fileName.classList.remove('hidden');
        //             dropzone.classList.add('border-blue-600');
        //         } else {
        //             fileName.classList.add('hidden');
        //             dropzone.classList.remove('border-blue-600');
        //         }
        //     });

        //     ['dragenter', 'dragover'].forEach(evt => {
        //         dropzone.addEventListener(evt, (e) => {
        //             e.preventDefault();
        //             e.stopPropagation();
        //             dropzone.classList.add('border-blue-600');
        //         });
        //     });

        //     ['dragleave', 'drop'].forEach(evt => {
        //         dropzone.addEventListener(evt, (e) => {
        //             e.preventDefault();
        //             e.stopPropagation();
        //             if (!invoiceInput.files.length) dropzone.classList.remove('border-blue-600');
        //         });
        //     });
        // }

        // document.addEventListener('keydown', (e) => {
        //     if (e.key === 'Escape') {
        //         if (invoiceModal && !invoiceModal.classList.contains('hidden')) closeInvoiceModal();
        //         else if (cancelModal && !cancelModal.classList.contains('hidden')) closeCancelModal();
        //     }
        // });

        // invoiceModal?.addEventListener('click', (e) => {
        //     if (e.target === invoiceModal) closeInvoiceModal();
        // });
    </script>
@endsection
