<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pemenuhan PO</title>

    @vite(['resources/css/app.css'])

    <style>
        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        html,
        body {
            background: #e5e7eb;
        }

        .sheet {
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

            .sheet {
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

        $docNumber =
            'PO-FUL/' . Carbon::parse($sale->report_date)->format('Y/m/') . str_pad($sale->id, 4, '0', STR_PAD_LEFT);
        $companyName = 'PT GRACE INDO PRATAMA';
        $companyAddress = 'Jl. Industri No. 12, Medan, Sumatera Utara';
        $companyPhone = '(061) 12345678';

        $saleDate = Carbon::parse($sale->sale_date)->translatedFormat('d F Y');
        $printDate = now()->translatedFormat('d F Y');

        $customerDisplay = $sale->customer_name ?: '-';
        $customerRegion = trim(
            ($sale->customer_city ? $sale->customer_city . ', ' : '') . ($sale->customer_province ?: ''),
        );

        $totalOrdered = $sale->items->sum('quantity');
        $totalFulfilled = $sale->items->sum('fulfilled_quantity');
        $totalRemaining = max(0, $totalOrdered - $totalFulfilled);
    @endphp

    {{-- Toolbar --}}
    <div class="no-print mx-auto flex w-[210mm] items-center justify-between gap-3 py-5">
        <div>
            <div class="text-sm font-semibold text-gray-500">Preview Surat Pemenuhan PO</div>
            <div class="mt-1 text-lg font-bold text-gray-900">{{ $docNumber }}</div>
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

    {{-- Kertas --}}
    <div class="sheet mx-auto mb-8 bg-white px-12 py-10 shadow-[0_10px_30px_rgba(0,0,0,0.08)]">

        {{-- Header Perusahaan --}}
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <img src="{{ asset('image/bhos-logo.png') }}" alt="Logo" class="h-14 w-auto">
                <div>
                    <div class="text-[18px] font-extrabold tracking-wide text-[#127a45]">{{ $companyName }}</div>
                    <div class="mt-1 text-[11px] leading-4 text-gray-600">
                        {{ $companyAddress }}<br>
                        Telp: {{ $companyPhone }}
                    </div>
                </div>
            </div>
            <div class="text-right text-[11px] text-gray-700">
                <div class="font-semibold">No. Dokumen</div>
                <div class="text-sm font-bold text-gray-900">{{ $docNumber }}</div>
                <div class="mt-2 font-semibold">Tanggal Cetak</div>
                <div>{{ $printDate }}</div>
            </div>
        </div>

        <div class="mt-6 border-t-2 border-gray-800"></div>

        {{-- Judul --}}
        <div class="mt-6 text-center">
            <h1 class="text-[20px] font-extrabold tracking-wide text-gray-900">
                SURAT PEMENUHAN PRE-ORDER (PO)
            </h1>
            <p class="mt-1 text-[12px] text-gray-600">
                Dokumen ini mencatat penyerahan barang dari stok ready terhadap pesanan Pre-Order
            </p>
        </div>

        <div class="mt-6 border-t border-gray-300"></div>

        {{-- Info Transaksi & Customer --}}
        <div class="mt-6 grid grid-cols-2 gap-8 text-[12px]">
            <div>
                <div class="font-extrabold text-gray-900 mb-2">Data Penjualan</div>
                <div class="grid grid-cols-[130px_1fr] gap-y-1">
                    <div class="text-gray-600">No. Penjualan</div>
                    <div class="font-semibold">: SALE-{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</div>
                    <div class="text-gray-600">Tanggal Jual</div>
                    <div class="font-semibold">: {{ $saleDate }}</div>
                    <div class="text-gray-600">Jenis Penjualan</div>
                    <div class="font-semibold">: {{ $sale->sale_type }}</div>
                    <div class="text-gray-600">Gudang</div>
                    <div class="font-semibold">: {{ $sale->warehouse?->name ?? '-' }}</div>
                    <div class="text-gray-600">Status Pembayaran</div>
                    <div class="font-semibold">: {{ $sale->status }}</div>
                </div>
            </div>
            <div>
                <div class="font-extrabold text-gray-900 mb-2">Data Pemesan</div>
                <div class="grid grid-cols-[130px_1fr] gap-y-1">
                    <div class="text-gray-600">Nama</div>
                    <div class="font-semibold">: {{ $customerDisplay }}</div>
                    <div class="text-gray-600">Kontak</div>
                    <div class="font-semibold">: {{ $sale->customer_contact ?: '-' }}</div>
                    <div class="text-gray-600">Daerah</div>
                    <div class="font-semibold">: {{ $customerRegion ?: '-' }}</div>
                    <div class="text-gray-600">Alamat</div>
                    <div class="font-semibold">: {{ $sale->customer_address ?: '-' }}</div>
                </div>
            </div>
        </div>

        <div class="mt-6 border-t border-gray-300"></div>

        {{-- Ringkasan Qty --}}
        <div class="mt-6 grid grid-cols-3 gap-4 text-center text-[12px]">
            <div class="rounded-lg border border-gray-300 bg-gray-50 p-3">
                <div class="text-gray-500 font-semibold">Total Ordered</div>
                <div class="mt-1 text-xl font-extrabold text-gray-900">{{ $totalOrdered }}</div>
            </div>
            <div class="rounded-lg border border-green-300 bg-green-50 p-3">
                <div class="text-green-700 font-semibold">Sudah Dipenuhi</div>
                <div class="mt-1 text-xl font-extrabold text-green-700">{{ $totalFulfilled }}</div>
            </div>
            <div class="rounded-lg border border-red-300 bg-red-50 p-3">
                <div class="text-red-700 font-semibold">Sisa PO</div>
                <div class="mt-1 text-xl font-extrabold text-red-700">{{ $totalRemaining }}</div>
            </div>
        </div>

        {{-- Daftar Item --}}
        <div class="mt-6">
            <div class="mb-3 text-[13px] font-extrabold text-gray-900">Rincian Barang Pre-Order</div>

            <table class="w-full border-collapse text-[11px]">
                <thead>
                    <tr class="bg-[#24784d] text-white">
                        <th class="border border-[#1d603d] px-3 py-2.5 text-center font-extrabold w-8">No</th>
                        <th class="border border-[#1d603d] px-3 py-2.5 text-center font-extrabold">SKU</th>
                        <th class="border border-[#1d603d] px-3 py-2.5 text-center font-extrabold">Nama Produk</th>
                        <th class="border border-[#1d603d] px-3 py-2.5 text-center font-extrabold">Ordered</th>
                        <th class="border border-[#1d603d] px-3 py-2.5 text-center font-extrabold">Dipenuhi</th>
                        <th class="border border-[#1d603d] px-3 py-2.5 text-center font-extrabold">Sisa</th>
                        <th class="border border-[#1d603d] px-3 py-2.5 text-center font-extrabold">Unit</th>
                        <th class="border border-[#1d603d] px-3 py-2.5 text-center font-extrabold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->items as $item)
                        @php
                            $remaining = max(0, $item->quantity - $item->fulfilled_quantity);
                            $status =
                                $remaining <= 0 ? 'Lunas' : ($item->fulfilled_quantity > 0 ? 'Sebagian' : 'Belum');
                        @endphp
                        <tr>
                            <td class="border border-gray-300 px-3 py-2 text-center font-semibold">
                                {{ $loop->iteration }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-center font-semibold">
                                {{ $item->productStock?->productVariant?->sku ?? '-' }}
                            </td>
                            <td class="border border-gray-300 px-3 py-2 font-semibold">
                                {{ $item->productStock?->productVariant?->name ?? '-' }}
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center font-semibold">
                                {{ $item->quantity }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-center font-semibold text-green-700">
                                {{ $item->fulfilled_quantity }}
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center font-semibold text-red-600">
                                {{ $remaining }}
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center font-semibold">
                                {{ $item->productStock?->productVariant?->unit ?? '-' }}
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center font-semibold">
                                {{ $status }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Riwayat Pemenuhan --}}
        <div class="mt-6">
            <div class="mb-3 text-[13px] font-extrabold text-gray-900">Riwayat Pemenuhan / Penyerahan Stok</div>

            @if ($sale->fulfillments->count())
                <table class="w-full border-collapse text-[11px]">
                    <thead>
                        <tr class="bg-gray-100 text-gray-800">
                            <th class="border border-gray-300 px-3 py-2 text-center font-extrabold">No</th>
                            <th class="border border-gray-300 px-3 py-2 text-center font-extrabold">Tanggal Penyerahan
                            </th>
                            <th class="border border-gray-300 px-3 py-2 text-center font-extrabold">Produk</th>
                            <th class="border border-gray-300 px-3 py-2 text-center font-extrabold">SKU</th>
                            <th class="border border-gray-300 px-3 py-2 text-center font-extrabold">Qty</th>
                            <th class="border border-gray-300 px-3 py-2 text-center font-extrabold">Petugas</th>
                            <th class="border border-gray-300 px-3 py-2 text-center font-extrabold">Waktu Input</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->fulfillments->sortBy('fulfillment_date') as $fulfill)
                            <tr>
                                <td class="border border-gray-300 px-3 py-2 text-center font-semibold">
                                    {{ $loop->iteration }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-center font-semibold">
                                    {{ $fulfill->fulfillment_date?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="border border-gray-300 px-3 py-2 font-semibold">
                                    {{ $fulfill->saleItem?->productStock?->productVariant?->name ?? '-' }}
                                </td>
                                <td class="border border-gray-300 px-3 py-2 text-center font-semibold">
                                    {{ $fulfill->saleItem?->productStock?->productVariant?->sku ?? '-' }}
                                </td>
                                <td class="border border-gray-300 px-3 py-2 text-center font-semibold text-green-700">
                                    {{ $fulfill->quantity }}
                                </td>
                                <td class="border border-gray-300 px-3 py-2 text-center font-semibold">
                                    {{ $fulfill->createdBy?->name ?? '-' }}
                                </td>
                                <td class="border border-gray-300 px-3 py-2 text-center font-semibold">
                                    {{ $fulfill->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div
                    class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center text-[12px] text-gray-500">
                    Belum ada riwayat pemenuhan stok untuk PO ini.
                </div>
            @endif
        </div>

        <div class="mt-8 border-t border-gray-300"></div>

        {{-- Catatan --}}
        <div class="mt-5 text-[12px] leading-6 text-gray-800">
            <div class="font-extrabold mb-1">Catatan:</div>
            <p>
                Dokumen ini hanya mencatat pemenuhan stok dari gudang terhadap pesanan Pre-Order.
                Bukti Serah Terima final (BST) akan diterbitkan terpisah setelah seluruh pembayaran dinyatakan lunas.
            </p>
        </div>

        {{-- Tanda Tangan --}}
        <div class="mt-10 grid grid-cols-2 gap-16 text-center text-[12px]">
            <div>
                <div class="font-semibold text-gray-800">Petugas Gudang / Pemasaran</div>
                <div class="mt-16 border-t border-gray-400 pt-2 font-bold">
                    ................................
                </div>
                <div class="mt-1 text-gray-500">Tanda Tangan & Nama Jelas</div>
            </div>
            <div>
                <div class="font-semibold text-gray-800">Pemesan / Penerima</div>
                <div class="mt-16 border-t border-gray-400 pt-2 font-bold">
                    ................................
                </div>
                <div class="mt-1 text-gray-500">Tanda Tangan & Nama Jelas</div>
            </div>
        </div>
    </div>
</body>

</html>
