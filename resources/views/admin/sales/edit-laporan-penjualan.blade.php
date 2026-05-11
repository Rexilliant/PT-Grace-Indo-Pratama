@extends('admin.layout.master')

@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-laporan-penjualan', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    {{-- BREADCRUMB --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Pemasaran</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="{{ route('admin.pemasaran-laporan-penjualan') }}" class="text-gray-700 hover:underline">
                Laporan Penjualan
            </a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600 font-bold">Sunting Laporan</span>
        </div>
    </section>

    {{-- ALERT MESSAGES --}}
    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-red-300 bg-red-50 p-4 text-sm text-red-700">
            <div class="font-bold mb-2">Ada data yang masih bermasalah:</div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-green-300 bg-green-50 p-4 text-sm text-green-700">
            <div class="font-bold">{{ session('success') }}</div>
        </div>
    @endif

    @php
        $isPaidOff = $sale->status === 'Lunas' || (int) $sale->debt_amount <= 0;
    @endphp

    {{-- FORM UTAMA: Update Pembayaran & Catatan --}}
    <form action="{{ route('admin.pemasaran-laporan-penjualan.update', $sale->id) }}" method="POST"
        enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        @if ($isPaidOff)
            <div class="rounded-xl border border-green-300 bg-green-50 p-4 text-sm text-green-700">
                <div class="font-bold">Transaksi ini sudah lunas.</div>
                <div class="mt-1">Hanya catatan yang dapat diperbarui. Cicilan tambahan dan bukti bayar dikunci otomatis.
                </div>
            </div>
        @endif

        {{-- BLOK HEADER (READ ONLY) --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Penjualan</label>
                    <input value="{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Penanggung Jawab</label>
                    <input value="{{ $personResponsibleName }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Gudang</label>
                    <input value="{{ $sale->warehouse?->name ?? '-' }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Nama Pembeli</label>
                    <input value="{{ $sale->customer_name }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>
            </div>
        </section>

        {{-- TABEL DAFTAR BARANG (READ ONLY) --}}
        <section class="bg-[#a7dfb2] p-5 shadow border border-[#68b97a] rounded-xl overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-900">
                <thead class="border-b border-[#68b97a]">
                    <tr>
                        <th class="px-3 py-3 font-bold">No</th>
                        <th class="px-3 py-3 font-bold">Nama Produk</th>
                        <th class="px-3 py-3 font-bold">Qty</th>
                        <th class="px-3 py-3 font-bold">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->items as $item)
                        <tr class="border-b border-[#8fcf9b] last:border-b-0">
                            <td class="px-3 py-3">{{ $loop->iteration }}</td>
                            <td class="px-3 py-3 font-semibold">{{ $item->productStock?->productVariant?->name ?? '-' }}
                            </td>
                            <td class="px-3 py-3">{{ $item->quantity }}</td>
                            <td class="px-3 py-3 font-bold">Rp {{ number_format((int) $item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        {{-- TOTAL + PEMBAYARAN --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Total Pesanan Keseluruhan</label>
                    <input value="Rp {{ number_format((int) $sale->total_amount, 0, ',', '.') }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Status Saat Ini</label>
                    <input id="statusDisplay" value="{{ $sale->status }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Total Sudah Dibayar</label>
                    <input id="currentPaidDisplay" value="Rp {{ number_format((int) $currentPaidAmount, 0, ',', '.') }}"
                        readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>

                @if (!$isPaidOff)
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-2.5 text-blue-700">Input Tambahan
                            Pembayaran</label>
                        <input name="payment_amount" id="paymentAmount" value="{{ old('payment_amount') }}"
                            inputmode="numeric" placeholder="Masukkan nominal cicilan"
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-2.5">Sisa Tagihan Setelah Update</label>
                        <input id="remainingDebtDisplay" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                    </div>
                @endif

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Catatan</label>
                    <textarea name="notes" rows="4" placeholder="Tambahkan catatan di sini..."
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">{{ old('notes', $sale->notes) }}</textarea>
                </div>
            </div>
        </section>

        {{-- BUKTI PEMBAYARAN CICILAN (HANYA MUNCUL JIKA BELUM LUNAS) --}}
        @if (!$isPaidOff)
            <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl mt-5">
                <label class="block text-sm font-bold mb-3 text-gray-800 text-blue-700">Upload Bukti Pembayaran</label>
                <div id="dropzone"
                    class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-400 bg-gray-100 px-6 py-8 text-center min-h-[200px] transition-colors">
                    <input id="invoice" name="invoice" type="file" accept=".png,.jpg,.jpeg,.pdf"
                        class="absolute inset-0 h-full w-full cursor-pointer opacity-0 z-10" />
                    <div id="dropzoneContent"
                        class="flex flex-col items-center gap-3 w-full pointer-events-none text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                d="M3 15a4 4 0 004 4h10a4 4 0 004-4m-4-4l-4-4m0 0L9 11m4-4v12" />
                        </svg>
                        <div class="text-sm font-bold text-gray-800">Klik atau seret bukti bayar ke sini</div>
                        <div class="text-xs">PNG, JPG, JPEG, PDF (Maks. 3MB)</div>
                    </div>
                </div>
            </section>
        @endif

        {{-- FOOTER ACTION --}}
        <div
            class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-4 pt-4 border-t border-gray-300">
            <a href="{{ route('admin.pemasaran-laporan-penjualan') }}"
                class="inline-flex items-center justify-center rounded-lg bg-red-600 px-10 py-3 text-sm font-bold text-white hover:bg-red-700 transition-colors">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg px-10 py-3 text-sm font-bold text-white bg-[#2D2ACD] hover:bg-blue-800 transition-colors">
                Simpan Perubahan
            </button>
        </div>
    </form>

    {{-- BUKTI SERAH TERIMA (HANYA MUNCUL JIKA LUNAS) --}}
    {{-- SECTION BUKTI SERAH TERIMA (BST) --}}
    @if ($isPaidOff)
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl mt-10 animate-scale-in">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                    <label class="block text-sm font-bold text-gray-800">Bukti Serah Terima Barang (BST)</label>
                    <p class="text-[10px] text-blue-600 font-medium italic">Silakan unggah dokumen yang sudah
                        ditandatangani penerima.</p>
                </div>

                @if ($sale->hasMedia('delivery_proof'))
                    <a href="{{ $sale->getFirstMediaUrl('delivery_proof') }}" target="_blank"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Lihat BST Terunggah
                    </a>
                @endif
            </div>

            <form action="{{ route('admin.pemasaran-laporan-penjualan.upload-bst', $sale->id) }}" method="POST"
                enctype="multipart/form-data" id="formBST">
                @csrf
                {{-- Desain Dropzone BST - Identik dengan Bukti Bayar --}}
                <div id="dropzoneBST"
                    class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-blue-400 bg-gray-100 px-6 py-8 text-center min-h-[200px] transition-all hover:bg-blue-50 group">

                    <input id="delivery_proof" name="delivery_proof" type="file" accept=".png,.jpg,.jpeg,.pdf"
                        class="absolute inset-0 h-full w-full cursor-pointer opacity-0 z-10"
                        onchange="handleBSTPreview(this)" />

                    <div id="bstDropzoneContent"
                        class="flex flex-col items-center gap-3 w-full pointer-events-none text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-10 w-10 text-blue-600 group-hover:scale-110 transition-transform" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div class="text-sm font-bold text-gray-800">Klik atau seret file BST ke sini</div>
                        <div class="text-xs italic">PNG, JPG, JPEG, PDF (Maks. 3MB)</div>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg px-8 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-800 transition-colors">
                        Upload & Simpan BST
                    </button>
                </div>

                @error('delivery_proof')
                    <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p>
                @enderror
            </form>
        </section>
    @endif

    <style>
        @keyframes scaleIn {
            from {
                transform: scale(.98);
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
    </style>

    <script>
        // DATA BINDING FROM BACKEND
        const totalAmount = @json((int) $sale->total_amount);
        const currentPaidAmount = @json((int) $currentPaidAmount);
        const isPaidOff = @json($isPaidOff);

        const paymentAmount = document.getElementById('paymentAmount');
        const remainingDebtDisplay = document.getElementById('remainingDebtDisplay');
        const statusDisplay = document.getElementById('statusDisplay');
        const invoiceInput = document.getElementById('invoice');
        const dropzone = document.getElementById('dropzone');
        const dropzoneContent = document.getElementById('dropzoneContent');

        // HELPER FUNCTIONS
        const parseNumber = (val) => {
            const cleaned = String(val || 0).replace(/[^\d]/g, '');
            return cleaned ? parseInt(cleaned, 10) : 0;
        };

        const formatRupiah = (num) => 'Rp ' + Number(num).toLocaleString('id-ID');

        // SINKRONISASI PEMBAYARAN
        function syncPaymentSummary() {
            if (isPaidOff || !paymentAmount) return;

            const additional = parseNumber(paymentAmount.value);
            const totalPaid = currentPaidAmount + additional;
            const remaining = Math.max(0, totalAmount - totalPaid);

            if (remainingDebtDisplay) remainingDebtDisplay.value = formatRupiah(remaining);
            if (statusDisplay) statusDisplay.value = (remaining <= 0) ? 'Lunas' : 'Terhutang';
        }

        // DROPZONE HANDLER
        if (dropzone && !isPaidOff) {
            ['dragenter', 'dragover'].forEach(e => dropzone.addEventListener(e, (evt) => {
                evt.preventDefault();
                dropzone.classList.add('border-blue-500', 'bg-blue-50');
            }));

            ['dragleave', 'dragend', 'drop'].forEach(e => dropzone.addEventListener(e, (evt) => {
                evt.preventDefault();
                dropzone.classList.remove('border-blue-500', 'bg-blue-50');
            }));

            dropzone.addEventListener('drop', (e) => {
                const file = e.dataTransfer.files[0];
                if (file && invoiceInput) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    invoiceInput.files = dataTransfer.files;
                    invoiceInput.dispatchEvent(new Event('change'));
                }
            });
        }

        // PREVIEW HANDLER
        if (invoiceInput) {
            invoiceInput.addEventListener('change', function() {
                const file = this.files[0];
                if (!file) return;

                // VALIDASI FILE
                const allowed = ['image/png', 'image/jpeg', 'application/pdf'];
                if (!allowed.includes(file.type)) {
                    alert('Format file tidak didukung!');
                    this.value = '';
                    return;
                }

                if (file.size > 3 * 1024 * 1024) {
                    alert('Ukuran file maksimal 3MB!');
                    this.value = '';
                    return;
                }

                // RENDER PREVIEW
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        dropzoneContent.innerHTML = `
                            <img src="${e.target.result}" class="max-h-32 rounded border shadow-sm">
                            <span class="text-xs font-bold mt-2">${file.name}</span>
                        `;
                    };
                    reader.readAsDataURL(file);
                } else {
                    dropzoneContent.innerHTML = `
                        <div class="bg-red-100 p-3 rounded font-bold text-red-600">PDF DOCUMENT</div>
                        <span class="text-xs mt-2">${file.name}</span>
                    `;
                }
            });
        }

        if (paymentAmount) paymentAmount.addEventListener('input', syncPaymentSummary);
        syncPaymentSummary();

        function handleBSTPreview(input) {
            const file = input.files[0];
            const content = document.getElementById('bstDropzoneContent');
            if (!file) return;

            // Validasi Sederhana
            const allowed = ['image/png', 'image/jpeg', 'application/pdf'];
            if (!allowed.includes(file.type)) {
                alert('Format file BST tidak didukung!');
                input.value = '';
                return;
            }

            // Render Preview Identik dengan Bukti Bayar
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    content.innerHTML = `
                <img src="${e.target.result}" class="max-h-40 rounded-lg border border-gray-300 shadow-sm object-contain bg-white p-1">
                <div class="text-sm font-bold text-gray-800 mt-2 break-all">${file.name}</div>
                <div class="text-xs text-gray-500">${(file.size / 1024).toFixed(1)} KB</div>
            `;
                };
                reader.readAsDataURL(file);
            } else {
                content.innerHTML = `
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600 font-bold text-sm border border-red-200">PDF</div>
            <div class="text-sm font-bold text-gray-800 mt-2 break-all">${file.name}</div>
            <div class="text-xs text-gray-500">${(file.size / 1024).toFixed(1)} KB</div>
        `;
            }
        }
    </script>
@endsection
