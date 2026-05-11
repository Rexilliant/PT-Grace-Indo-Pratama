@extends('admin.layout.master')

@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-laporan-produksi', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    <div class="p-4 sm:p-8 bg-white min-h-screen" id="printableArea">
        {{-- Tombol Navigasi - Muncul hanya di layar, hilang saat diprint --}}
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4 print:hidden">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.gudang-laporan-produksi') }}"
                    class="group flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:text-[#2E7E3F] hover:border-[#2E7E3F]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:-translate-x-1"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Laporan
                </a>
                <span class="text-gray-300">|</span>
                <h2 class="text-sm font-medium text-gray-500">Preview Dokumen Produksi</h2>
            </div>

            <button onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-[#2E7E3F] to-[#275931] px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-green-900/20 transition-all hover:scale-[1.02] hover:shadow-xl active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Laporan
            </button>
        </div>

        {{-- Header Dokumen --}}
        <div class="border-b-4 border-gray-800 pb-4 mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-black uppercase tracking-tighter">Laporan Produksi Barang</h1>
                <p class="text-sm text-gray-600 font-medium">No. Laporan: <span
                        class="font-mono font-bold text-black">PB-{{ str_pad($productionBatch->id, 5, '0', STR_PAD_LEFT) }}</span></p>
            </div>
            <div class="text-right text-xs font-semibold text-gray-500">
                <p>Dicetak Pada: {{ now()->format('d/m/Y H:i') }}</p>
                <p>User: {{ Auth::user()->name }}</p>
            </div>
        </div>

        {{-- Info Utama Produksi --}}
        <div class="grid grid-cols-2 gap-8 mb-8 border-b border-gray-200 pb-6">
            <div class="space-y-3">
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Penanggung Jawab</label>
                    <p class="font-bold text-gray-800 border-l-4 border-[#53BF6A] pl-2 uppercase">{{ $productionBatch->personResponsible->name ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Lokasi Gudang</label>
                    <p class="font-bold text-gray-800 border-l-4 border-[#53BF6A] pl-2 uppercase">{{ $productionBatch->warehouse->name }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal Masuk</label>
                    <p class="font-bold text-gray-800 border-l-4 border-[#53BF6A] pl-2 uppercase">
                        {{ \Carbon\Carbon::parse($productionBatch->entry_date)->format('d F Y') }}</p>
                </div>
            </div>
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                <label class="text-[10px] font-black text-[#2E7E3F] uppercase tracking-widest block mb-2">Detail Produk Jadi</label>
                <div class="grid grid-cols-2 gap-2">
                    <span class="text-xs text-gray-500">ID Barang</span>
                    <span class="text-xs font-bold">: {{ $productionBatch->productStock->productVariant->product->code ?? '-' }}</span>
                    
                    <span class="text-xs text-gray-500">SKU</span>
                    <span class="text-xs font-bold text-blue-700">: {{ $productionBatch->productStock->productVariant->sku }}</span>
                    
                    <span class="text-xs text-gray-500">Nama Produk</span>
                    <span class="text-xs font-bold">: {{ $productionBatch->productStock->productVariant->name }}</span>
                    
                    <span class="text-xs text-gray-500 font-bold uppercase">Total Hasil</span>
                    <span class="text-lg font-black text-[#2E7E3F]">: {{ $productionBatch->quantity }} {{ $productionBatch->productStock->productVariant->unit }}</span>
                </div>
            </div>
        </div>

        {{-- Tabel Pemakaian Bahan Baku --}}
        <div class="mb-8">
            <h3 class="text-xs font-black mb-3 uppercase tracking-[0.2em] text-gray-600">Konsumsi Bahan Baku (Material Usage)</h3>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white [&>th]:px-4 [&>th]:py-3 [&>th]:text-[10px] [&>th]:uppercase [&>th]:tracking-wider">
                        <th class="text-center rounded-tl-lg w-12">No</th>
                        <th>Kode & Nama Material</th>
                        <th class="text-center">Stok Awal</th>
                        <th class="text-center">Jumlah Digunakan</th>
                        <th class="rounded-tr-lg">Satuan</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach ($productionBatch->materials as $index => $material)
                        <tr class="border-b border-gray-300 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-center font-mono text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-800">{{ $material->rawMaterial->name }}</div>
                                <div class="text-[10px] text-gray-500 font-mono">{{ $material->rawMaterial->code ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">{{ $material->stock }}</td>
                            <td class="px-4 py-3 text-center font-black text-red-600 bg-red-50/50 italic">
                                - {{ $material->quantity_use }}
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-600">{{ $material->rawMaterial->unit }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Catatan --}}
        <div class="mb-12">
            <div class="border-2 border-dashed border-gray-300 p-4 rounded-xl">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Catatan Produksi:</label>
                <p class="text-sm text-gray-700 italic leading-relaxed">
                    {{ $productionBatch->note ?? 'Tidak ada catatan khusus untuk batch produksi ini.' }}
                </p>
            </div>
        </div>

        {{-- Tanda Tangan --}}
        <div class="grid grid-cols-3 gap-12 text-center">
            <div class="space-y-16">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-500">Operator Produksi</p>
                <div class="border-t border-gray-800 pt-2">
                    <p class="text-sm font-bold uppercase">{{ $productionBatch->personResponsible->name ?? '-' }}</p>
                    <p class="text-[9px] text-gray-400 uppercase font-medium">Digital Signature Valid</p>
                </div>
            </div>
            
            <div>
                {{-- Logo atau Cap Perusahaan (Opsional) --}}
                <div class="flex items-center justify-center h-full">
                    <div class="w-20 h-20 border-4 border-gray-100 rounded-full flex items-center justify-center opacity-20 rotate-12">
                        <span class="text-[10px] font-black uppercase tracking-tighter">PRODUCED</span>
                    </div>
                </div>
            </div>

            <div class="space-y-16">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-500">Kepala Gudang / Supervisor</p>
                <div class="border-t border-gray-800 pt-2">
                    <p class="text-sm font-bold uppercase">( ............................ )</p>
                    <p class="text-[9px] text-gray-400 uppercase font-medium">NIP / Tanda Tangan Basah</p>
                </div>
            </div>
        </div>

        {{-- Footer Print --}}
        <div class="mt-12 pt-4 border-t border-gray-100 hidden print:block">
            <p class="text-[8px] text-gray-400 text-center italic">
                Dokumen ini merupakan laporan resmi sistem pergudangan yang dihasilkan secara otomatis. Segala manipulasi data tanpa persetujuan akan diproses sesuai ketentuan perusahaan.
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
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .print\:hidden {
                display: none !important;
            }
            #printableArea {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }
            /* Memastikan warna background pada tabel/elemen tetap muncul saat print */
            .bg-gray-100 { background-color: #f3f4f6 !important; }
            .bg-gray-800 { background-color: #1f2937 !important; }
            .bg-gray-50 { background-color: #f9fafb !important; }
            .bg-red-50\/50 { background-color: rgba(254, 242, 242, 0.5) !important; }
            .text-white { color: white !important; }
            .text-[#2E7E3F] { color: #2e7e3f !important; }
        }
    </style>
@endsection