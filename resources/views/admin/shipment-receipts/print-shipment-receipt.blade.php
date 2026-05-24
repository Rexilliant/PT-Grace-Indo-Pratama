@extends('admin.layout.master')

@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-penerimaan-pengiriman-produk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    <div class="p-4 sm:p-8 bg-white min-h-screen" id="printableArea">
        {{-- Tombol Navigasi --}}
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4 print:hidden">
            <div class="flex items-center gap-3">
                <a href="{{ route('shipment-receipts') }}"
                    class="group flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:text-[#2E7E3F] hover:border-[#2E7E3F]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:-translate-x-1"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar
                </a>
                <span class="text-gray-300">|</span>
                <h2 class="text-sm font-medium text-gray-500">Preview Dokumen Penerimaan Barang</h2>
            </div>

            <button onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-[#2E7E3F] to-[#275931] px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-green-900/20 transition-all hover:scale-[1.02] hover:shadow-xl active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Dokumen
            </button>
        </div>

        {{-- Header Dokumen --}}
        <div class="border-b-4 border-gray-800 pb-4 mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-black uppercase tracking-tighter text-gray-900">Shipment Receipt Report</h1>
                <p class="text-sm text-gray-600 font-medium">No. Receipt: <span
                        class="font-mono font-bold text-black">SR-{{ str_pad($shipmentReceipt->id, 6, '0', STR_PAD_LEFT) }}</span>
                </p>
            </div>
            <div class="text-right text-xs font-semibold text-gray-500">
                <p>Dicetak Pada: {{ now()->format('d/m/Y H:i') }}</p>
                <p>Oleh: {{ Auth::user()->name }}</p>
            </div>
        </div>

        {{-- Info Utama --}}
        <div class="grid grid-cols-3 gap-6 mb-8">
            <div class="col-span-2 grid grid-cols-2 gap-6">
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kode Shipment Asal</label>
                    <p class="font-mono font-bold text-gray-800 border-l-4 border-[#2E7E3F] pl-2 uppercase">
                        {{ $shipmentReceipt->shipment->shipment_code ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Gudang Penerima</label>
                    <p class="font-bold text-gray-800 border-l-4 border-[#2E7E3F] pl-2 uppercase">
                        {{ $shipmentReceipt->shipment->warehouse->name ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Diterima Oleh</label>
                    <p class="font-bold text-gray-800 border-l-4 border-[#2E7E3F] pl-2 uppercase">
                        {{ $shipmentReceipt->receivedBy->name ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal Kedatangan</label>
                    <p class="font-bold text-gray-800 border-l-4 border-[#2E7E3F] pl-2 uppercase">
                        {{ $shipmentReceipt->received_at ? $shipmentReceipt->received_at->format('d F Y H:i') : '-' }}</p>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 flex flex-col items-center justify-center">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Status Dokumen</label>
                <span
                    class="text-xl font-black uppercase px-4 py-1 rounded-lg border-2 
                    {{ $shipmentReceipt->status === 'disetujui'
                        ? 'border-green-600 text-green-600'
                        : ($shipmentReceipt->status === 'ditolak'
                            ? 'border-red-600 text-red-600'
                            : 'border-blue-600 text-blue-600') }}">
                    {{ $shipmentReceipt->status }}
                </span>
            </div>
        </div>

        {{-- Tabel Daftar Item --}}
        <div class="mb-8">
            <h3 class="text-xs font-black mb-3 uppercase tracking-[0.2em] text-gray-600">Daftar Item Diterima</h3>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-gray-800 text-white [&>th]:px-4 [&>th]:py-3 [&>th]:text-[10px] [&>th]:uppercase [&>th]:tracking-wider">
                        <th class="text-center rounded-tl-lg w-12">No</th>
                        <th>Informasi Produk</th>
                        <th class="text-center">Qty Kirim</th>
                        <th class="text-center">Qty Terima</th>
                        <th class="text-center">Selisih</th>
                        <th class="rounded-tr-lg">Catatan</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach ($shipmentReceipt->items as $index => $item)
                        <tr class="border-b border-gray-300 hover:bg-gray-50">
                            <td class="px-4 py-3 text-center font-mono text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-800">
                                    {{ $item->shipmentItem->productStock->productVariant->name ?? '-' }}</div>
                                <div class="text-[10px] text-blue-600 font-mono font-bold">
                                    {{ $item->shipmentItem->productStock->productVariant->sku ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $item->shipmentItem->quantity }}</td>
                            <td class="px-4 py-3 text-center font-black text-[#2E7E3F] bg-green-50/30">
                                {{ $item->qty_received }}</td>
                            <td
                                class="px-4 py-3 text-center font-bold {{ $item->shipmentItem->quantity - $item->qty_received > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                {{ $item->shipmentItem->quantity - $item->qty_received }}
                            </td>
                            <td class="px-4 py-3 text-xs italic text-gray-500">{{ $item->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Alasan Penolakan --}}
        @if ($shipmentReceipt->status === 'ditolak')
            <div class="mb-6 bg-red-50 border-l-4 border-red-600 p-4">
                <label class="text-[10px] font-black text-red-600 uppercase tracking-widest block mb-1">Alasan
                    Penolakan:</label>
                <p class="text-sm text-red-700 font-bold">{{ $shipmentReceipt->reject_reason }}</p>
            </div>
        @endif

        {{-- Catatan & Tanda Tangan --}}
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div class="border-2 border-dashed border-gray-300 p-4 rounded-xl">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Catatan
                    Penerimaan:</label>
                <p class="text-sm text-gray-700 italic leading-relaxed">
                    {{ $shipmentReceipt->notes ?? 'Tidak ada catatan khusus.' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4 text-center">
                <div class="flex flex-col justify-between">
                    <p class="text-[9px] font-black uppercase text-gray-500">Penerima</p>
                    <div class="mt-12 border-t border-gray-800 pt-1">
                        <p class="text-xs font-bold uppercase">{{ $shipmentReceipt->receivedBy->name ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex flex-col justify-between">
                    <p class="text-[9px] font-black uppercase text-gray-500">Pengirim</p>
                    <div class="mt-12 border-t border-gray-800 pt-1">
                        <p class="text-xs font-bold uppercase">( ............................ )</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lampiran Bukti Foto (Halaman Baru) --}}
        @php $damageProofs = $shipmentReceipt->getMedia('damage_proofs'); @endphp
        @if ($damageProofs->count() > 0)
            <div class="print:break-before-page pt-8">
                <div class="border-b-2 border-gray-200 pb-2 mb-4">
                    <h3 class="text-sm font-black uppercase tracking-widest text-gray-700">Lampiran Bukti Kondisi Barang
                    </h3>
                    <p class="text-[10px] text-gray-500 italic">Lampiran dokumen No:
                        SR-{{ str_pad($shipmentReceipt->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    @foreach ($damageProofs as $media)
                        <div class="break-inside-avoid border border-gray-200 p-2 rounded-lg bg-gray-50">
                            <div class="aspect-video overflow-hidden rounded border border-gray-300">
                                <img src="{{ $media->getFullUrl() }}" class="w-full h-full object-cover">
                            </div>
                            <p class="text-[8px] mt-1 text-center text-gray-400 font-mono">{{ $media->file_name }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Footer --}}
        <div class="mt-8 pt-4 border-t border-gray-100 hidden print:block">
            <p class="text-[8px] text-gray-400 text-center italic">
                Dokumen ini dicetak otomatis melalui Sistem Pergudangan Digital. <br>
                ID Transaksi: {{ $shipmentReceipt->id }} | Dicetak: {{ now()->format('d/m/Y H:i:s') }}
            </p>
        </div>
    </div>

    <style>
        @media print {
            @page {
                margin: 1.5cm;
                size: A4;
            }

            body {
                background: white !important;
                -webkit-print-color-adjust: exact !important;
            }

            .print\:hidden {
                display: none !important;
            }

            #printableArea {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }

            .print\:break-before-page {
                page-break-before: always !important;
            }

            .break-inside-avoid {
                page-break-inside: avoid !important;
            }

            /* Warna Force */
            .bg-gray-800 {
                background-color: #1f2937 !important;
            }

            .bg-gray-50 {
                background-color: #f9fafb !important;
            }

            .bg-green-50\/30 {
                background-color: rgba(240, 253, 244, 0.3) !important;
            }

            .text-white {
                color: white !important;
            }

            .text-[#2E7E3F] {
                color: #2e7e3f !important;
            }

            .border-[#2E7E3F] {
                border-color: #2e7e3f !important;
            }
        }
    </style>
@endsection
