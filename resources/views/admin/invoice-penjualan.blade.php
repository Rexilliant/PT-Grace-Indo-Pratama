<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Penjualan</title>

    @vite(['resources/css/app.css'])

    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        html,
        body {
            background: #e5e7eb;
        }

        .invoice-sheet {
            width: 210mm;
            min-height: 297mm;
        }

        @media print {

            html,
            body {
                background: white !important;
            }

            .no-print {
                display: none !important;
            }

            .invoice-sheet {
                width: 100%;
                min-height: auto;
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
            }
        }
    </style>
</head>

<body class="font-sans text-gray-900">
    @php
        use Carbon\Carbon;

        $invoiceNumber =
            'INV/' . Carbon::parse($sale->report_date)->format('Y/m/') . str_pad($sale->id, 4, '0', STR_PAD_LEFT);

        $companyName = 'PT GRACE INDO PRATAMA';
        $companyAddress = 'Jl. Industri No. 12, Medan, Sumatera Utara';
        $companyPhone = '(061) 12345678';
        $companyEmail = 'sales@graceindopratama.co.id';

        $bankName = 'Transfer Bank';
        $bankAccountName = 'A/N PT Grace Indo Pratama';

        $approvedBy = 'Manager Pemasaran';

        $invoiceDate = Carbon::parse($sale->report_date)->translatedFormat('d F Y');
        $saleDate = Carbon::parse($sale->sale_date)->translatedFormat('d F Y');

        $statusText = strtoupper($sale->status);
        $statusColor = $sale->status === 'Terhutang' ? 'text-red-700' : 'text-green-700';

        $customerDisplay = $sale->customer_name ?: '-';
        $customerRegion = trim(
            ($sale->customer_city ? $sale->customer_city . ', ' : '') . ($sale->customer_province ?: ''),
        );

        $paymentHistories = $sale->paymentHistories->sortBy([['payment_date', 'asc'], ['id', 'asc']])->values();

        $totalPaid = $paymentHistories->sum('amount');
    @endphp

    {{-- top tools --}}
    <div class="no-print mx-auto flex w-[210mm] items-center justify-between gap-3 py-5">
        <div>
            <div class="text-sm font-semibold text-gray-500">Preview Invoice</div>
            <div class="mt-1 text-lg font-bold text-gray-900">{{ $invoiceNumber }}</div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.pemasaran-laporan-penjualan.history-pembayaran', $sale->id) }}"
                class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50">
                Kembali
            </a>

            <button type="button" onclick="window.print()"
                class="inline-flex items-center justify-center rounded-xl bg-[#2D2ACD] px-5 py-3 text-sm font-bold text-white hover:bg-blue-800">
                Cetak
            </button>
        </div>
    </div>

    {{-- invoice paper --}}
    <div class="invoice-sheet mx-auto mb-8 bg-white px-12 py-10 shadow-[0_10px_30px_rgba(0,0,0,0.08)]">
        {{-- title --}}
        <div class="text-center">
            <h1 class="text-[22px] font-extrabold tracking-wide text-gray-900">
                INVOICE PENJUALAN
            </h1>
        </div>

        <div class="mt-4 border-t border-gray-300"></div>

        {{-- header company + invoice info --}}
        <div class="mt-8 grid grid-cols-2 gap-10">
            <div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center mr-4">
                        <img src="{{ asset('build/image/bhos-logo.png') }}" alt="BHOS Technology" class="h-12 w-auto">
                    </div>

                    <div class="text-[18px] font-extrabold leading-tight tracking-wide text-[#127a45]">
                        {{ $companyName }}
                    </div>
                </div>

                <div class="mt-5 space-y-1 text-[11px] leading-4 text-gray-700">
                    <div>{{ $companyAddress }}</div>
                    <div>Telp: {{ $companyPhone }}</div>
                    <div>Email: {{ $companyEmail }}</div>
                </div>
            </div>

            <div class="flex justify-end">
                <div class="mt-5 space-y-1 text-[11px] leading-6 text-gray-700">
                    <table class="w-[310px] text-[11px] leading-3 text-gray-800">
                        <tr>
                            <td class="w-[48%] py-1 text-right font-semibold text-gray-600">Invoice No :</td>
                            <td class="w-[52%] py-1 pl-4 text-right font-semibold">{{ $invoiceNumber }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-right font-semibold text-gray-600">Tanggal Invoice :</td>
                            <td class="py-1 pl-4 text-right font-semibold">{{ $invoiceDate }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-right font-semibold text-gray-600">Tanggal Penjualan :</td>
                            <td class="py-1 pl-4 text-right font-semibold">{{ $saleDate }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-7 border-t border-gray-300"></div>

        {{-- customer --}}
        <div class="mt-7">
            <div class="text-[13px] font-extrabold text-gray-900">Kepada:</div>

            <div class="mt-3 space-y-1 text-[12px] leading-4 text-gray-800">
                <div class="text-[13px] font-extrabold">{{ $customerDisplay }}</div>
                <div>{{ $customerRegion ?: '-' }}</div>
                <div>Kontak: {{ $sale->customer_contact ?: '-' }}</div>
                @if ($sale->customer_address)
                <div>{{ $sale->customer_address }}</div>
                @endif
                {{-- <div>PIC: {{ $sale->personResponsible?->name ?? '-' }}</div> --}}
            </div>
        </div>

        <div class="mt-7 border-t border-gray-300"></div>

        {{-- items --}}
        <div class="mt-5">
            <div class="mb-4 text-[13px] font-extrabold text-gray-900">Detail Barang</div>

            <table class="w-full table-fixed border-collapse text-[11px]">
                <thead>
                    <tr class="bg-[#24784d] text-white">
                        <th class="border border-[#1d603d] px-3 py-3 text-center font-extrabold">SKU</th>
                        <th class="border border-[#1d603d] px-3 py-3 text-center font-extrabold">Nama Produk</th>
                        <th class="border border-[#1d603d] px-3 py-3 text-center font-extrabold">Qty</th>
                        <th class="border border-[#1d603d] px-3 py-3 text-center font-extrabold">Unit</th>
                        <th class="border border-[#1d603d] px-3 py-3 text-center font-extrabold">Harga Satuan</th>
                        <th class="border border-[#1d603d] px-3 py-3 text-center font-extrabold">Diskon</th>
                        <th class="border border-[#1d603d] px-3 py-3 text-center font-extrabold">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->items as $item)
                        <tr class="bg-white">
                            <td class="border border-gray-300 px-3 py-3 text-center font-semibold">
                                {{ $item->productStock?->productVariant?->sku ?? '-' }}
                            </td>
                            <td class="border border-gray-300 px-3 py-3 font-semibold">
                                {{ $item->productStock?->productVariant?->name ?? '-' }}
                            </td>
                            <td class="border border-gray-300 px-3 py-3 text-center font-semibold">
                                {{ $item->quantity }}
                            </td>
                            <td class="border border-gray-300 px-3 py-3 text-center font-semibold">
                                {{ $item->productStock?->productVariant?->unit ?? '-' }}
                            </td>
                            <td class="border border-gray-300 px-3 py-3 text-right font-semibold">
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                            </td>
                            <td class="border border-gray-300 px-3 py-3 text-right font-semibold">
                                Rp {{ number_format($item->discount, 0, ',', '.') }}
                            </td>
                            <td class="border border-gray-300 px-3 py-3 text-right font-extrabold">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-7 border-t border-gray-300"></div>

        {{-- payment summary --}}
        <div class="mt-5">
            <div class="mb-4 text-[13px] font-extrabold text-gray-900">Ringkasan & Riwayat Pembayaran</div>

            <div class="overflow-hidden border border-gray-300">
                {{-- grand total --}}
                <div class="grid grid-cols-[1fr_auto] border-b border-gray-300 px-4 py-3 text-[12px]">
                    <div class="font-semibold text-gray-700">Grand Total:</div>
                    <div class="font-extrabold text-gray-900">
                        Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                    </div>
                </div>

                {{-- daftar pembayaran --}}
                <div class="border-b border-gray-300 px-4 py-3 text-[12px]">
                    <div class="font-semibold text-gray-700">Riwayat Pembayaran Masuk:</div>

                    @if ($paymentHistories->count())
                        <div class="mt-3 space-y-2">
                            @foreach ($paymentHistories as $payment)
                                <div class="grid grid-cols-[1fr_auto] gap-4 rounded-md bg-gray-50 px-3 py-2">
                                    <div class="text-gray-800">
                                        Pembayaran ke-{{ $loop->iteration }}
                                        — {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}
                                    </div>
                                    <div class="font-bold text-gray-900">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-2 text-gray-500">
                            Belum ada pembayaran tercatat
                        </div>
                    @endif
                </div>

                {{-- total paid --}}
                <div class="grid grid-cols-[1fr_auto] border-b border-gray-300 px-4 py-3 text-[12px]">
                    <div class="font-semibold text-gray-700">Total Sudah Dibayar:</div>
                    <div class="font-extrabold text-gray-900">
                        Rp {{ number_format($totalPaid, 0, ',', '.') }}
                    </div>
                </div>

                {{-- remaining --}}
                <div class="grid grid-cols-[1fr_auto] border-b border-gray-300 px-4 py-3 text-[12px]">
                    <div class="font-semibold text-gray-700">Jumlah Terhutang:</div>
                    <div class="font-extrabold text-gray-900">
                        Rp {{ number_format($sale->debt_amount, 0, ',', '.') }}
                    </div>
                </div>

                {{-- status --}}
                <div class="grid grid-cols-[1fr_auto] bg-gray-100 px-4 py-3 text-[12px]">
                    <div class="font-semibold text-gray-700">Status Pembayaran:</div>
                    <div class="font-black tracking-wide {{ $statusColor }}">
                        {{ $statusText }}
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-7 border-t border-gray-300"></div>

        {{-- payment method --}}
        <div class="mt-5">
            <div class="mb-3 text-[13px] font-extrabold text-gray-900">Metode Pembayaran</div>

            <div class="space-y-1 text-[12px] leading-4 text-gray-800">
                <div>{{ $bankName }}</div>
                <div>{{ $bankAccountName }}</div>
            </div>
        </div>

        <div class="mt-7 border-t border-gray-300"></div>

        {{-- notes --}}
        <div class="mt-5">
            <div class="mb-3 text-[13px] font-extrabold text-gray-900">Catatan</div>

            <div class="text-[12px] leading-7 text-gray-800">
                {{ $sale->notes ?: 'Pengiriman dilakukan maksimal 2 hari kerja setelah pembayaran diverifikasi.' }}
            </div>
        </div>

        <div class="mt-8 border-t border-gray-300"></div>

        {{-- signatures --}}
        <div class="mt-8 grid grid-cols-2 gap-16 text-center">
            <div>
                <div class="text-[12px] text-gray-800">Dibuat oleh</div>
                <div class="mt-16 border-t border-gray-400 pt-3 text-[12px] font-bold text-gray-900">
                    {{ $sale->personResponsible?->name ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-[12px] text-gray-800">Disetujui oleh</div>
                <div class="mt-16 border-t border-gray-400 pt-3 text-[12px] italic font-semibold text-gray-900">
                    {{ $approvedBy }}
                </div>
            </div>
        </div>
    </div>
</body>

</html>
