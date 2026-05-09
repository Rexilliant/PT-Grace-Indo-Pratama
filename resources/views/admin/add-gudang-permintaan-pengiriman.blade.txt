@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-permintaan-pengiriman', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    @php
        // dummy status (ganti dari DB nanti)
        $status = old('status_permintaan', 'Menunggu'); // Menunggu | Ditolak | Dikirim

        // dummy data header
        $header = (object) [
            'no' => 'SRN001',
            'tgl_pengajuan' => '30/11/2025',
            'tgl_permintaan' => '01/12/2025',
            'pj' => 'Bambang Pratama Putra Hadi',
            'jenis_pengiriman' => 'Pesanan',
            'provinsi' => 'Riau',
            'armada' => 'Bus',
            'nama_penerima' => 'Thahirudin',
            'kontak' => '0812-0000-0000',
            'alamat' => '',
            'catatan' => '',
        ];

        // items: [id_produk, sku, nama_produk, jumlah]
        $items = [['BHOS001', 'SKU-001', 'BHOS Ekstra', '150 Ltr'], ['BHOS002', 'SKU-002', 'BHOS Turbo', '150 Kg']];

        // dummy bagian dikirim
        $detailKirim = (object) [
            'tgl_pengiriman' => '27/12/2025',
            'jasa' => 'Bus',
        ];
    @endphp

    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-700 hover:underline">Permintaan Pengiriman</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600 font-bold">Detail Pengiriman</span>
        </div>
    </section>

    <form action="#" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- BLOK DETAIL PERMINTAAN --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="text-xs font-bold text-gray-800 mb-4">
                Laporan Pengajuan Nomor: {{ $header->no }}
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Pengajuan</label>
                    <input type="text" value="{{ $header->tgl_pengajuan }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Permintaan Pengiriman</label>
                    <input type="text" value="{{ $header->tgl_permintaan }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Penanggung Jawab</label>
                    <input type="text" value="{{ $header->pj }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Jenis Pengiriman</label>
                    <input type="text" value="{{ $header->jenis_pengiriman }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Provinsi</label>
                    <input type="text" value="{{ $header->provinsi }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Armada Pengiriman</label>
                    <input type="text" value="{{ $header->armada }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Nama Penerima</label>
                    <input type="text" value="{{ $header->nama_penerima }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Kontak Penerima</label>
                    <input type="text" value="{{ $header->kontak }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Alamat Lengkap</label>
                    <textarea rows="3" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">{{ $header->alamat }}</textarea>
                </div>
            </div>

            {{-- ITEMS (RESPONSIVE: mobile stacked, iPad+ table grid) --}}
            <div class="mt-4">
                {{-- header grid hanya tampil iPad+ --}}
                <div class="hidden md:grid grid-cols-4 gap-4 text-xs font-bold text-gray-800 mb-2.5">
                    <div>ID Produk</div>
                    <div>Stock Keeping Unit</div>
                    <div>Nama Produk</div>
                    <div>Jumlah Pengiriman</div>
                </div>

                <div class="space-y-3">
                    @foreach ($items as $row)
                        {{-- MOBILE: card --}}
                        <div class="md:hidden rounded-xl border border-gray-300 bg-white p-4 space-y-3">
                            <div>
                                <div class="text-[11px] font-bold text-gray-600 mb-1">ID Produk</div>
                                <input value="{{ $row[0] }}" readonly
                                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                            </div>

                            <div>
                                <div class="text-[11px] font-bold text-gray-600 mb-1">Stock Keeping Unit</div>
                                <input value="{{ $row[1] }}" readonly
                                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                            </div>

                            <div>
                                <div class="text-[11px] font-bold text-gray-600 mb-1">Nama Produk</div>
                                <input value="{{ $row[2] }}" readonly
                                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                            </div>

                            <div>
                                <div class="text-[11px] font-bold text-gray-600 mb-1">Jumlah Permintaan</div>
                                <input value="{{ $row[3] }}" readonly
                                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                            </div>
                        </div>

                        {{-- iPad+ : grid row --}}
                        <div class="hidden md:grid grid-cols-4 gap-3">
                            <input value="{{ $row[0] }}" readonly
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                            <input value="{{ $row[1] }}" readonly
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                            <input value="{{ $row[2] }}" readonly
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                            <input value="{{ $row[3] }}" readonly
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Tinggalkan Pesan?</label>
                    <textarea rows="3" readonly
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">{{ $header->catatan }}</textarea>
                </div>
            </div>
        </section>

        {{-- BLOK UBAH STATUS --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Ubah Status</label>
                    <input type="text" value="27/12/2025" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Penanggung Jawab</label>
                    <input type="text" value="{{ $header->pj }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Status Permintaan</label>
                    <select id="statusPermintaan" name="status_permintaan"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                        <option value="Menunggu" {{ $status === 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="Ditolak" {{ $status === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="Dikirim" {{ $status === 'Dikirim' ? 'selected' : '' }}>Dikirim</option>
                    </select>
                </div>
            </div>
        </section>

        {{-- BLOK ALASAN DITOLAK (MUNCUL HANYA JIKA DITOLAK) --}}
        <section id="blokDitolak" class="space-y-5 {{ $status === 'Ditolak' ? '' : 'hidden' }}">
            <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
                <div class="text-xs font-bold text-gray-800 mb-4">Alasan Ditolak</div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Alasan Penolakan</label>
                    <textarea id="alasanDitolak" name="alasan_ditolak" rows="4"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500"
                        placeholder="Tulis alasan penolakan...">{{ old('alasan_ditolak') }}</textarea>
                    <div class="mt-2 text-[11px] text-gray-600">
                        Wajib diisi jika status <span class="font-bold">Ditolak</span>.
                    </div>
                </div>
            </section>
        </section>

        {{-- BLOK DETAIL PENGIRIMAN (MUNCUL HANYA JIKA DIKIRIM) --}}
        <section id="blokDikirim" class="space-y-5 {{ $status === 'Dikirim' ? '' : 'hidden' }}">
            <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
                <div class="text-xs font-bold text-gray-800 mb-4">Detail Pengiriman</div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Pengiriman</label>
                        <input type="date" name="tgl_pengiriman"
                            value="{{ old('tgl_pengiriman', $detailKirim->tgl_pengiriman) }}"
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-2.5">Jasa Pengiriman</label>
                        <select name="jasa_pengiriman"
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                            <option value="Bus"
                                {{ old('jasa_pengiriman', $detailKirim->jasa) === 'Bus' ? 'selected' : '' }}>Bus</option>
                            <option value="Truk"
                                {{ old('jasa_pengiriman', $detailKirim->jasa) === 'Truk' ? 'selected' : '' }}>Truk</option>
                            <option value="Ekspedisi"
                                {{ old('jasa_pengiriman', $detailKirim->jasa) === 'Ekspedisi' ? 'selected' : '' }}>
                                Ekspedisi</option>
                        </select>
                    </div>
                </div>

                {{-- ITEMS (RESPONSIVE: mobile stacked, iPad+ table grid) --}}
                <div class="mt-4">
                    <div class="hidden md:grid grid-cols-4 gap-4 text-xs font-bold text-gray-800 mb-2.5">
                        <div>ID Produk</div>
                        <div>Stock Keeping Unit</div>
                        <div>Nama Produk</div>
                        <div>Jumlah Permintaan</div>
                    </div>

                    <div class="space-y-3">
                        @foreach ($items as $row)
                            {{-- MOBILE: card --}}
                            <div class="md:hidden rounded-xl border border-gray-300 bg-white p-4 space-y-3">
                                <div>
                                    <div class="text-[11px] font-bold text-gray-600 mb-1">ID Produk</div>
                                    <input value="{{ $row[0] }}" readonly
                                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                                </div>

                                <div>
                                    <div class="text-[11px] font-bold text-gray-600 mb-1">Stock Keeping Unit</div>
                                    <input value="{{ $row[1] }}" readonly
                                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                                </div>

                                <div>
                                    <div class="text-[11px] font-bold text-gray-600 mb-1">Nama Produk</div>
                                    <input value="{{ $row[2] }}" readonly
                                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                                </div>

                                <div>
                                    <div class="text-[11px] font-bold text-gray-600 mb-1">Jumlah Pengiriman</div>
                                    <input value="{{ $row[3] }}"
                                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0">
                                </div>
                            </div>

                            {{-- iPad+ : grid row --}}
                            <div class="hidden md:grid grid-cols-4 gap-3">
                                <input value="{{ $row[0] }}" readonly
                                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                                <input value="{{ $row[1] }}" readonly
                                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                                <input value="{{ $row[2] }}" readonly
                                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                                <input value="{{ $row[3] }}"
                                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0">
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- INVOICE PENGIRIMAN (MUNCUL HANYA JIKA DIKIRIM) --}}
            <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
                <label class="block text-sm font-bold mb-3 text-gray-800">Invoice Pengiriman</label>

                <div id="dropzone"
                    class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-400 bg-gray-100 px-6 py-14 text-center">
                    <input id="invoice" name="invoice_pengiriman" type="file" accept=".png,.jpg,.jpeg,.pdf"
                        class="absolute inset-0 h-full w-full cursor-pointer opacity-0" />

                    {{-- KONTEN DROPZONE (AKAN DIGANTI NAMA FILE SAAT ADA FILE) --}}
                    <div id="dropzoneContent" class="flex flex-col items-center gap-3 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-700" fill="none"
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
        </section>

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

                <a href="{{ route('admin.gudang-permintaan-pengiriman') }}"
                    class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 text-white hover:bg-red-700">
                    Ya, Batalkan
                </a>
            </div>
        </div>
    </div>

    <script>
        const statusSelect = document.getElementById('statusPermintaan');
        const blokDikirim = document.getElementById('blokDikirim');
        const blokDitolak = document.getElementById('blokDitolak');
        const alasanDitolak = document.getElementById('alasanDitolak');
        const modal = document.getElementById('cancelModal');

        function openCancelModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCancelModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function syncBlocks() {
            const val = statusSelect.value;

            // tampil/sembunyi blok dikirim
            if (val === 'Dikirim') {
                blokDikirim.classList.remove('hidden');
            } else {
                blokDikirim.classList.add('hidden');
            }

            // tampil/sembunyi blok ditolak
            if (val === 'Ditolak') {
                blokDitolak.classList.remove('hidden');
                if (alasanDitolak) alasanDitolak.setAttribute('required', 'required');
            } else {
                blokDitolak.classList.add('hidden');
                if (alasanDitolak) alasanDitolak.removeAttribute('required');
            }
        }

        statusSelect.addEventListener('change', syncBlocks);
        syncBlocks();

        // =========================
        // DROPZONE INVOICE (SHOW NAME + VALIDASI + DROP SET INPUT)
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
            if (!dropzoneContent) return;

            dropzoneContent.innerHTML = `
                <div class="text-sm font-bold text-gray-800 break-all">
                    ${file.name}
                </div>
            `;
            dropzone.classList.add('border-blue-600');
        }

        function resetDropzoneBorderIfEmpty() {
            if (!invoiceInput || !dropzone) return;
            if (!invoiceInput.files || !invoiceInput.files.length) {
                dropzone.classList.remove('border-blue-600');
            }
        }

        if (invoiceInput && dropzone) {
            invoiceInput.addEventListener('change', () => {
                const file = invoiceInput.files && invoiceInput.files[0] ? invoiceInput.files[0] : null;
                if (!file) {
                    resetDropzoneBorderIfEmpty();
                    return;
                }

                if (!validateInvoiceFile(file)) {
                    invoiceInput.value = '';
                    resetDropzoneBorderIfEmpty();
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
                resetDropzoneBorderIfEmpty();
            });

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const files = e.dataTransfer && e.dataTransfer.files ? e.dataTransfer.files : null;
                if (!files || !files.length) {
                    resetDropzoneBorderIfEmpty();
                    return;
                }

                const file = files[0];

                if (!validateInvoiceFile(file)) {
                    resetDropzoneBorderIfEmpty();
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
