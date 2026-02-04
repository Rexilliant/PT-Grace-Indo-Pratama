@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-barang-masuk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-800">
            <span class="text-gray-800">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-800 hover:underline">Barang Masuk</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600 font-bold">Tambah Barang Masuk</span>
        </div>
    </section>

    <form action="#" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- ROW 1 --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Nama Penerima</label>
                    <input type="text" name="customer_name" value="Bambang Pratama Putra Hadi" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500 cursor-not-allowed" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Provinsi</label>
                    <input type="text" name="provinsi" value="" placeholder="Contoh: Sumatera Utara"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Total Pesanan</label>
                    <input type="text" name="total_pesanan" value="" placeholder="Contoh: Rp6.000.000"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Tanggal Barang Masuk</label>
                    <input type="date" name="tanggal_produksi" value="2025-11-30"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                </div>
            </div>
        </section>

        {{-- ROW 2 (GREEN) --}}
        {{-- <section class="bg-[#53BF6A]/55 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">ID Produk</label>
                    <input type="text" name="id_produk" value="BHOS001" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700 cursor-not-allowed focus:ring-0">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Stock Keeping Unit</label>
                    <input type="text" name="sku" value="BHOSEK1000" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700 focus:ring-0 focus:border-gray-500 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Nama Produk</label>
                    <input type="text" name="nama_produk" value="BHOS Ekstra" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700 focus:ring-0 focus:border-gray-500 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Jumlah Produksi</label>
                    <input type="text" name="jumlah_produksi" value="150 Ltr"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                </div>
            </div>
        </section> --}}

        {{-- ITEMS (REPEATABLE BLOCK) --}}
        @php
            $items = [
                ['CA001', 'Kalsium', '200 Kg', '150 Kg'],
                ['CL001', 'Klorida', '200 Kg', '150 Kg'],
                ['MG001', 'Magnesium', '200 Kg', '150 Kg'],
            ];
        @endphp

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
                        <label class="block text-sm font-bold mb-2">Jumlah Barang Masuk</label>
                        <input name="items[{{ $i }}][stok_digunakan]" value="{{ $it[3] }}"
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-blue-600 focus:ring-0">
                    </div>

                </div>
            </section>
        @endforeach

        {{-- INVOICE UPLOAD --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <label class="block text-sm font-bold mb-3 text-gray-800">Invoice Pembelian Barang</label>

            <div id="dropzone"
                class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-400 bg-gray-100 px-6 py-14 text-center">

                <input id="invoice" name="invoice" type="file" accept=".png,.jpg,.jpeg,.pdf"
                    class="absolute inset-0 h-full w-full cursor-pointer opacity-0" />

                <!-- KONTEN AWAL -->
                <div id="dropzoneContent" class="pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-700" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                            d="M3 15a4 4 0 004 4h10a4 4 0 004-4m-4-4l-4-4m0 0L9 11m4-4v12" />
                    </svg>

                    <div class="mt-3 text-sm text-gray-800">
                        <span class="font-bold">Click to upload</span> or drag and drop
                    </div>
                    <div class="text-xs text-gray-600">PNG, JPG, JPEG, or PDF (MAX 3 Mb)</div>
                </div>
            </div>

        </section>

        {{-- ACTIONS --}}
        <div class="flex items-center justify-end gap-4 pt-2">
            <button type="button" onclick="openCancelModal()"
                class="inline-flex items-center justify-center rounded-lg bg-red-600 px-10 py-3 text-sm font-bold text-white hover:bg-red-700">
                Batal
            </button>

            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                Simpan
            </button>
        </div>
    </form>

    {{-- MODAL BATAL --}}
    <div id="cancelModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 animate-scale-in">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Batalkan Pemesanan?</h3>
            </div>

            <div class="px-6 py-4 text-sm text-gray-700 leading-relaxed">
                Data yang sudah kamu isi <span class="font-semibold">belum disimpan</span>.
                Kalau dibatalkan, semua perubahan akan hilang.
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <button type="button" onclick="closeCancelModal()"
                    class="px-4 py-2 rounded-lg text-sm font-semibold bg-gray-200 hover:bg-gray-300">
                    Tetap di Halaman
                </button>

                <a href="{{ route('admin.gudang-barang-masuk') }}"
                    class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 text-white hover:bg-red-700">
                    Ya, Batalkan
                </a>
            </div>
        </div>
    </div>

    <script>
        const invoiceInput = document.getElementById('invoice');
        const dropzone = document.getElementById('dropzone');
        const dropzoneContent = document.getElementById('dropzoneContent');
        const modal = document.getElementById('cancelModal');

        function openCancelModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCancelModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function validateInvoiceFile(file) {
            const allowed = ['image/png', 'image/jpeg', 'application/pdf']; // jpg & jpeg = image/jpeg

            if (!allowed.includes(file.type)) {
                alert('File harus PNG / JPG / JPEG / PDF');
                return false;
            }

            const maxSize = 3 * 1024 * 1024; // 3MB
            if (file.size > maxSize) {
                alert('Ukuran file maksimal 3MB');
                return false;
            }

            return true;
        }

        function showFileName(file) {
            dropzoneContent.innerHTML = `
      <div class="text-sm font-bold text-gray-800 break-all">
        ${file.name}
      </div>
    `;
            dropzone.classList.add('border-blue-600');
        }

        invoiceInput.addEventListener('change', () => {
            const file = invoiceInput.files && invoiceInput.files[0] ? invoiceInput.files[0] : null;
            if (!file) return;

            if (!validateInvoiceFile(file)) {
                invoiceInput.value = ''; // reset pilihan kalau tidak valid
                return;
            }

            showFileName(file);
        });

        ['dragenter', 'dragover'].forEach(evt => {
            dropzone.addEventListener(evt, e => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('border-blue-600');
            });
        });

        dropzone.addEventListener('drop', e => {
            e.preventDefault();
            e.stopPropagation();

            const file = e.dataTransfer && e.dataTransfer.files ? e.dataTransfer.files[0] : null;
            if (!file) return;

            if (!validateInvoiceFile(file)) {
                return;
            }

            const dt = new DataTransfer();
            dt.items.add(file);
            invoiceInput.files = dt.files;

            showFileName(file);
        });
    </script>
@endsection
