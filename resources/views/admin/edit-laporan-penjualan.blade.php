@extends('admin.layout.master')

@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-laporan-penjualan', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
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

    <form action="{{ route('admin.pemasaran-laporan-penjualan.update', $sale->id) }}" method="POST"
        enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        @if ($isPaidOff)
            <div class="rounded-xl border border-green-300 bg-green-50 p-4 text-sm text-green-700">
                <div class="font-bold">Transaksi ini sudah lunas.</div>
                <div class="mt-1">Cicilan tambahan dan upload bukti pembayaran dinonaktifkan supaya nggak bikin bug aneh
                    lagi.</div>
            </div>
        @endif

        {{-- BLOK HEADER --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Laporan</label>
                    <input type="date" value="{{ $reportDate }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Penjualan</label>
                    <input type="date" value="{{ \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d') }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Penanggung Jawab</label>
                    <input type="text" value="{{ $personResponsibleName }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Jenis Penjualan</label>
                    <input type="text" value="{{ $sale->sale_type }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Gudang</label>
                    <input type="text"
                        value="{{ $sale->warehouse?->name ? $sale->warehouse->name . ' — ' . $sale->warehouse->province . ' / ' . $sale->warehouse->city : '-' }}"
                        readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Provinsi</label>
                    <input type="text" value="{{ $sale->customer_province }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Daerah</label>
                    <input type="text" value="{{ $sale->customer_city }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Alamat Lengkap</label>
                    <textarea rows="3" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">{{ $sale->customer_address }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Nama Pembeli</label>
                    <input type="text" value="{{ $sale->customer_name }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Kontak Pembeli</label>
                    <input type="text" value="{{ $sale->customer_contact }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>
            </div>
        </section>

        {{-- DAFTAR BARANG READ ONLY --}}
        <section class="space-y-4">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-sm sm:text-base font-bold text-gray-800">Daftar Barang Terjual</h2>
            </div>

            <div class="bg-[#a7dfb2] p-5 shadow border border-[#68b97a] rounded-xl overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-900">
                    <thead class="border-b border-[#68b97a]">
                        <tr>
                            <th class="px-3 py-3 font-bold whitespace-nowrap">No</th>
                            <th class="px-3 py-3 font-bold whitespace-nowrap">SKU</th>
                            <th class="px-3 py-3 font-bold whitespace-nowrap">Nama Produk</th>
                            <th class="px-3 py-3 font-bold whitespace-nowrap">Qty</th>
                            <th class="px-3 py-3 font-bold whitespace-nowrap">Harga</th>
                            <th class="px-3 py-3 font-bold whitespace-nowrap">Diskon</th>
                            <th class="px-3 py-3 font-bold whitespace-nowrap">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sale->items as $item)
                            <tr class="border-b border-[#8fcf9b] last:border-b-0">
                                <td class="px-3 py-3 font-semibold whitespace-nowrap">{{ $loop->iteration }}</td>
                                <td class="px-3 py-3 font-semibold whitespace-nowrap">
                                    {{ $item->productStock?->productVariant?->sku ?? '-' }}
                                </td>
                                <td class="px-3 py-3 font-semibold min-w-[200px]">
                                    {{ $item->productStock?->productVariant?->name ?? '-' }}
                                </td>
                                <td class="px-3 py-3 font-semibold whitespace-nowrap">{{ $item->quantity }}</td>
                                <td class="px-3 py-3 font-semibold whitespace-nowrap">
                                    Rp {{ number_format((int) $item->price, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 font-semibold whitespace-nowrap">
                                    Rp {{ number_format((int) $item->discount, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 font-bold whitespace-nowrap">
                                    Rp {{ number_format((int) $item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-4 text-center font-semibold text-gray-700">
                                    Tidak ada item penjualan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- TOTAL + PEMBAYARAN --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Total Pesanan Keseluruhan</label>
                    <input id="grandTotalDisplay" value="Rp {{ number_format((int) $sale->total_amount, 0, ',', '.') }}"
                        readonly
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

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Tambahan Pembayaran</label>
                    <input name="payment_amount" id="paymentAmount" value="{{ old('payment_amount', '') }}"
                        inputmode="numeric" placeholder="Contoh: 400000" {{ $isPaidOff ? 'disabled' : '' }}
                        class="w-full rounded-md border border-gray-400 {{ $isPaidOff ? 'bg-gray-100 cursor-not-allowed' : 'bg-white' }} px-3 py-2.5 text-sm font-semibold text-gray-900">
                    <p class="mt-2 text-xs text-gray-600">
                        Isi nominal cicilan tambahan. Harus lebih dari 0 dan tidak boleh melebihi sisa tagihan.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Sisa Tagihan Setelah Update</label>
                    <input id="remainingDebtDisplay" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>

                <div class="flex items-end">
                    <a href="{{ route('admin.pemasaran-laporan-penjualan.history-pembayaran', $sale->id) }}"
                        class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-6 py-3 text-sm font-bold text-white hover:bg-blue-800 w-full md:w-auto">
                        Lihat History Pembayaran
                    </a>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Catatan</label>
                    <textarea name="notes" rows="4" placeholder="Contoh: Sisa pembayaran akan dilunasi tanggal 10 Mei"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">{{ old('notes', $sale->notes) }}</textarea>
                </div>
            </div>
        </section>

        {{-- BUKTI PEMBAYARAN --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <label for="invoice" class="block text-sm font-bold mb-3 text-gray-800">Bukti Pembayaran</label>

            <div id="dropzone"
                class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-400 {{ $isPaidOff ? 'bg-gray-200' : 'bg-gray-100' }} px-6 py-8 text-center min-h-[220px]">
                <input id="invoice" name="invoice" type="file" accept=".png,.jpg,.jpeg,.pdf"
                    {{ $isPaidOff ? 'disabled' : '' }}
                    class="absolute inset-0 h-full w-full {{ $isPaidOff ? 'cursor-not-allowed' : 'cursor-pointer' }} opacity-0 z-10" />

                <div id="dropzoneContent" class="flex flex-col items-center gap-3 w-full pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-700" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                            d="M3 15a4 4 0 004 4h10a4 4 0 004-4m-4-4l-4-4m0 0L9 11m4-4v12" />
                    </svg>
                    <div class="text-sm text-gray-800">
                        <span class="font-bold">Click to upload</span> or drag and drop
                    </div>
                    <div class="text-xs text-gray-600">PNG, JPG, JPEG, or PDF (MAX 3 Mb)</div>
                </div>
            </div>

            @if (!$isPaidOff)
                <p class="mt-2 text-xs text-gray-600">
                    Bukti bayar wajib diupload kalau menambah cicilan.
                </p>
            @endif
        </section>

        {{-- ACTION --}}
        <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-4 pt-2">
            <a href="{{ route('admin.pemasaran-laporan-penjualan') }}"
                class="inline-flex items-center justify-center rounded-lg bg-red-600 px-10 py-3 text-sm font-bold text-white hover:bg-red-700">
                Batal
            </a>

            <button type="submit" {{ $isPaidOff ? 'disabled' : '' }}
                class="inline-flex items-center justify-center rounded-lg px-10 py-3 text-sm font-bold text-white {{ $isPaidOff ? 'bg-gray-400 cursor-not-allowed' : 'bg-[#2D2ACD] hover:bg-blue-800' }}">
                Simpan Perubahan
            </button>
        </div>
    </form>

    <script>
        const totalAmount = @json((int) $sale->total_amount);
        const currentPaidAmount = @json((int) $currentPaidAmount);
        const isPaidOff = @json($isPaidOff);

        const paymentAmount = document.getElementById('paymentAmount');
        const currentPaidDisplay = document.getElementById('currentPaidDisplay');
        const remainingDebtDisplay = document.getElementById('remainingDebtDisplay');
        const statusDisplay = document.getElementById('statusDisplay');

        const invoiceInput = document.getElementById('invoice');
        const dropzone = document.getElementById('dropzone');
        const dropzoneContent = document.getElementById('dropzoneContent');

        function parseNumber(value) {
            if (value === null || value === undefined) return 0;
            const cleaned = String(value).replace(/[^\d]/g, '');
            return cleaned ? parseInt(cleaned, 10) : 0;
        }

        function formatRupiah(num) {
            return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
        }

        function syncPaymentSummary() {
            const additional = parseNumber(paymentAmount?.value || 0);
            const simulatedPaid = currentPaidAmount + additional;
            const remainingDebtRaw = totalAmount - simulatedPaid;
            const remainingDebt = Math.max(0, remainingDebtRaw);
            const status = remainingDebt <= 0 ? 'Lunas' : 'Terhutang';

            if (currentPaidDisplay) {
                currentPaidDisplay.value = formatRupiah(currentPaidAmount);
            }

            if (remainingDebtDisplay) {
                remainingDebtDisplay.value = formatRupiah(remainingDebt);
            }

            if (statusDisplay) {
                statusDisplay.value = status;
            }
        }

        function validateInvoiceFile(file) {
            const allowed = ['image/png', 'image/jpeg', 'application/pdf'];

            if (!allowed.includes(file.type)) {
                alert('File harus PNG / JPG / JPEG / PDF');
                return false;
            }

            const maxSize = 3 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('Ukuran file maksimal 3MB');
                return false;
            }

            return true;
        }

        function getDefaultDropzoneContent() {
            return `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-700" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                        d="M3 15a4 4 0 004 4h10a4 4 0 004-4m-4-4l-4-4m0 0L9 11m4-4v12" />
                </svg>
                <div class="text-sm text-gray-800">
                    <span class="font-bold">Click to upload</span> or drag and drop
                </div>
                <div class="text-xs text-gray-600">PNG, JPG, JPEG, or PDF (MAX 3 Mb)</div>
            `;
        }

        function showDropzonePreview(file) {
            const isImage = file.type.startsWith('image/');
            const isPdf = file.type === 'application/pdf';
            const fileSizeKb = (file.size / 1024).toFixed(1);

            if (isImage) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    dropzoneContent.innerHTML = `
                        <div class="flex flex-col items-center gap-3 w-full">
                            <img src="${e.target.result}" alt="Preview invoice"
                                class="max-h-40 w-auto rounded-lg border border-gray-300 shadow-sm object-contain bg-white p-1">
                            <div class="text-sm font-bold text-gray-800 break-all">${file.name}</div>
                            <div class="text-xs text-gray-600">${fileSizeKb} KB</div>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
                return;
            }

            if (isPdf) {
                dropzoneContent.innerHTML = `
                    <div class="flex flex-col items-center gap-3 w-full">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600 font-bold text-sm border border-red-200">
                            PDF
                        </div>
                        <div class="text-sm font-bold text-gray-800 break-all">${file.name}</div>
                        <div class="text-xs text-gray-600">${fileSizeKb} KB</div>
                    </div>
                `;
            }
        }

        if (paymentAmount && !isPaidOff) {
            paymentAmount.addEventListener('input', syncPaymentSummary);
        }

        syncPaymentSummary();

        if (invoiceInput && !isPaidOff) {
            invoiceInput.addEventListener('change', function() {
                const file = this.files?.[0];

                if (!file) {
                    dropzoneContent.innerHTML = getDefaultDropzoneContent();
                    return;
                }

                if (!validateInvoiceFile(file)) {
                    this.value = '';
                    dropzoneContent.innerHTML = getDefaultDropzoneContent();
                    return;
                }

                showDropzonePreview(file);
            });
        }

        if (dropzone && !isPaidOff) {
            ['dragenter', 'dragover'].forEach(evt => {
                dropzone.addEventListener(evt, e => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('border-blue-600');
                });
            });

            ['dragleave', 'dragend'].forEach(evt => {
                dropzone.addEventListener(evt, e => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.remove('border-blue-600');
                });
            });

            dropzone.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('border-blue-600');

                const file = e.dataTransfer?.files?.[0];
                if (!file) return;

                if (!validateInvoiceFile(file)) return;

                const dt = new DataTransfer();
                dt.items.add(file);
                invoiceInput.files = dt.files;

                showDropzonePreview(file);
            });
        }
    </script>
@endsection
