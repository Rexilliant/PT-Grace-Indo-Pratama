@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-barang-masuk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    @php
        // dummy data untuk modal "Lihat"
        $detail = (object) [
            'pesanan_no' => 'PSN0001',
            'tanggal' => '30/11/2025',
            'nama_pemesan' => 'Bambang Pratama Putra Hadi',
            'provinsi' => 'Riau',
            'total_pesanan' => 'Rp6.000.000',
            // dummy invoice image (nanti ganti ke storage/url kamu)
            'invoice_img' => asset('build/image/bhos-logo.png'),
            'status' => 'Selesai',
        ];

        $detailItems = [
            ['CA001', 'Kalsium', '200 Kg', '150 Kg'],
            ['KO01', 'Kalium', '200 Kg', '150 Kg'],
            ['CL001', 'Klorida', '200 Kg', '150 Kg'],
            ['MG001', 'Magnesium', '200 Kg', '150 Kg'],
        ];
    @endphp

    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Barang Masuk</a>
        </div>
    </section>

    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        {{-- top bar --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between mb-3">
            <div class="w-full lg:max-w-[560px]">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 0 0114 0z" />
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
                            d="M4 4v6h6M20 20v-6h-6M20 8a8 0 00-14.9-3M4 16a8 8 0 0014.9 3" />
                    </svg>
                    Export .xlsx
                </a>

                <a href="{{ route('admin.add-barang-masuk') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2D2ACD] px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <span class="text-lg leading-none">+</span>
                    Tambah Baru
                </a>
            </div>
        </div>

        {{-- table --}}
        <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-800">
                    <thead class="bg-[#5aba6f]/70 text-gray-900">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Tanggal Masuk</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Nama Penerima</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Provinsi</th>
                            {{-- ✅ tambah kolom --}}
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="bg-gray-200 divide-y divide-gray-500">
                        @for ($i = 0; $i < 5; $i++)
                            <tr class="hover:bg-gray-300">
                                <td class="px-6 py-4 font-semibold">30/11/2025</td>
                                <td class="px-6 py-4 font-semibold">Bambang Pratama Putra Hadi</td>
                                <td class="px-6 py-4 font-semibold">{{ $detail->provinsi }}</td> {{-- ✅ isi provinsi --}}
                                <td class="px-6 py-3">
                                    <div class="flex items-center justify-start gap-6 font-semibold">
                                        <a href="{{ route('admin.edit-barang-masuk') }}"
                                            class="text-[#2E7E3F] hover:underline">Sunting</a>

                                        <button type="button" onclick="openDetailModal()"
                                            class="text-[#2D2ACD] hover:underline  cursor-pointer">
                                            Lihat
                                        </button>

                                        <a href="#" class="text-[#EC0000] hover:underline">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            {{-- footer / pagination --}}
            <div
                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between bg-gray-200 px-3 sm:px-4 md:px-5 py-3 sm:py-4 border-t border-gray-400">

                <div class="text-xs sm:text-sm font-semibold text-gray-700">
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

    {{-- VIEW MODAL (RESPONSIVE TABLE STYLE) --}}
    <div id="detailModal"
        class="fixed inset-0 z-[9999] hidden bg-black/50 backdrop-blur-sm
           items-center justify-center p-2 sm:p-4">
        <div
            class="bg-white rounded-xl shadow-xl w-full max-w-4xl mx-auto animate-scale-in overflow-hidden
                   max-h-[calc(100dvh-16px)] sm:max-h-[calc(100dvh-32px)] flex flex-col">

            {{-- header --}}
            <div
                class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 flex items-center justify-between
                       sticky top-0 bg-white z-10">
                <div class="flex flex-col sm:flex-row sm:items-center sm:gap-3 gap-2">
                    <h3 class="text-sm sm:text-base font-bold text-gray-800">
                        Pesanan Nomor: <span class="font-extrabold">{{ $detail->pesanan_no }}</span>
                    </h3>

                    <span id="dm_status_badge"
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border bg-green-100 text-green-800 border-green-300">
                        {{ $detail->status }}
                    </span>
                </div>

                <button type="button" onclick="closeDetailModal()"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full
                           text-gray-700 hover:bg-gray-100 active:bg-gray-200">
                    <span class="text-2xl leading-none">×</span>
                </button>
            </div>

            {{-- body (scroll di sini) --}}
            <div class="px-4 sm:px-6 py-4 overflow-y-auto overscroll-contain">
                <div id="dm_hint"
                    class="mb-4 rounded-lg border border-gray-300 bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-700">
                    Detail barang masuk.
                </div>

                {{-- top fields --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Pemesanan</label>
                        <input value="{{ $detail->tanggal }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Nama Pemesan</label>
                        <input value="{{ $detail->nama_pemesan }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Provinsi</label>
                        <input value="{{ $detail->provinsi }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Total Pesanan</label>
                        <input value="{{ $detail->total_pesanan }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900" />
                    </div>

                </div>

                {{-- items container (sama kayak template pengadaan) --}}
                <div class="rounded-lg border border-gray-300 bg-gray-50 p-3 sm:p-4">
                    {{-- header row (md+) --}}
                    <div class="hidden md:grid grid-cols-4 gap-4 text-xs font-bold text-gray-700 mb-3">
                        <div>ID Barang</div>
                        <div>Nama Barang</div>
                        <div>Stok Tersedia</div>
                        <div>Input Jumlah Pesanan</div>
                    </div>

                    {{-- rows --}}
                    <div class="space-y-3">
                        @foreach ($detailItems as $row)
                            <div
                                class="md:grid md:grid-cols-4 md:gap-4 md:items-center bg-white rounded-lg border border-gray-300 p-3">
                                <div class="grid grid-cols-2 gap-2 md:block">
                                    <div class="md:hidden text-[11px] font-bold text-gray-600">ID Barang</div>
                                    <input value="{{ $row[0] }}" readonly
                                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2 text-sm font-semibold" />
                                </div>

                                <div class="grid grid-cols-2 gap-2 md:block mt-2 md:mt-0">
                                    <div class="md:hidden text-[11px] font-bold text-gray-600">Nama Barang</div>
                                    <input value="{{ $row[1] }}" readonly
                                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2 text-sm font-semibold" />
                                </div>

                                <div class="grid grid-cols-2 gap-2 md:block mt-2 md:mt-0">
                                    <div class="md:hidden text-[11px] font-bold text-gray-600">Stok Tersedia</div>
                                    <input value="{{ $row[2] }}" readonly
                                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2 text-sm font-semibold" />
                                </div>

                                <div class="grid grid-cols-2 gap-2 md:block mt-2 md:mt-0">
                                    <div class="md:hidden text-[11px] font-bold text-gray-600">Input Jumlah Pesanan</div>
                                    <input value="{{ $row[3] }}" readonly
                                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2 text-sm font-semibold" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- actions --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-5">
                    <button type="button" onclick="openInvoiceModal()"
                        class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-6 sm:px-8 py-2.5 text-sm font-bold text-white hover:bg-blue-800">
                        Invoice
                    </button>

                    <button type="button" onclick="closeDetailModal()"
                        class="inline-flex items-center justify-center rounded-lg bg-red-600 px-6 sm:px-8 py-2.5 text-sm font-bold text-white hover:bg-red-700">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- INVOICE MODAL (DI ATAS VIEW MODAL) --}}
    <div id="invoiceModal"
        class="fixed inset-0 z-[10000] hidden bg-black/60 backdrop-blur-sm
           items-center justify-center p-2 sm:p-4">

        <div
            class="bg-white rounded-xl shadow-xl w-full max-w-5xl mx-auto overflow-hidden
                   max-h-[calc(100dvh-16px)] sm:max-h-[calc(100dvh-32px)] flex flex-col">

            <div
                class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 flex items-center justify-between
                       sticky top-0 bg-white z-10">
                <div class="text-sm sm:text-base font-bold text-gray-800">Invoice</div>

                <button type="button" onclick="closeInvoiceModal()"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full
                           text-gray-700 hover:bg-gray-100 active:bg-gray-200">
                    <span class="text-2xl leading-none">×</span>
                </button>
            </div>

            <div class="p-3 sm:p-4 bg-gray-100 overflow-y-auto">
                <img src="{{ asset('build/image/bhos-logo.png') }}" alt="Invoice"
                    class="w-full max-h-[70dvh] object-contain rounded-lg border border-gray-300 bg-white" />
            </div>
        </div>
    </div>

    <style>
        @keyframes scaleIn {
            from {
                transform: scale(.98);
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
        const detailModal = document.getElementById('detailModal');
        const invoiceModal = document.getElementById('invoiceModal');

        function openDetailModal() {
            detailModal.classList.remove('hidden');
            detailModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeDetailModal() {
            if (!invoiceModal.classList.contains('hidden')) closeInvoiceModal();

            detailModal.classList.add('hidden');
            detailModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        function openInvoiceModal() {
            invoiceModal.classList.remove('hidden');
            invoiceModal.classList.add('flex');
        }

        function closeInvoiceModal() {
            invoiceModal.classList.add('hidden');
            invoiceModal.classList.remove('flex');
        }

        // close modal when click backdrop
        detailModal?.addEventListener('click', (e) => {
            if (e.target === detailModal) closeDetailModal();
        });

        invoiceModal?.addEventListener('click', (e) => {
            if (e.target === invoiceModal) closeInvoiceModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (!invoiceModal.classList.contains('hidden')) closeInvoiceModal();
                else if (!detailModal.classList.contains('hidden')) closeDetailModal();
            }
        });
    </script>
@endsection
