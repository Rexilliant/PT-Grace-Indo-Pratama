@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-laporan-penjualan', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    @php
        // =========================
        // DUMMY DATA (ganti DB nanti)
        // =========================

        // tanggal laporan = hari ini (readonly)
        $tglLaporan = now()->format('Y-m-d');

        // dummy penanggung jawab dari user login (nanti ganti auth()->user()->name)
        $pjLogin = 'Bambang Pratama Putra Hadi';

        // status default
        $status = old('status', 'Terhutang'); // Terhutang | Lunas

        $header = (object) [
            'tgl_penjualan' => old('tgl_penjualan', '2025-12-01'),
            'jenis_penjualan' => old('jenis_penjualan', 'Perseorangan'),
            'provinsi' => old('provinsi', 'Medan'),
            'daerah' => old('daerah', 'Pasir Putih'),
            'nama_pembeli' => old('nama_pembeli', ''),
            'kontak_pembeli' => old('kontak_pembeli', ''),
            'catatan' => old('catatan', ''),
        ];

        // items: jumlah_terjual & diskon boleh edit, lainnya readonly
        $items = [
            [
                'id_produk' => 'BHOS001',
                'sku' => 'SKU-001',
                'nama_produk' => 'BHOS Ekstra',
                'jumlah_terjual' => old('items.0.jumlah_terjual', '150 Ltr'),
                'harga' => 500000,
                'diskon' => old('items.0.diskon', 50000),
            ],
            [
                'id_produk' => 'BHOS002',
                'sku' => 'SKU-002',
                'nama_produk' => 'BHOS Turbo',
                'jumlah_terjual' => old('items.1.jumlah_terjual', '150 Kg'),
                'harga' => 500000,
                'diskon' => old('items.1.diskon', 50000),
            ],
        ];

        // helper rupiah
        $rp = function ($n) {
            return 'Rp ' . number_format((int) $n, 0, ',', '.');
        };

        // hitung total per item (dummy sederhana: total = harga - diskon)
        // (kalau mau berdasarkan qty numeric, nanti ubah logikanya di backend)
        $totalItems = [];
        $grandTotal = 0;
        foreach ($items as $idx => $it) {
            $t = max(0, (int) $it['harga'] - (int) $it['diskon']);
            $totalItems[$idx] = $t;
            $grandTotal += $t;
        }

        $jumlahTerhutang = old('jumlah_terhutang', 900000);
    @endphp

    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Pemasaran</span>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-gray-700">Laporan Penjualan</span>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600 font-bold">Tambah Laporan</span>
        </div>
    </section>

    <form action="#" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- BLOK HEADER --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Laporan</label>
                    <input type="date" name="tgl_laporan" value="{{ old('tgl_laporan', $tglLaporan) }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Penjualan</label>
                    <input type="date" name="tgl_penjualan" value=""
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Penanggung Jawab</label>
                    <input type="text" name="pj" value="{{ old('pj', $pjLogin) }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Jenis Penjualan</label>
                    <select name="jenis_penjualan"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                        @foreach (['Perseorangan', 'Instansi', 'Pesanan'] as $j)
                            <option value="{{ $j }}"
                                {{ old('jenis_penjualan', $header->jenis_penjualan) === $j ? 'selected' : '' }}>
                                {{ $j }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Provinsi</label>
                    <input type="text" name="provinsi" value="" placeholder="Masukkan provinsi"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5
               text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                </div>


                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Daerah</label>
                    <input type="text" name="daerah" value=""
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Nama Pembeli</label>
                    <input type="text" name="nama_pembeli" value="{{ old('nama_pembeli', $header->nama_pembeli) }}"
                        placeholder="Masukkan nama pembeli"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Kontak Pembeli</label>
                    <input type="text" name="kontak_pembeli" value="{{ old('kontak_pembeli', $header->kontak_pembeli) }}"
                        placeholder="Masukkan kontak pembeli"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                </div>
            </div>
        </section>

        {{-- LIST ITEM (kartu hijau) --}}
        <section class="space-y-4">
            @foreach ($items as $i => $row)
                <section class="bg-[#a7dfb2] p-5 shadow border border-[#68b97a] rounded-xl">
                    {{-- RESPONSIVE: mobile 1 kolom, iPad/tablet 2 kolom, desktop 4 kolom --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-2.5">ID Produk</label>
                            <input name="items[{{ $i }}][id_produk]" value="{{ $row['id_produk'] }}" readonly
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-2.5">Stock Keeping Unit</label>
                            <input name="items[{{ $i }}][sku]" value="{{ $row['sku'] }}" readonly
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-2.5">Nama Produk</label>
                            <input name="items[{{ $i }}][nama_produk]" value="{{ $row['nama_produk'] }}"
                                readonly
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-2.5">Jumlah Terjual</label>
                            <input name="items[{{ $i }}][jumlah_terjual]" value="{{ $row['jumlah_terjual'] }}"
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-2.5">Harga</label>
                            <input value="{{ $rp($row['harga']) }}" readonly
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-2.5">Diskon</label>
                            <input name="items[{{ $i }}][diskon]" value="{{ $row['diskon'] }}"
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-2.5">Total</label>
                            <input id="total_item_{{ $i }}" value="{{ $rp($totalItems[$i]) }}" readonly
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                        </div>
                    </div>
                </section>
            @endforeach
        </section>

        {{-- BLOK TOTAL + STATUS --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Total Pesanan Keseluruhan</label>
                    <input id="grand_total" value="{{ $rp($grandTotal) }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Status</label>
                    <select id="statusSelect" name="status"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                        <option value="Terhutang" {{ old('status', $status) === 'Terhutang' ? 'selected' : '' }}>Terhutang
                        </option>
                        <option value="Lunas" {{ old('status', $status) === 'Lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>

                {{-- MUNCUL HANYA JIKA TERHUTANG --}}
                <div id="blokTerhutang" class="{{ old('status', $status) === 'Terhutang' ? '' : 'hidden' }}">
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Jumlah Terhutang</label>
                    <input name="jumlah_terhutang" id="jumlahTerhutang" value="{{ $jumlahTerhutang }}"
                        placeholder="Masukkan jumlah terhutang"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Catatan</label>
                    <textarea name="catatan" rows="4"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">{{ old('catatan', $header->catatan) }}</textarea>
                </div>
            </div>
        </section>

        {{-- INVOICE UPLOAD --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <label class="block text-sm font-bold mb-3 text-gray-800">Invoice Pembayaran</label>

            <div id="dropzone"
                class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-400 bg-gray-100 px-6 py-14 text-center">
                <input id="invoice" name="invoice" type="file" accept=".png,.jpg,.jpeg,.pdf"
                    class="absolute inset-0 h-full w-full cursor-pointer opacity-0" />

                <div id="dropzoneContent" class="flex flex-col items-center gap-3 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-700" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                            d="M3 15a4 4 0 004 4h10a4 4 0 004-4m-4-4l-4-4m0 0L9 11m4-4v12" />
                    </svg>

                    <div class="text-sm text-gray-800">
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
    <div id="cancelModal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-2 sm:p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-auto animate-scale-in overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Batalkan?</h3>
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

                <a href="{{ route('admin.pemasaran-laporan-penjualan') }}"
                    class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 text-white hover:bg-red-700">
                    Ya, Batalkan
                </a>
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
        // =========================
        // MODAL BATAL
        // =========================
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

        cancelModal?.addEventListener('click', (e) => {
            if (e.target === cancelModal) closeCancelModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && cancelModal && !cancelModal.classList.contains('hidden')) {
                closeCancelModal();
            }
        });

        // =========================
        // STATUS: TERHUTANG / LUNAS
        // =========================
        const statusSelect = document.getElementById('statusSelect');
        const blokTerhutang = document.getElementById('blokTerhutang');
        const jumlahTerhutang = document.getElementById('jumlahTerhutang');

        function syncTerhutang() {
            const val = statusSelect.value;
            if (val === 'Terhutang') {
                blokTerhutang.classList.remove('hidden');
            } else {
                blokTerhutang.classList.add('hidden');
                if (jumlahTerhutang) jumlahTerhutang.value = '';
            }
        }

        statusSelect?.addEventListener('change', syncTerhutang);
        syncTerhutang();

        // =========================
        // DROPZONE INVOICE (VALIDASI + SHOW NAME)
        // =========================
        const invoiceInput = document.getElementById('invoice');
        const dropzone = document.getElementById('dropzone');
        const dropzoneContent = document.getElementById('dropzoneContent');

        function validateInvoiceFile(file) {
            const allowed = ['image/png', 'image/jpeg', 'application/pdf'];
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

        function showDropzoneFileName(file) {
            dropzoneContent.innerHTML = `
                <div class="text-sm font-bold text-gray-800 break-all">
                    ${file.name}
                </div>
            `;
            dropzone.classList.add('border-blue-600');
        }

        function resetDropzoneIfEmpty() {
            if (!invoiceInput?.files?.length) {
                dropzone.classList.remove('border-blue-600');
                dropzoneContent.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-700" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                            d="M3 15a4 4 0 004 4h10a4 4 0 004-4m-4-4l-4-4m0 0L9 11m4-4v12" />
                    </svg>
                    <div class="text-sm text-gray-800">
                        <span class="font-bold">Click to upload</span> or drag and drop
                    </div>
                    <div class="text-xs text-gray-600">PNG, JPG, JPEG, or PDF (MAX 3 Mb)</div>
                `;
            }
        }

        if (invoiceInput && dropzone && dropzoneContent) {
            invoiceInput.addEventListener('change', () => {
                const file = invoiceInput.files && invoiceInput.files[0] ? invoiceInput.files[0] : null;
                if (!file) {
                    resetDropzoneIfEmpty();
                    return;
                }
                if (!validateInvoiceFile(file)) {
                    invoiceInput.value = '';
                    resetDropzoneIfEmpty();
                    return;
                }
                showDropzoneFileName(file);
            });

            ['dragenter', 'dragover'].forEach(evt => {
                dropzone.addEventListener(evt, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('border-blue-600');
                });
            });

            dropzone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                e.stopPropagation();
                resetDropzoneIfEmpty();
            });

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const files = e.dataTransfer && e.dataTransfer.files ? e.dataTransfer.files : null;
                if (!files || !files.length) {
                    resetDropzoneIfEmpty();
                    return;
                }

                const file = files[0];
                if (!validateInvoiceFile(file)) {
                    resetDropzoneIfEmpty();
                    return;
                }

                const dt = new DataTransfer();
                dt.items.add(file);
                invoiceInput.files = dt.files;

                showDropzoneFileName(file);
            });
        }
    </script>
@endsection
