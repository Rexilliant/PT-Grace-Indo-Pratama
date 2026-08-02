@extends('admin.layout.master')

@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-barang-masuk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('addCss')
    <style>
        @media print {
            body {
                background: white !important;
            }

            {{-- Sembunyikan elemen layout admin agar tidak ikut tercetak --}} nav,
            .sidebar,
            .navbar,
            .footer,
            .print\:hidden {
                display: none !important;
            }

            #printableArea {
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
                min-height: auto !important;
            }

            @page {
                margin: 1.5cm;
                size: A4;
            }
        }
    </style>
@endsection

@section('content')
    <div class="p-4 sm:p-8 bg-white min-h-screen" id="printableArea">
        {{-- Tombol Navigasi --}}
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4 print:hidden">
            <div class="flex items-center gap-3">
                <a href="{{ route('purchase-receipts') }}"
                    class="group flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:text-[#2E7E3F] hover:border-[#2E7E3F]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:-translate-x-1"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar
                </a>
                <span class="text-gray-300">|</span>
                <h2 class="text-sm font-medium text-gray-500">Preview Bukti Barang Masuk</h2>
            </div>

            <button onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-[#2E7E3F] to-[#275931] px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-green-900/20 transition-all hover:scale-[1.02] hover:shadow-xl active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Bukti
            </button>
        </div>

        {{-- Header Dokumen --}}
        <div class="border-b-2 border-gray-800 pb-4 mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold uppercase">Bukti Penerimaan Barang</h1>
                <p class="text-sm text-gray-600">Nomor: <span
                        class="font-mono font-bold text-black">{{ $receipt->receipt_number }}</span></p>
            </div>
            <div class="text-right text-sm">
                <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</p>
                <p class="text-xs text-gray-500 mt-1 uppercase font-semibold">Dokumen Digital PT Grace Indo Pratama</p>
            </div>
        </div>

        {{-- Info Utama --}}
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div class="space-y-2">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase italic">Penerima (Staff Gudang)</label>
                    <p class="font-semibold border-b border-gray-200 pb-1">{{ $receipt->receivedBy->name ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase italic">Gudang Penyimpanan</label>
                    <p class="font-semibold border-b border-gray-200 pb-1">{{ $receipt->warehouse->name }}</p>
                </div>
            </div>
            <div class="space-y-2">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase italic">Tanggal Barang Masuk</label>
                    <p class="font-semibold border-b border-gray-200 pb-1">
                        {{ $receipt->received_at->format('d F Y') }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase italic">Ref. ID Pengadaan</label>
                    <p class="font-bold border-b border-gray-200 pb-1 uppercase">#{{ $receipt->procurement_id }}</p>
                </div>
            </div>
        </div>

        {{-- Tabel Item --}}
        <div class="mb-8">
            <h3 class="text-sm font-bold mb-3 uppercase tracking-wider">Detail Material Masuk</h3>
            <table class="w-full text-left border-collapse border border-gray-400">
                <thead>
                    <tr class="bg-gray-100 [&>th]:border [&>th]:border-gray-400 [&>th]:px-4 [&>th]:py-2 [&>th]:text-xs">
                        <th class="text-center w-12">NO</th>
                        <th>KODE & NAMA MATERIAL</th>
                        <th class="text-center">JUMLAH TERIMA</th>
                        <th>SATUAN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($receipt->items as $index => $item)
                        <tr class="[&>td]:border [&>td]:border-gray-400 [&>td]:px-4 [&>td]:py-2 text-sm">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->rawMaterial->code ?? 'RM-' . $item->raw_material_id }} -
                                {{ $item->rawMaterial->name }}</td>
                            <td class="text-center font-bold">{{ number_format($item->quantity_received) }}</td>
                            <td>{{ $item->rawMaterial->unit }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-bold bg-gray-50">
                        <td colspan="2" class="border border-gray-400 text-center px-4 py-2 text-xs uppercase">Estimasi
                            Nilai Total</td>
                        <td colspan="2" class="border border-gray-400 px-4 py-2 text-center">
                            Rp {{ number_format($receipt->total_price) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Catatan --}}
        <div class="mb-12">
            <div class="border p-3 rounded border-gray-300 w-full md:w-1/2">
                <label class="text-xs font-bold text-gray-500 uppercase italic block mb-1">Catatan Penerimaan:</label>
                <p class="text-sm italic text-gray-700">{{ $receipt->note ?? 'Tidak ada catatan tambahan.' }}</p>
            </div>
        </div>

        {{-- Tanda Tangan --}}
        <div class="grid grid-cols-2 gap-4 text-center mt-auto">
            <div class="flex flex-col h-36 justify-between">
                <p class="text-xs font-bold uppercase">Petugas Penyerah,</p>
                <div class="mt-auto">
                    <p class="text-gray-400 italic mb-10 text-[10px]">(..........................................)</p>
                    <p class="text-[10px] text-gray-500 underline uppercase italic">Driver / Supplier / Logistik</p>
                </div>
            </div>

            <div class="flex flex-col h-36 justify-between">
                <p class="text-xs font-bold uppercase">Diterima Oleh (Gudang),</p>
                <div class="mt-auto">
                    <p class="font-bold underline uppercase">{{ $receipt->receivedBy->name ?? '-' }}</p>
                    <p class="text-[10px] text-gray-500">Tgl:
                        {{ $receipt->received_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <div class="mt-10 pt-4 border-t border-dashed border-gray-300 print:hidden text-center">
            <p class="text-[10px] text-gray-400 italic">Pastikan printer terhubung dan gunakan ukuran kertas A4 untuk hasil
                terbaik.</p>
        </div>
    </div>
@endsection
