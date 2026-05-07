@extends('admin.layout.master')

@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pengadaan', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    @php
        // DUMMY DATA (nanti ganti dari DB)
        $orders = [
            [
                'order_no' => 'PSN0001',
                'date' => '30/12/2025',
                'name' => 'Bambang Pratama Putra Hadi',
                'provinsi' => 'Kalimantan Barat',
                'status' => 'Menunggu',
                'alasan_penolakan' => '',
                'items' => [
                    ['CA001', 'Kalsium', '200 Kg', '150 Kg'],
                    ['K001', 'Kalium', '200 Kg', '150 Kg'],
                    ['CL001', 'Klorida', '200 Kg', '150 Kg'],
                    ['MG001', 'Magnesium', '200 Kg', '150 Kg'],
                ],
            ],
            [
                'order_no' => 'PSN0002',
                'date' => '30/11/2025',
                'name' => 'Bambang Pratama Putra Hadi',
                'provinsi' => 'Riau',
                'status' => 'Ditolak',
                'alasan_penolakan' => 'Barang masih tersedia.',
                'items' => [['CA001', 'Kalsium', '200 Kg', '150 Kg'], ['K001', 'Kalium', '200 Kg', '150 Kg']],
            ],
            [
                'order_no' => 'PSN0003',
                'date' => '30/11/2025',
                'name' => 'Bambang Pratama Putra Hadi',
                'provinsi' => 'Sumatera Barat',
                'status' => 'Disetujui',
                'alasan_penolakan' => '',
                'items' => [['CL001', 'Klorida', '200 Kg', '150 Kg'], ['MG001', 'Magnesium', '200 Kg', '150 Kg']],
            ],
        ];

        $statusBadge = function ($status) {
            return match ($status) {
                'Disetujui' => 'bg-green-100 text-green-800 border-green-300',
                'Ditolak' => 'bg-red-100 text-red-800 border-red-300',
                default => 'bg-gray-100 text-gray-700 border-gray-300',
            };
        };
    @endphp

    <section class="mb-5">
        {{-- breadcrumb --}}
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Pengadaan Barang</a>
        </div>
    </section>

    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        {{-- top bar: search + actions --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between mb-3">
            <div class="w-full lg:max-w-[560px]">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text"
                        class="block w-full rounded-lg border border-gray-400 bg-gray-100 pl-10 pr-3 py-2.5 text-sm text-gray-900 focus:border-gray-400 focus:ring-0"
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

                <a href="{{ route('admin.add-gudang-pengadaan-barang') }}"
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
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Tanggal Pemesanan</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Nama Pemesan</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Provinsi</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Aksi</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Status</th>
                        </tr>
                    </thead>

                    <tbody class="bg-gray-200 divide-y divide-gray-500">
                        @foreach ($orders as $idx => $o)
                            <tr class="hover:bg-gray-300">
                                <td class="px-6 py-4 text-left font-semibold">{{ $o['date'] }}</td>
                                <td class="px-6 py-4 text-left font-semibold">{{ $o['name'] }}</td>
                                <td class="px-6 py-4 text-left font-semibold">{{ $o['provinsi'] }}</td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-start gap-6 font-semibold">
                                        <button type="button" onclick='openViewModal(@json($o))'
                                            class="text-[#2D2ACD] hover:underline cursor-pointer">
                                            Lihat
                                        </button>

                                        {{-- Gudang hanya boleh hapus kalau masih Menunggu --}}
                                        @if ($o['status'] === 'Menunggu')
                                            <a href="#" class="text-[#EC0000] hover:underline">Hapus</a>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-left font-semibold">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $statusBadge($o['status']) }}">
                                        {{ $o['status'] }}
                                    </span>
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

    {{-- VIEW MODAL (READ ONLY - RESPONSIVE) --}}
    <div id="viewModal"
        class="fixed inset-0 z-[9999] hidden bg-black/50 backdrop-blur-sm
               items-start sm:items-center justify-center p-2 sm:p-4">
        <div
            class="bg-white rounded-xl shadow-xl w-full max-w-4xl mx-auto animate-scale-in overflow-hidden
                   max-h-[calc(100dvh-16px)] sm:max-h-[calc(100dvh-32px)] flex flex-col">
            {{-- header --}}
            <div
                class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 flex items-center justify-between
                       sticky top-0 bg-white z-10">
                <div class="flex flex-col sm:flex-row sm:items-center sm:gap-3 gap-2">
                    <h3 class="text-sm sm:text-base font-bold text-gray-800">
                        Pesanan Nomor: <span id="vm_order_no" class="font-extrabold">-</span>
                    </h3>
                    <span id="vm_status_badge"
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border bg-gray-100 text-gray-700 border-gray-300">
                        Menunggu
                    </span>
                </div>

                {{-- close: selalu keliatan + enak dipencet --}}
                <button type="button" onclick="closeViewModal()"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full
                           text-gray-700 hover:bg-gray-100 active:bg-gray-200">
                    <span class="text-2xl leading-none">×</span>
                </button>
            </div>

            {{-- body (SCROLL DI SINI) --}}
            <div class="px-4 sm:px-6 py-4 overflow-y-auto overscroll-contain">
                {{-- hint for warehouse --}}
                <div id="vm_hint"
                    class="mb-4 rounded-lg border border-gray-300 bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-700">
                    Status pesanan masih <span class="font-extrabold">Menunggu</span> persetujuan manajer.
                </div>

                {{-- top fields --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Nama Pemesan</label>
                        <input id="vm_name" value="" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Provinsi</label>
                        <input id="vm_provinsi" value="" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Pemesanan</label>
                        <input id="vm_date" value="" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900" />
                    </div>
                </div>

                {{-- items container --}}
                <div class="rounded-lg border border-gray-300 bg-gray-50 p-3 sm:p-4">
                    {{-- header row (md+) --}}
                    <div class="hidden md:grid grid-cols-4 gap-4 text-xs font-bold text-gray-700 mb-3">
                        <div>ID Barang</div>
                        <div>Nama Barang</div>
                        <div>Stok Tersedia</div>
                        <div>Jumlah Pesanan</div>
                    </div>

                    {{-- rows --}}
                    <div id="vm_items" class="space-y-3">
                        {{-- injected by JS --}}
                    </div>
                </div>

                {{-- ✅ BLOK ALASAN PENOLAKAN (MUNCUL HANYA JIKA DITOLAK) --}}
                <div id="vm_reject_wrap" class="hidden mb-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 mt-6">
                    <div class="text-sm font-semibold text-red-800">
                        Catatan
                    </div>
                    <div class="mt-2">
                        <textarea id="vm_reject_note" rows="3" readonly
                            class="w-full rounded-md border border-red-300 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900"></textarea>
                    </div>
                </div>

                {{-- actions --}}
                <div class="flex items-center justify-end gap-3 pt-5">
                    <button type="button" onclick="closeViewModal()"
                        class="inline-flex items-center justify-center rounded-lg bg-red-600 px-6 sm:px-8 py-2.5 text-sm font-bold text-white hover:bg-red-700">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* kalau ada animate-scale-in belum ada, ini biar ga error (optional) */
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
        const viewModal = document.getElementById('viewModal');

        const vmOrderNo = document.getElementById('vm_order_no');
        const vmStatusBadge = document.getElementById('vm_status_badge');
        const vmHint = document.getElementById('vm_hint');
        const vmName = document.getElementById('vm_name');
        const vmProvinsi = document.getElementById('vm_provinsi');
        const vmDate = document.getElementById('vm_date');
        const vmItems = document.getElementById('vm_items');

        // ✅ alasan penolakan
        const vmRejectWrap = document.getElementById('vm_reject_wrap');
        const vmRejectNote = document.getElementById('vm_reject_note');

        function badgeClass(status) {
            if (status === 'Disetujui') return 'bg-green-100 text-green-800 border-green-300';
            if (status === 'Ditolak') return 'bg-red-100 text-red-800 border-red-300';
            return 'bg-gray-100 text-gray-700 border-gray-300';
        }

        function hintHTML(status) {
            if (status === 'Disetujui') {
                return `Pesanan sudah <span class="font-extrabold text-green-800">Disetujui</span> oleh manajer Thahirudin.`;
            }
            if (status === 'Ditolak') {
                return `Pesanan <span class="font-extrabold text-red-800">Ditolak</span> oleh manajer Thahirudin.`;
            }
            return `Status pesanan masih <span class="font-extrabold">Menunggu</span> persetujuan manajer.`;
        }

        function renderItems(items) {
            vmItems.innerHTML = '';
            items.forEach((v) => {
                const row = document.createElement('div');
                row.className =
                    'md:grid md:grid-cols-4 md:gap-4 md:items-center bg-white rounded-lg border border-gray-300 p-3';

                row.innerHTML = `
                    <div class="grid grid-cols-2 gap-2 md:block">
                        <div class="md:hidden text-[11px] font-bold text-gray-600">ID Barang</div>
                        <input value="${v[0]}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2 text-sm font-semibold" />
                    </div>

                    <div class="grid grid-cols-2 gap-2 md:block mt-2 md:mt-0">
                        <div class="md:hidden text-[11px] font-bold text-gray-600">Nama Barang</div>
                        <input value="${v[1]}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2 text-sm font-semibold" />
                    </div>

                    <div class="grid grid-cols-2 gap-2 md:block mt-2 md:mt-0">
                        <div class="md:hidden text-[11px] font-bold text-gray-600">Stok Tersedia</div>
                        <input value="${v[2]}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2 text-sm font-semibold" />
                    </div>

                    <div class="grid grid-cols-2 gap-2 md:block mt-2 md:mt-0">
                        <div class="md:hidden text-[11px] font-bold text-gray-600">Jumlah Pesanan</div>
                        <input value="${v[3]}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2 text-sm font-semibold" />
                    </div>
                `;
                vmItems.appendChild(row);
            });
        }

        function openViewModal(order) {
            vmOrderNo.textContent = order.order_no ?? '-';
            vmName.value = order.name ?? '';
            vmProvinsi.value = order.provinsi ?? '';
            vmDate.value = order.date ?? '';

            const status = order.status ?? 'Menunggu';
            vmStatusBadge.textContent = status;
            vmStatusBadge.className =
                `inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border ${badgeClass(status)}`;

            vmHint.innerHTML = hintHTML(status);
            renderItems(order.items ?? []);

            // ✅ tampilkan alasan penolakan hanya kalau Ditolak
            if (status === 'Ditolak') {
                vmRejectWrap.classList.remove('hidden');
                vmRejectNote.value = order.alasan_penolakan ?? '';
            } else {
                vmRejectWrap.classList.add('hidden');
                vmRejectNote.value = '';
            }

            viewModal.classList.remove('hidden');
            viewModal.classList.add('flex');

            // lock body, tapi modal tetep bisa scroll (scroll-nya ada di body modal)
            document.body.classList.add('overflow-hidden');
        }

        function closeViewModal() {
            viewModal.classList.add('hidden');
            viewModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        // close modal when click backdrop
        viewModal?.addEventListener('click', (e) => {
            if (e.target === viewModal) closeViewModal();
        });

        // close modal with ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !viewModal.classList.contains('hidden')) closeViewModal();
        });
    </script>
@endsection
