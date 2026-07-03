@extends('admin.layout.master')

@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-laporan-penjualan', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('addCss')
    {{-- CSS Wajib FilePond --}}
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">

    <style>
        /* Styling FilePond */
        .filepond--root {
            font-family: inherit;
            margin-bottom: 0;
            min-height: 250px !important;
        }

        .filepond--drop-label {
            background-color: transparent !important;
            cursor: pointer;
            min-height: 250px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 1.5rem !important;
        }

        .filepond--drop-label>div {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 !important;
            padding: 0 !important;
        }

        .filepond--panel-root {
            background-color: #ffffff !important;
            border: 2px dashed #d1d5db !important;
            border-radius: 1rem !important;
            transition: all 0.3s ease;
        }

        .filepond--root:hover .filepond--panel-root {
            border-color: #3b82f6 !important;
            background-color: #eff6ff !important;
        }

        .filepond--label-action {
            text-decoration: none;
            cursor: pointer;
            color: #3b82f6;
            font-weight: 700;
        }
    </style>
@endsection

@section('content')
    {{-- BREADCRUMB --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Pemasaran</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="{{ route('admin.pemasaran-laporan-penjualan') }}" class="text-gray-700 hover:underline">Laporan
                Penjualan</a>
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

    @php $isPaidOff = $sale->status === 'Lunas' || (int) $sale->debt_amount <= 0; @endphp

    <form action="{{ route('admin.pemasaran-laporan-penjualan.update', $sale->id) }}" method="POST"
        enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

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

        {{-- TABEL DAFTAR BARANG --}}
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
                    <textarea name="notes" rows="4"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">{{ old('notes', $sale->notes) }}</textarea>
                </div>
            </div>
        </section>

        {{-- BUKTI PEMBAYARAN CICILAN --}}
        @if (!$isPaidOff)
            <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl mt-5">
                <label class="block text-sm font-bold mb-3 text-gray-800 text-blue-700">Upload Bukti Pembayaran</label>
                <input type="file" name="invoice" id="invoicePond"
                    accept="image/png, image/jpeg, image/jpg, application/pdf">
                @error('invoice')
                    <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                @enderror
            </section>
        @endif

        {{-- FOOTER ACTION --}}
        <div
            class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-4 pt-4 border-t border-gray-300">
            <a href="{{ route('admin.pemasaran-laporan-penjualan') }}"
                class="inline-flex items-center justify-center rounded-lg bg-red-600 px-10 py-3 text-sm font-bold text-white hover:bg-red-700 transition-colors">Batal</a>
            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg px-10 py-3 text-sm font-bold text-white bg-[#2D2ACD] hover:bg-blue-800 transition-colors">Simpan
                Perubahan</button>
        </div>
    </form>

    {{-- BUKTI SERAH TERIMA (HANYA MUNCUL JIKA LUNAS) --}}
    @if ($isPaidOff)
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl mt-10 animate-scale-in">
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-800">Bukti Serah Terima Barang (BST)</label>
                <p class="text-[10px] text-blue-600 font-medium italic">Unggah dokumen yang sudah ditandatangani.</p>
            </div>

            <form action="{{ route('admin.pemasaran-laporan-penjualan.upload-bst', $sale->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                {{-- Input FilePond untuk BST --}}
                <input type="file" name="delivery_proof" id="bstPond"
                    accept="image/png, image/jpeg, image/jpg, application/pdf">

                <div class="mt-4 flex justify-end">
                    <button type="submit"
                        class="rounded-lg px-8 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-800 transition-colors">Upload
                        & Simpan BST</button>
                </div>
                @error('delivery_proof')
                    <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p>
                @enderror
            </form>
        </section>
    @endif
@endsection

@section('addJs')
    {{-- Library FilePond --}}
    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // DATA BINDING
        const totalAmount = @json((int) $sale->total_amount);
        const currentPaidAmount = @json((int) $currentPaidAmount);
        const isPaidOff = @json($isPaidOff);

        const paymentAmount = document.getElementById('paymentAmount');
        const remainingDebtDisplay = document.getElementById('remainingDebtDisplay');
        const statusDisplay = document.getElementById('statusDisplay');

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

        // PREVIEW HANDLER BST
        function handleBSTPreview(input) {
            const file = input.files[0];
            const content = document.getElementById('bstDropzoneContent');
            if (!file) return;
            const allowed = ['image/png', 'image/jpeg', 'application/pdf'];
            if (!allowed.includes(file.type)) {
                alert('Format file BST tidak didukung!');
                input.value = '';
                return;
            }
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    content.innerHTML =
                        `<img src="${e.target.result}" class="max-h-40 rounded-lg border border-gray-300 shadow-sm object-contain bg-white p-1"><div class="text-sm font-bold text-gray-800 mt-2">${file.name}</div>`;
                };
                reader.readAsDataURL(file);
            } else {
                content.innerHTML =
                    `<div class="flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600 font-bold text-sm border border-red-200">PDF</div><div class="text-sm font-bold text-gray-800 mt-2">${file.name}</div>`;
            }
        }

        // INIT APP
        document.addEventListener('DOMContentLoaded', function() {
            // FilePond Init
            FilePond.registerPlugin(FilePondPluginFileValidateType, FilePondPluginFileValidateSize,
                FilePondPluginImagePreview);
            const inv = document.querySelector('#invoicePond');
            if (inv) {
                FilePond.create(inv, {
                    storeAsFile: true,
                    acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf'],
                    maxFileSize: '3MB',
                    labelIdle: `
                        <div class="flex flex-col items-center gap-2 py-4">
                            <div class="p-4 bg-blue-50 rounded-full transition-transform duration-300 hover:scale-110">
                                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                        <div class="text-center">
                                    <p class="text-base font-bold text-gray-700"><span class="filepond--label-action">Klik</span> atau Tarik gambar ke sini</p>
                                    <p class="text-xs text-gray-500 mt-1 font-medium">PNG, JPG, WEBP (Maksimum 2MB)</p>
                                </div>
                        </div>
                    `,
                });
            }

            const bst = document.querySelector('#bstPond');
            if (bst) {
                FilePond.create(bst, {
                    storeAsFile: true,
                    acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf'],
                    maxFileSize: '3MB',
                    labelIdle: `
                        <div class="flex flex-col items-center gap-2 py-4">
                            <div class="p-4 bg-blue-50 rounded-full transition-transform duration-300 hover:scale-110">
                                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                        <div class="text-center">
                                    <p class="text-base font-bold text-gray-700"><span class="filepond--label-action">Klik</span> atau Tarik gambar ke sini</p>
                                    <p class="text-xs text-gray-500 mt-1 font-medium">PNG, JPG, WEBP (Maksimum 2MB)</p>
                                </div>
                        </div>
                    `,
                });
            }

            // Sync Event
            if (paymentAmount) paymentAmount.addEventListener('input', syncPaymentSummary);
            syncPaymentSummary();
        });

        @if (session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#53BF6A'
            });
        @endif
    </script>
@endsection
