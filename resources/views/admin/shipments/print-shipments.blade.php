@extends('admin.layout.master')

@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-permintaan-pengiriman', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('addCss')

    <style>
        @media print {
            body {
                background: white !important;
                margin: 0;
                padding: 0;
            }

            .print\:hidden {
                display: none !important;
            }

            #printableArea {
                padding: 0 !important;
                border: none !important;
                min-height: auto !important;
                width: 100% !important;
            }

            @page {
                size: auto;
                margin: 10mm;
            }

            .page-break-inside-avoid {
                break-inside: avoid;
            }
        }
    </style>
@endsection

@section('content')
    <div class="p-4 sm:p-8 bg-white min-h-screen" id="printableArea">

        {{-- Tombol Navigasi --}}
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4 print:hidden">
            <div class="flex items-center gap-3">
                <a href="{{ route('shipments') }}"
                    class="group flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:text-[#2E7E3F] hover:border-[#2E7E3F]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:-translate-x-1"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar
                </a>
                <span class="text-gray-300">|</span>
                <h2 class="text-sm font-medium text-gray-500">Preview Surat Jalan</h2>
            </div>

            <button onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-[#2E7E3F] to-[#275931] px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-green-900/20 transition-all hover:scale-[1.02] hover:shadow-xl active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Sekarang
            </button>
        </div>

        {{-- Header Dokumen --}}
        <div
            class="border-b-2 border-gray-800 pb-4 mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
            <div>
                <h1 class="text-2xl font-bold uppercase tracking-wide">Surat Jalan / Pengiriman</h1>
                <p class="text-sm text-gray-600 mt-1">Kode: <span
                        class="font-mono font-bold text-black">{{ $shipment->shipment_code }}</span></p>
            </div>
            <div class="text-left sm:text-right text-sm bg-gray-50 p-3 rounded border border-gray-200">
                <p class="font-semibold">Tanggal Cetak: <span class="font-normal">{{ now()->format('d/m/Y H:i') }}</span>
                </p>
                <p class="font-semibold">Status: <span class="font-bold uppercase">{{ $shipment->status }}</span></p>
                <p class="font-semibold">Jenis Pengiriman: <span class="font-normal">{{ $shipment->shipment_type }}</span>
                </p>
            </div>
        </div>

        {{-- Info Utama --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-8">
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase block">Gudang Tujuan</label>
                    <p class="font-semibold border-b border-gray-200 pb-1">{{ $shipment->warehouse->name ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase block">Alamat Pengiriman</label>
                    <p class="font-semibold border-b border-gray-200 pb-1">{{ $shipment->address ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase block">Kontak / Penerima</label>
                    <p class="font-semibold border-b border-gray-200 pb-1">{{ $shipment->contact ?? '-' }}</p>
                </div>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase block">Tanggal Permintaan</label>
                    <p class="font-semibold border-b border-gray-200 pb-1">
                        {{ \Carbon\Carbon::parse($shipment->shipment_request_at)->format('d F Y') }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase block">Jadwal Pengiriman</label>
                    <p class="font-semibold border-b border-gray-200 pb-1">
                        {{ $shipment->shipment_at ? \Carbon\Carbon::parse($shipment->shipment_at)->format('d F Y') : 'Belum Dijadwalkan' }}
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase block">Armada</label>
                        <p class="font-semibold border-b border-gray-200 pb-1">{{ $shipment->shipping_fleet ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase block">Layanan</label>
                        <p class="font-semibold border-b border-gray-200 pb-1">{{ $shipment->shipment_services ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Item Pengiriman --}}
        <div class="mb-8 overflow-x-auto">
            <h3 class="text-sm font-bold mb-3 uppercase tracking-wider text-gray-700">Detail Produk</h3>
            <table class="w-full text-left border-collapse border border-gray-400 min-w-[600px]">
                <thead class="bg-gray-100">
                    <tr
                        class="[&>th]:border [&>th]:border-gray-400 [&>th]:px-4 [&>th]:py-3 [&>th]:text-xs [&>th]:font-bold">
                        <th class="w-12 text-center">NO</th>
                        <th class="w-12 text-center">SKU</th>
                        <th>NAMA PRODUK (VARIANT)</th>
                        <th>SUMBER GUDANG</th>
                        <th class="text-center w-32">QTY DIKIRIM</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($shipment->shipmentItems as $index => $item)
                        <tr class="[&>td]:border [&>td]:border-gray-400 [&>td]:px-4 [&>td]:py-2 text-sm hover:bg-gray-50">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center font-mono">{{ $item->productStock->productVariant->sku ?? '-' }}</td>
                            <td class="font-semibold">
                                {{ $item->productStock->productVariant->name ?? 'Produk Tidak Diketahui' }}</td>
                            <td class="text-gray-700">{{ $item->productStock->warehouse->name ?? '-' }}</td>
                            <td class="text-center font-bold text-lg">{{ $item->quantity }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border border-gray-400 px-4 py-4 text-center text-gray-500 italic">
                                Data produk kosong.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Catatan & Alasan Penolakan --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-12">
            <div class="border p-4 rounded-lg border-gray-300 bg-gray-50">
                <label class="text-xs font-bold text-gray-500 uppercase block mb-2">Catatan:</label>
                <p class="text-sm italic text-gray-800">{{ $shipment->notes ?? 'Tidak ada catatan.' }}</p>
            </div>

            @if ($shipment->status === 'Ditolak' && $shipment->reason)
                <div class="border p-4 rounded-lg border-red-200 bg-red-50">
                    <label class="text-xs font-bold text-red-600 uppercase block mb-2">Alasan Penolakan:</label>
                    <p class="text-sm font-semibold text-red-700">{{ $shipment->reason }}</p>
                </div>
            @endif
        </div>

        {{-- Tanda Tangan --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 text-center mt-auto page-break-inside-avoid">
            <div class="flex flex-col h-32 justify-between">
                <p class="text-xs font-bold uppercase">Penanggung Pemesanan</p>
                <div class="mt-auto">
                    <p class="font-bold border-b border-gray-400 inline-block px-4 pb-1">
                        {{ $shipment->personResponsible->name ?? '-' }}</p>
                </div>
            </div>
            <div class="hidden sm:block"></div> {{-- Spacer untuk desktop --}}

            {{-- Bagian Penanggung Jawab Status yang diubah --}}
            <div class="flex flex-col h-32 justify-between">
                <p class="text-xs font-bold uppercase">Penanggung Jawab Status</p>
                <div class="mt-auto">
                    @php
                        $statusUser = '(..................................)';
                        $statusLower = strtolower($shipment->status);

                        if (in_array($statusLower, ['disetujui', 'dikirim', 'selesai'])) {
                            $statusUser = $shipment->approvedBy->name ?? $statusUser;
                        } elseif ($statusLower === 'ditolak') {
                            $statusUser = $shipment->rejectedBy->name ?? $statusUser;
                        }
                    @endphp
                    <p class="font-bold border-b border-gray-400 inline-block px-4 pb-1">
                        {{ $statusUser }}
                    </p>
                </div>
            </div>
        </div>
    </div>

@endsection
