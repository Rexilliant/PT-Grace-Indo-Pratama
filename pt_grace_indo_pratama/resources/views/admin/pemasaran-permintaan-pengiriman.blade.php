@extends('admin.layout.master')

@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-permintaan-pengiriman', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    @php
        // ============================
        // DUMMY DATA (NANTI DARI DB)
        // ============================
        $orders = [
            [
                'no' => 'SRN001',
                'tgl_pemesanan' => '30/11/2025',
                'tgl_pengiriman' => '01/12/2025',
                'pemesan' => 'Bambang Pratama Putra Hadi',
                'tujuan' => 'PT Pamor Ganda',
                'provinsi' => 'Kalimantan Barat',
                'status' => 'Menunggu', // Menunggu | Ditolak | Dikirim

                'detail' => [
                    'no' => 'SRN001',
                    'tgl_pengajuan' => '30/11/2025',
                    'tgl_permintaan' => '01/12/2025',
                    'pj' => 'Bambang Pratama Putra Hadi',
                    'jenis_pengiriman' => 'Perseorangan',
                    'provinsi' => 'Kalimantan Barat',
                    'armada' => 'Bus',
                    'nama_penerima' => 'Thahirudin',
                    'kontak' => '0812-0000-0000',
                    'alamat' => '',
                    'catatan' => '',
                    'items' => [['BHOS001', 'BHOS Ekstra', '150 Ltr'], ['BHOS002', 'BHOS Turbo', '150 Kg']],
                ],

                'alasan_ditolak' => '',
                'tgl_kirim' => '',
                'invoice_name' => null,
                'kirim' => null,
            ],
            [
                'no' => 'SRN002',
                'tgl_pemesanan' => '30/11/2025',
                'tgl_pengiriman' => '01/12/2025',
                'pemesan' => 'Bambang Pratama Putra Hadi',
                'tujuan' => 'PT Pamor Ganda',
                'provinsi' => 'Riau',
                'status' => 'Ditolak',
                'detail' => [
                    'no' => 'SRN002',
                    'tgl_pengajuan' => '30/11/2025',
                    'tgl_permintaan' => '01/12/2025',
                    'pj' => 'Bambang Pratama Putra Hadi',
                    'jenis_pengiriman' => 'Perseorangan',
                    'provinsi' => 'Riau',
                    'armada' => 'Bus',
                    'nama_penerima' => 'Thahirudin',
                    'kontak' => '0812-0000-0000',
                    'alamat' => '',
                    'catatan' => '',
                    'items' => [['BHOS001', 'BHOS Ekstra', '150 Ltr']],
                ],
                'alasan_ditolak' => 'Stok tidak mencukupi.',
                'tgl_kirim' => '',
                'invoice_name' => null,
                'kirim' => null,
            ],
            [
                'no' => 'SRN003',
                'tgl_pemesanan' => '30/11/2025',
                'tgl_pengiriman' => '01/12/2025',
                'pemesan' => 'Bambang Pratama Putra Hadi',
                'tujuan' => 'PT Pamor Ganda',
                'provinsi' => 'Bengkulu',
                'status' => 'Dikirim',
                'detail' => [
                    'no' => 'SRN003',
                    'tgl_pengajuan' => '30/11/2025',
                    'tgl_permintaan' => '01/12/2025',
                    'pj' => 'Bambang Pratama Putra Hadi',
                    'jenis_pengiriman' => 'Perseorangan',
                    'provinsi' => 'Bengkulu',
                    'armada' => 'Bus',
                    'nama_penerima' => 'Thahirudin',
                    'kontak' => '0812-0000-0000',
                    'alamat' => '',
                    'catatan' => '',
                    'items' => [['BHOS001', 'BHOS Ekstra', '150 Ltr'], ['BHOS002', 'BHOS Turbo', '150 Kg']],
                ],
                'alasan_ditolak' => '',
                'tgl_kirim' => '27/12/2025',
                'invoice_name' => 'invoice_pengiriman.pdf',
                'kirim' => [
                    'no_pengiriman' => 'SEND00001',
                    'pj' => 'Bambang Pratama Putra Hadi',
                    'status' => 'Dikirimkan',
                    'tgl_pengiriman' => '27/12/2025',
                    'jasa' => 'Bus',
                    'items' => [['BHOS001', 'BHOS Turbo', '150 Kg'], ['BHOS002', 'BHOS Turbo', '150 Kg']],
                    'bukti' => [
                        ['PM2.5', 331, -0.2, 0.4, -1.0, -0.6, -0.3, 0.0, 1.0, 0, 0.0],
                        ['RHUI', 331, 0.0, 0.4, -1.0, -0.2, 0.0, 0.3, 1.0, 0, 0.0],
                        ['TEMP', 331, 0.1, 0.4, -1.0, -0.2, 0.2, 0.4, 1.0, 0, 0.0],
                        ['PRE', 331, -0.0, 0.4, -1.0, -0.2, -0.0, 0.2, 1.0, 0, 0.0],
                    ],
                ],
            ],
        ];

        $statusTextClass = function ($status) {
            return match ($status) {
                'Dikirim' => 'text-[#2E7E3F]',
                'Ditolak' => 'text-[#EC0000]',
                default => 'text-gray-600',
            };
        };

        $statusBadgeClass = function ($status) {
            return match ($status) {
                'Dikirim' => 'bg-green-100 text-green-800 border-green-300',
                'Ditolak' => 'bg-red-100 text-red-800 border-red-300',
                default => 'bg-gray-100 text-gray-700 border-gray-300',
            };
        };
    @endphp

    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Pemasaran</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Permintaan Pengiriman</a>
        </div>
    </section>

    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        {{-- top bar --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between mb-3">
            <div class="w-full lg:max-w-[520px]">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text"
                        class="block w-full rounded-lg border border-gray-400 bg-gray-100 pl-10 pr-3 py-2.5 text-sm text-gray-900 focus:border-gray-500 focus:ring-0"
                        placeholder="Search for Name and Date">
                </div>
            </div>

            <div class="flex items-center gap-2 justify-end">
                <a href="#"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2E7E3F] px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-300">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v6h6M20 20v-6h-6M20 8a8 0 00-14.9-3M4 16a8 0 0014.9 3" />
                    </svg>
                    Export .xlsx
                </a>

                <a href="{{ route('admin.add-pemasaran-permintaan-pengiriman') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2D2ACD] px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <span class="text-lg leading-none">+</span>
                    Tambah Baru
                </a>
            </div>
        </div>

        {{-- table --}}
        <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-900">
                    <thead class="bg-[#5aba6f]/70 text-gray-900">
                        <tr class="[&>th]:border-b [&>th]:border-gray-500">
                            <th scope="col" class="px-6 py-3 font-extrabold text-left">Tanggal Pemesanan</th>
                            <th scope="col" class="px-6 py-3 font-extrabold text-left">Tanggal Pengiriman</th>
                            <th scope="col" class="px-6 py-3 font-extrabold text-left">Nama Pemesan</th>
                            <th scope="col" class="px-6 py-3 font-extrabold text-left">Tujuan</th>
                            <th scope="col" class="px-6 py-3 font-extrabold text-left">Aksi</th>
                            <th scope="col" class="px-6 py-3 font-extrabold text-left">Status</th>
                        </tr>
                    </thead>

                    <tbody class="bg-gray-200 divide-y divide-gray-500">
                        @foreach ($orders as $o)
                            <tr class="hover:bg-gray-300">
                                <td class="px-6 py-4 font-semibold">{{ $o['tgl_pemesanan'] }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $o['tgl_pengiriman'] }}</td>
                                <td class="px-6 py-4 font-semibold leading-tight">{{ $o['pemesan'] }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $o['tujuan'] }}</td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-start gap-6 font-semibold">
                                        <button type="button" onclick='openViewModal(@json($o))'
                                            class="text-[#2D2ACD] hover:underline cursor-pointer">
                                            Lihat
                                        </button>

                                        {{-- ✅ Hapus hanya boleh kalau status Menunggu --}}
                                        @if (($o['status'] ?? 'Menunggu') === 'Menunggu')
                                            <a href="#" class="text-[#EC0000] hover:underline">Hapus</a>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold {{ $statusTextClass($o['status']) }}">
                                    {{ $o['status'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- footer / pagination --}}
            <div
                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between
                       bg-gray-200 px-3 sm:px-4 md:px-5 py-3 sm:py-4 border-t border-gray-400">

                <div class="text-xs sm:text-sm font-semibold text-gray-800">
                    Showing 1–10 of 100
                </div>

                <div class="w-full sm:w-auto overflow-x-auto">
                    <div class="inline-flex w-max rounded-lg border border-gray-400 overflow-hidden shadow-sm">
                        <a href="#"
                            class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold bg-gray-200 hover:bg-gray-300 border-r border-gray-400">
                            Previous
                        </a>
                        <a href="#"
                            class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold bg-gray-200 hover:bg-gray-300 border-r border-gray-400">
                            1
                        </a>
                        <a href="#"
                            class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold bg-gray-200 hover:bg-gray-300 border-r border-gray-400">
                            2
                        </a>
                        <span
                            class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold bg-gray-200 border-r border-gray-400">
                            …
                        </span>
                        <a href="#"
                            class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold bg-gray-200 hover:bg-gray-300 border-r border-gray-400">
                            10
                        </a>
                        <a href="#"
                            class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold bg-gray-200 hover:bg-gray-300">
                            Next
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- ================================== --}}
    {{-- MODAL VIEW PERMINTAAN (READ ONLY) --}}
    {{-- ================================== --}}
    <div id="viewModal"
        class="fixed inset-0 z-[9999] hidden bg-black/50 backdrop-blur-sm items-start sm:items-center justify-center p-2 sm:p-4">
        <div
            class="bg-[#f3f3f3] rounded-xl shadow-2xl w-full max-w-5xl mx-auto overflow-hidden animate-scale-in
                   max-h-[calc(100dvh-16px)] sm:max-h-[calc(100dvh-32px)] flex flex-col border border-gray-300">

            {{-- header --}}
            <div
                class="px-4 sm:px-6 py-3 border-b border-gray-300 bg-[#f3f3f3] flex items-center justify-between sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <div class="text-sm font-bold text-gray-800">
                        Laporan Pengajuan Nomor: <span id="vm_no" class="font-extrabold">-</span>
                    </div>

                    <span id="vm_status_badge"
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border bg-gray-100 text-gray-700 border-gray-300">
                        Menunggu
                    </span>
                </div>

                <button type="button" onclick="closeViewModal()"
                    class="inline-flex items-center justify-center h-10 w-10 rounded-full
           bg-gray-700 text-white hover:bg-red-700 active:bg-red-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

            </div>

            {{-- body --}}
            <div class="p-4 sm:p-6 overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Pengajuan</label>
                        <input id="vm_tgl_pengajuan" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Permintaan Pengiriman</label>
                        <input id="vm_tgl_permintaan" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Penanggung Jawab</label>
                        <input id="vm_pj" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Jenis Pengiriman</label>
                        <input id="vm_jenis" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Provinsi</label>
                        <input id="vm_provinsi" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Armada Pengiriman</label>
                        <input id="vm_armada" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Nama Penerima</label>
                        <input id="vm_penerima" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Kontak Penerima</label>
                        <input id="vm_kontak" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Alamat Lengkap</label>
                        <textarea id="vm_alamat" rows="3" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold"></textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="grid grid-cols-3 gap-4 text-xs font-bold text-gray-700 mb-2.5">
                        <div>ID Produk</div>
                        <div>Nama Produk</div>
                        <div>Jumlah Permintaan</div>
                    </div>
                    <div id="vm_items" class="space-y-2"></div>
                </div>

                <div class="mt-4">
                    <label class="block text-xs font-bold text-gray-700 mb-2">Tinggalkan Pesan?</label>
                    <textarea id="vm_catatan" rows="3" readonly
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold"></textarea>
                </div>

                <div class="mt-5 space-y-4">
                    <div id="blokMenunggu" class="hidden rounded-lg border border-gray-300 bg-gray-100 px-4 py-3">
                        <div class="text-sm font-semibold text-gray-700">
                            Status saat ini: <span class="font-extrabold">Menunggu</span>
                        </div>
                        <div class="text-xs text-gray-600 mt-1">
                            Permintaan ini masih menunggu proses.
                        </div>
                    </div>

                    <div id="blokDitolak" class="hidden rounded-lg border border-red-300 bg-red-50 px-4 py-3">
                        <div class="text-sm font-semibold text-red-800">
                            Status saat ini: <span class="font-extrabold">Ditolak</span>
                        </div>
                        <div class="mt-2">
                            <label class="block text-xs font-bold text-red-800 mb-1">Alasan Ditolak</label>
                            <textarea id="vm_alasan_ditolak" rows="3" readonly
                                class="w-full rounded-md border border-red-300 bg-white px-3 py-2 text-sm font-semibold text-gray-900"></textarea>
                        </div>
                    </div>

                    <div id="blokDikirim" class="hidden rounded-lg border border-green-300 bg-green-50 px-4 py-3">
                        <div class="text-sm font-semibold text-green-800">
                            Status saat ini: <span class="font-extrabold">Dikirim</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label class="block text-xs font-bold text-green-900 mb-1">Tanggal Pengiriman</label>
                                <input id="vm_tgl_kirim" readonly
                                    class="w-full rounded-md border border-green-300 bg-white px-3 py-2 text-sm font-semibold text-gray-900">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-green-900 mb-1">Invoice Pengiriman</label>
                                <div class="rounded-xl border border-green-300 bg-white px-4 py-3">
                                    <div class="text-sm font-semibold text-gray-800 break-all" id="vm_invoice_name">-
                                    </div>

                                </div>

                                <div class="mt-3">
                                    <button type="button" id="btnLihatPengiriman"
                                        class="hidden px-4 py-2 rounded-lg text-xs font-bold bg-[#2D2ACD] text-white hover:bg-blue-800">
                                        Lihat Detail Pengiriman
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-5">
                    <button type="button" onclick="closeViewModal()"
                        class="inline-flex items-center justify-center rounded-lg bg-red-600 px-6 sm:px-8 py-2.5 text-sm font-bold text-white hover:bg-red-700">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL PENGIRIMAN --}}
    <div id="kirimModal"
        class="fixed inset-0 z-[10000] hidden bg-black/50 backdrop-blur-sm items-start sm:items-center justify-center p-2 sm:p-4">
        <div
            class="bg-[#f3f3f3] rounded-xl shadow-2xl w-full max-w-5xl mx-auto overflow-hidden animate-scale-in
                   max-h-[calc(100dvh-16px)] sm:max-h-[calc(100dvh-32px)] flex flex-col border border-gray-300">

            <div
                class="px-4 sm:px-6 py-3 border-b border-gray-300 bg-[#f3f3f3] flex items-center justify-between sticky top-0 z-10">
                <div class="text-sm font-bold text-gray-800">
                    Pengiriman Barang Nomor: <span id="km_no" class="font-extrabold">-</span>
                </div>

                <button type="button" onclick="closeKirimModal()"
                    class="inline-flex items-center justify-center h-10 w-10 rounded-full
           bg-red-600 text-white hover:bg-red-700 active:bg-red-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

            </div>

            <div class="p-4 sm:p-6 overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Penanggung Jawab</label>
                        <input id="km_pj" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Status Permintaan</label>
                        <input id="km_status" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Pengiriman</label>
                        <input id="km_tgl" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Jasa Pengiriman</label>
                        <input id="km_jasa" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>
                </div>

                <div class="mt-4">
                    <div class="grid grid-cols-3 gap-4 text-xs font-bold text-gray-700 mb-2.5">
                        <div>ID Produk</div>
                        <div>Nama Produk</div>
                        <div>Jumlah Dikirimkan</div>
                    </div>
                    <div id="km_items" class="space-y-2"></div>
                </div>

                <div class="mt-5">
                    <div class="text-xs font-bold text-gray-700 mb-2.5">Bukti Pengiriman</div>

                    <div class="overflow-x-auto bg-white border border-gray-400 rounded-lg">
                        <table class="w-full text-xs text-left text-gray-900">
                            <thead class="bg-gray-100 border-b border-gray-400">
                                <tr>
                                    <th class="px-3 py-2 font-extrabold border-r border-gray-300">feature</th>
                                    <th class="px-3 py-2 font-extrabold border-r border-gray-300">count</th>
                                    <th class="px-3 py-2 font-extrabold border-r border-gray-300">mean</th>
                                    <th class="px-3 py-2 font-extrabold border-r border-gray-300">std</th>
                                    <th class="px-3 py-2 font-extrabold border-r border-gray-300">min</th>
                                    <th class="px-3 py-2 font-extrabold border-r border-gray-300">Q1</th>
                                    <th class="px-3 py-2 font-extrabold border-r border-gray-300">Q2</th>
                                    <th class="px-3 py-2 font-extrabold border-r border-gray-300">Q3</th>
                                    <th class="px-3 py-2 font-extrabold border-r border-gray-300">max</th>
                                    <th class="px-3 py-2 font-extrabold border-r border-gray-300">missing count</th>
                                    <th class="px-3 py-2 font-extrabold">missing pct</th>
                                </tr>
                            </thead>
                            <tbody id="km_bukti" class="divide-y divide-gray-300"></tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-5">
                    <button type="button" onclick="closeKirimModal()"
                        class="inline-flex items-center justify-center rounded-lg bg-red-600 px-6 sm:px-8 py-2.5 text-sm font-bold text-white hover:bg-red-700">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes scaleIn {
            from {
                transform: scale(.985);
                opacity: .6;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-scale-in {
            animation: scaleIn .12s ease-out;
        }
    </style>

    <script>
        const viewModal = document.getElementById('viewModal');

        const vmNo = document.getElementById('vm_no');
        const vmStatusBadge = document.getElementById('vm_status_badge');

        const vmTglPengajuan = document.getElementById('vm_tgl_pengajuan');
        const vmTglPermintaan = document.getElementById('vm_tgl_permintaan');
        const vmPj = document.getElementById('vm_pj');
        const vmJenis = document.getElementById('vm_jenis');
        const vmProvinsi = document.getElementById('vm_provinsi');
        const vmArmada = document.getElementById('vm_armada');
        const vmPenerima = document.getElementById('vm_penerima');
        const vmKontak = document.getElementById('vm_kontak');
        const vmAlamat = document.getElementById('vm_alamat');
        const vmCatatan = document.getElementById('vm_catatan');
        const vmItems = document.getElementById('vm_items');

        const blokMenunggu = document.getElementById('blokMenunggu');
        const blokDitolak = document.getElementById('blokDitolak');
        const blokDikirim = document.getElementById('blokDikirim');

        const vmAlasanDitolak = document.getElementById('vm_alasan_ditolak');
        const vmTglKirim = document.getElementById('vm_tgl_kirim');
        const vmInvoiceName = document.getElementById('vm_invoice_name');

        const btnLihatPengiriman = document.getElementById('btnLihatPengiriman');

        let currentOrder = null;

        function badgeClass(status) {
            if (status === 'Dikirim') return 'bg-green-100 text-green-800 border-green-300';
            if (status === 'Ditolak') return 'bg-red-100 text-red-800 border-red-300';
            return 'bg-gray-100 text-gray-700 border-gray-300';
        }

        function renderVmItems(items) {
            vmItems.innerHTML = '';
            (items || []).forEach((v) => {
                const row = document.createElement('div');
                row.className = 'grid grid-cols-1 sm:grid-cols-3 gap-3';
                row.innerHTML = `
                    <input value="${v[0] ?? ''}" readonly
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900" />
                    <input value="${v[1] ?? ''}" readonly
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900" />
                    <input value="${v[2] ?? ''}" readonly
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900" />
                `;
                vmItems.appendChild(row);
            });
        }

        function showOnlyStatusBlock(status) {
            blokMenunggu.classList.add('hidden');
            blokDitolak.classList.add('hidden');
            blokDikirim.classList.add('hidden');
            btnLihatPengiriman.classList.add('hidden');

            if (status === 'Menunggu') {
                blokMenunggu.classList.remove('hidden');
            } else if (status === 'Ditolak') {
                blokDitolak.classList.remove('hidden');
                vmAlasanDitolak.value = currentOrder?.alasan_ditolak ?? '';
            } else if (status === 'Dikirim') {
                blokDikirim.classList.remove('hidden');
                vmTglKirim.value = currentOrder?.tgl_kirim ?? '';
                vmInvoiceName.textContent = currentOrder?.invoice_name ?? 'Tidak ada invoice';
                if (currentOrder?.kirim) btnLihatPengiriman.classList.remove('hidden');
            }
        }

        function openViewModal(order) {
            if (!order) return;
            currentOrder = order;

            const d = order.detail || {};

            vmNo.textContent = d.no ?? '-';

            const status = order.status ?? 'Menunggu';
            vmStatusBadge.textContent = status;
            vmStatusBadge.className =
                `inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border ${badgeClass(status)}`;

            vmTglPengajuan.value = d.tgl_pengajuan ?? '';
            vmTglPermintaan.value = d.tgl_permintaan ?? '';
            vmPj.value = d.pj ?? '';
            vmJenis.value = d.jenis_pengiriman ?? '';
            vmProvinsi.value = d.provinsi ?? '';
            vmArmada.value = d.armada ?? '';
            vmPenerima.value = d.nama_penerima ?? '';
            vmKontak.value = d.kontak ?? '';
            vmAlamat.value = d.alamat ?? '';
            vmCatatan.value = d.catatan ?? '';

            renderVmItems(d.items ?? []);
            showOnlyStatusBlock(status);

            viewModal.classList.remove('hidden');
            viewModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeViewModal() {
            viewModal.classList.add('hidden');
            viewModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        viewModal?.addEventListener('click', (e) => {
            if (e.target === viewModal) closeViewModal();
        });

        const kirimModal = document.getElementById('kirimModal');

        const kmNo = document.getElementById('km_no');
        const kmPj = document.getElementById('km_pj');
        const kmStatus = document.getElementById('km_status');
        const kmTgl = document.getElementById('km_tgl');
        const kmJasa = document.getElementById('km_jasa');

        const kmItems = document.getElementById('km_items');
        const kmBukti = document.getElementById('km_bukti');

        function renderKirimItems(items) {
            kmItems.innerHTML = '';
            (items || []).forEach((v) => {
                const row = document.createElement('div');
                row.className = 'grid grid-cols-1 sm:grid-cols-3 gap-3';
                row.innerHTML = `
                    <input value="${v[0] ?? ''}" readonly
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold text-gray-900" />
                    <input value="${v[1] ?? ''}" readonly
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold text-gray-900" />
                    <input value="${v[2] ?? ''}" readonly
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold text-gray-900" />
                `;
                kmItems.appendChild(row);
            });
        }

        function renderBuktiTable(rows) {
            kmBukti.innerHTML = '';
            (rows || []).forEach((r) => {
                const tr = document.createElement('tr');
                tr.className = 'bg-white';
                tr.innerHTML = `
                    <td class="px-3 py-2 border-r border-gray-300">${r[0]}</td>
                    <td class="px-3 py-2 border-r border-gray-300">${r[1]}</td>
                    <td class="px-3 py-2 border-r border-gray-300">${r[2]}</td>
                    <td class="px-3 py-2 border-r border-gray-300">${r[3]}</td>
                    <td class="px-3 py-2 border-r border-gray-300">${r[4]}</td>
                    <td class="px-3 py-2 border-r border-gray-300">${r[5]}</td>
                    <td class="px-3 py-2 border-r border-gray-300">${r[6]}</td>
                    <td class="px-3 py-2 border-r border-gray-300">${r[7]}</td>
                    <td class="px-3 py-2 border-r border-gray-300">${r[8]}</td>
                    <td class="px-3 py-2 border-r border-gray-300">${r[9]}</td>
                    <td class="px-3 py-2">${r[10]}</td>
                `;
                kmBukti.appendChild(tr);
            });
        }

        function openKirimModal(data) {
            if (!data) return;

            kmNo.textContent = data.no_pengiriman ?? '-';
            kmPj.value = data.pj ?? '';
            kmStatus.value = data.status ?? '';
            kmTgl.value = data.tgl_pengiriman ?? '';
            kmJasa.value = data.jasa ?? '';

            renderKirimItems(data.items ?? []);
            renderBuktiTable(data.bukti ?? []);

            kirimModal.classList.remove('hidden');
            kirimModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeKirimModal() {
            kirimModal.classList.add('hidden');
            kirimModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        kirimModal?.addEventListener('click', (e) => {
            if (e.target === kirimModal) closeKirimModal();
        });

        btnLihatPengiriman.addEventListener('click', () => {
            if (!currentOrder?.kirim) return;
            openKirimModal(currentOrder.kirim);
        });

        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Escape') return;

            if (kirimModal && !kirimModal.classList.contains('hidden')) {
                closeKirimModal();
                return;
            }
            if (viewModal && !viewModal.classList.contains('hidden')) {
                closeViewModal();
                return;
            }
        });
    </script>
@endsection
