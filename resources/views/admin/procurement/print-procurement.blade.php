@extends('admin.layout.master')


@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pengadaan', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    <div class="p-4 sm:p-8 bg-white min-h-screen" id="printableArea">
        {{-- Tombol Navigasi - Modern & Sleek --}}
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4 print:hidden">
            <div class="flex items-center gap-3">
                <a href="{{ route('procurements') }}"
                    class="group flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:text-[#2E7E3F] hover:border-[#2E7E3F]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:-translate-x-1"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar
                </a>
                <span class="text-gray-300">|</span>
                <h2 class="text-sm font-medium text-gray-500">Preview Dokumen Cetak</h2>
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
        <div class="border-b-2 border-gray-800 pb-4 mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold uppercase">Form Pengadaan Barang</h1>
                <p class="text-sm text-gray-600">ID Pengadaan: <span
                        class="font-mono font-bold text-black">{{ $procurement->id }}</span></p>
            </div>
            <div class="text-right text-sm">
                <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        {{-- Info Utama --}}
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div class="space-y-2">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase italic">Pemesan</label>
                    <p class="font-semibold border-b border-gray-200 pb-1">{{ $procurement->userRequest->name ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase italic">Gudang Tujuan</label>
                    <p class="font-semibold border-b border-gray-200 pb-1">{{ $procurement->warehouse->name }}</p>
                </div>
            </div>
            <div class="space-y-2">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase italic">Tanggal Pemesanan</label>
                    <p class="font-semibold border-b border-gray-200 pb-1">
                        {{ \Carbon\Carbon::parse($procurement->purchase_at)->format('d F Y') }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase italic">Status Saat Ini</label>
                    <p class="font-bold border-b border-gray-200 pb-1 uppercase">{{ $procurement->status }}</p>
                </div>
            </div>
        </div>

        {{-- Tabel Item --}}
        <div class="mb-8">
            <h3 class="text-sm font-bold mb-3 uppercase tracking-wider">Daftar Bahan Baku</h3>
            <table class="w-full text-left border-collapse border border-gray-400">
                <thead>
                    <tr class="bg-gray-100 [&>th]:border [&>th]:border-gray-400 [&>th]:px-4 [&>th]:py-2 [&>th]:text-xs">
                        <th class="text-center">NO</th>
                        <th>KODE & NAMA MATERIAL</th>
                        <th class="text-center">JUMLAH</th>
                        <th>SATUAN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($procurement->procurement_items as $index => $item)
                        <tr class="[&>td]:border [&>td]:border-gray-400 [&>td]:px-4 [&>td]:py-2 text-sm">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->raw_material->code ?? 'RM-' . $item->raw_material->id }} -
                                {{ $item->raw_material->name }}</td>
                            <td class="text-center font-bold">{{ $item->quantity_requested }}</td>
                            <td>{{ $item->raw_material->unit }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Catatan & Alasan --}}
        <div class="grid grid-cols-2 gap-8 mb-12">
            <div class="border p-3 rounded border-gray-300">
                <label class="text-xs font-bold text-gray-500 uppercase italic block mb-1">Catatan Pemesan:</label>
                <p class="text-sm italic">{{ $procurement->note ?? 'Tidak ada catatan.' }}</p>
            </div>
            @if ($procurement->status === 'Ditolak')
                <div class="border p-3 rounded border-red-200 bg-red-50">
                    <label class="text-xs font-bold text-red-600 uppercase italic block mb-1">Alasan Penolakan:</label>
                    <p class="text-sm font-semibold">{{ $procurement->reason }}</p>
                </div>
            @endif
        </div>

        {{-- Tanda Tangan --}}
        <div class="grid grid-cols-3 gap-4 text-center mt-auto">
            <div class="flex flex-col h-32 justify-between">
                <p class="text-xs font-bold uppercase">Dipesan Oleh,</p>
                <div class="mt-auto">
                    <p class="font-bold underline">{{ $procurement->userRequest->name ?? '-' }}</p>
                    <p class="text-[10px] text-gray-500">Tgl:
                        {{ \Carbon\Carbon::parse($procurement->created_at)->format('d/m/Y') }}</p>
                </div>
            </div>
            <div></div> {{-- Spacer --}}
            <div class="flex flex-col h-32 justify-between">
                @if ($procurement->status === 'Disetujui')
                    <p class="text-xs font-bold uppercase">Disetujui Oleh,</p>
                    <div class="mt-auto">
                        <p class="font-bold underline">{{ $procurement->userApproved->name ?? '-' }}</p>
                        <p class="text-[10px] text-gray-500">Tgl:
                            {{ \Carbon\Carbon::parse($procurement->approved_at)->format('d/m/Y') }}</p>
                    </div>
                @elseif($procurement->status === 'Ditolak')
                    <p class="text-xs font-bold uppercase text-red-600">Ditolak Oleh,</p>
                    <div class="mt-auto">
                        <p class="font-bold underline text-red-600">{{ $procurement->userRejected->name ?? '-' }}</p>
                        <p class="text-[10px] text-gray-500">Tgl:
                            {{ \Carbon\Carbon::parse($procurement->rejected_at)->format('d/m/Y') }}</p>
                    </div>
                @else
                    <p class="text-xs font-bold uppercase text-gray-400 italic">Menunggu Persetujuan</p>
                @endif
            </div>
        </div>
    </div>

    <style>
        @media print {
            body {
                background: white;
            }

            .print\:hidden {
                display: none !important;
            }

            #printableArea {
                padding: 0 !important;
                border: none !important;
            }

            @page {
                margin: 1cm;
            }
        }
    </style>
@endsection
