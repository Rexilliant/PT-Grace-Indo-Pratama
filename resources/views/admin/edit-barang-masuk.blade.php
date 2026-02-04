@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-barang-masuk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    @php
        // dummy data untuk mode edit (nanti ganti dari controller: $barangMasuk, $items, dll)
        $barangMasuk = (object) [
            'id' => 12,
            'customer_name' => 'Bambang Pratama Putra Hadi',
            'tanggal_produksi' => '2025-11-30',
            // invoice lama (kalau udah ada file sebelumnya)
            'invoice_url' => asset('images/dummy-invoice.jpg'), // misal: asset('storage/invoice/inv-12.jpg')
            'invoice_name' => 'invoice-12.jpg',
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
            <a href="#" class="text-gray-800 hover:underline">Barang Masuk</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600 font-bold">Edit Barang Masuk</span>
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
                    <input type="text" name="customer_name" value="Bambang Pratama Putra Hadi" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500 cursor-not-allowed" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Provinsi</label>
                    <input type="text" name="provinsi" value="Sumatera Utara"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Total Pesanan</label>
                    <input type="text" name="total_pesanan" value="Rp6.000.000"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Tanggal Barang Masuk</label>
                    <input type="date" name="tanggal_produksi" value="2025-11-30"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                </div>
            </div>
        </section>

        {{-- ITEMS --}}
        @foreach ($items as $i => $it)
            <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-2">ID Barang</label>
                        <input name="items[{{ $i }}][id_barang]" value="{{ old("items.$i.id_barang", $it[0]) }}"
                            readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700 cursor-not-allowed focus:ring-0">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Nama Barang</label>
                        <input name="items[{{ $i }}][nama_barang]"
                            value="{{ old("items.$i.nama_barang", $it[1]) }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700 cursor-not-allowed focus:ring-0">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Stok Tersedia</label>
                        <input name="items[{{ $i }}][stok_tersedia]"
                            value="{{ old("items.$i.stok_tersedia", $it[2]) }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700 cursor-not-allowed focus:ring-0">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Jumlah Barang Masuk</label>
                        <input name="items[{{ $i }}][stok_digunakan]"
                            value="{{ old("items.$i.stok_digunakan", $it[3]) }}"
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-blue-600 focus:ring-0">
                    </div>
                </div>
            </section>
        @endforeach

        {{-- INVOICE (EDIT MODE: lihat invoice lama via MODAL + optional replace) --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                <label class="block text-sm font-bold text-gray-800">Invoice Pembelian Barang</label>

                @if ($barangMasuk->invoice_url)
                    <button type="button" onclick="openInvoiceModal()"
                        class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-4 py-2 text-xs font-bold text-white hover:bg-blue-800">
                        Lihat Invoice Lama
                    </button>
                @endif
            </div>

            @if ($barangMasuk->invoice_url)
                <div class="mb-4 rounded-lg border border-gray-300 bg-white px-4 py-3">
                    <div class="text-sm text-gray-800">
                        <div class="font-bold">Invoice saat ini</div>
                        <div class="text-xs text-gray-600">{{ $barangMasuk->invoice_name }}</div>
                    </div>
                </div>
            @endif

            <div id="dropzone"
                class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-400 bg-gray-100 px-6 py-14 text-center">
                <input id="invoice" name="invoice" type="file" accept=".png,.jpg,.jpeg"
                    class="absolute inset-0 h-full w-full cursor-pointer opacity-0" />

                <!-- KONTEN AWAL (NANTI AKAN DIGANTI JADI NAMA FILE) -->
                <div id="dropzoneContent" class="flex flex-col items-center gap-3 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-700" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                            d="M3 15a4 4 0 004 4h10a4 4 0 004-4m-4-4l-4-4m0 0L9 11m4-4v12" />
                    </svg>

                    <div class="text-sm text-gray-800">
                        <span class="font-bold">
                            {{ $barangMasuk->invoice_url ? 'Ganti invoice' : 'Click to upload' }}
                        </span>
                        {{ $barangMasuk->invoice_url ? 'atau drag and drop' : 'or drag and drop' }}
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
    <div id="cancelModal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-2 sm:p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-auto animate-scale-in overflow-hidden">
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

                <a href="{{ route('admin.gudang-barang-masuk') }}"
                    class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 text-white hover:bg-red-700">
                    Ya, Batalkan
                </a>
            </div>
        </div>
    </div>

    {{-- MODAL INVOICE LAMA (VIEW) --}}
    @if ($barangMasuk->invoice_url)
        <div id="invoiceModal"
            class="fixed inset-0 z-[10000] hidden bg-black/60 backdrop-blur-sm
                   items-center justify-center p-2 sm:p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl mx-auto overflow-hidden animate-scale-in">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                    <div class="text-sm font-bold text-gray-800">Invoice Lama</div>

                    <button type="button" onclick="closeInvoiceModal()"
                        class="rounded-lg bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700">
                        Tutup
                    </button>
                </div>

                <div class="p-4 bg-gray-100">
                    <img src="{{ $barangMasuk->invoice_url }}" alt="Invoice"
                        class="w-full max-h-[75vh] object-contain rounded-lg border border-gray-300 bg-white" />
                </div>
            </div>
        </div>
    @endif

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

        function openCancelModal() {
            cancelModal.classList.remove('hidden');
            cancelModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeCancelModal() {
            cancelModal.classList.add('hidden');
            cancelModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        // =========================
        // INVOICE UPLOAD (EDIT MODE)
        // - drag & drop works
        // - replace ALL dropzone text with uploaded file name
        // =========================
        const invoiceInput = document.getElementById('invoice');
        const dropzone = document.getElementById('dropzone');
        const dropzoneContent = document.getElementById('dropzoneContent'); // wajib ada di HTML

        function validateInvoiceFile(file) {
            // jenis file
            const allowed = ['image/png', 'image/jpeg', 'application/pdf']; // jpg & jpeg sama-sama image/jpeg
            if (!allowed.includes(file.type)) {
                alert('File harus PNG / JPG / JPEG / PDF');
                return false;
            }

            // ukuran max 3MB
            const maxSize = 3 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('Ukuran file maksimal 3MB');
                return false;
            }

            return true;
        }

        function showOnlyFileName(file) {
            if (!dropzoneContent) return; // biar tidak error kalau belum ditambah
            dropzoneContent.innerHTML = `
            <div class="text-sm font-bold text-gray-800 break-all">
                ${file.name}
            </div>
        `;
            dropzone.classList.add('border-blue-600');
        }

        function resetBorderIfEmpty() {
            if (!invoiceInput || !dropzone) return;
            if (!invoiceInput.files || !invoiceInput.files.length) {
                dropzone.classList.remove('border-blue-600');
            }
        }

        if (invoiceInput && dropzone) {
            // klik pilih file
            invoiceInput.addEventListener('change', () => {
                const file = invoiceInput.files && invoiceInput.files[0] ? invoiceInput.files[0] : null;
                if (!file) {
                    resetBorderIfEmpty();
                    return;
                }

                if (!validateInvoiceFile(file)) {
                    invoiceInput.value = ''; // kosongkan pilihan
                    resetBorderIfEmpty();
                    return;
                }

                showOnlyFileName(file);
            });

            // highlight saat drag
            ['dragenter', 'dragover'].forEach(evt => {
                dropzone.addEventListener(evt, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('border-blue-600');
                });
            });

            // keluar area dropzone
            dropzone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                e.stopPropagation();
                resetBorderIfEmpty();
            });

            // DROP file
            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const files = e.dataTransfer && e.dataTransfer.files ? e.dataTransfer.files : null;
                if (!files || !files.length) {
                    resetBorderIfEmpty();
                    return;
                }

                const file = files[0];

                if (!validateInvoiceFile(file)) {
                    resetBorderIfEmpty();
                    return;
                }

                // set file ke input (karena input.files read-only)
                const dt = new DataTransfer();
                dt.items.add(file);
                invoiceInput.files = dt.files;

                showOnlyFileName(file);
            });
        }

        // =========================
        // invoice modal (tetap)
        // =========================
        const invoiceModal = document.getElementById('invoiceModal');

        function openInvoiceModal() {
            if (!invoiceModal) return;
            invoiceModal.classList.remove('hidden');
            invoiceModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeInvoiceModal() {
            if (!invoiceModal) return;
            invoiceModal.classList.add('hidden');
            invoiceModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        // close when click backdrop
        invoiceModal?.addEventListener('click', (e) => {
            if (e.target === invoiceModal) closeInvoiceModal();
        });

        // ESC close (invoice dulu, baru cancel)
        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Escape') return;

            if (invoiceModal && !invoiceModal.classList.contains('hidden')) {
                closeInvoiceModal();
                return;
            }
            if (cancelModal && !cancelModal.classList.contains('hidden')) {
                closeCancelModal();
                return;
            }
        });
    </script>

@endsection
