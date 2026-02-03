@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-laporan-produksi', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    @php
        // ===== DUMMY LIST (TABLE) =====
        $rows = [
            ['30/11/2025', 'BHOS0001', 'BHOS Ekstra', 'Bambang Pratama Putra Hadi'],
            ['30/11/2025', 'BHOS0002', 'BHOS Turbo', 'Bambang Pratama Putra Hadi'],
            ['30/11/2025', 'BHOS0003', 'BHOS Ekstra', 'Bambang Pratama Putra Hadi'],
            ['30/11/2025', 'BHOS0004', 'BHOS Ekstra', 'Bambang Pratama Putra Hadi'],
            ['30/11/2025', 'BHOS0005', 'BHOS Turbo', 'Bambang Pratama Putra Hadi'],
        ];

        // helper SKU (dummy)
        $skuOf = function ($jenis) {
            return match ($jenis) {
                'BHOS Ekstra' => 'BHOSEK1000',
                'BHOS Turbo' => 'BHOSTB1000',
                default => '-',
            };
        };

        // ===== DUMMY DETAIL UNTUK MODAL "LIHAT" =====
        // key = id_produksi (kolom ID Barang di table)
        $details = [
            'BHOS0001' => [
                'laporan_no' => 'BRMS0001',
                'pj' => 'Bambang Pratama Putra Hadi',
                'tanggal_masuk' => '30/11/2025',
                'id_produksi' => 'BHOS0001',
                'sku' => 'BHOSEK1000',
                'nama_produk' => 'BHOS Ekstra',
                'jumlah_produksi' => '150 Ltr',
                'items' => [
                    ['CA001', 'Kalsium', '200 Kg', '150 Kg'],
                    ['K001', 'Kalium', '200 Kg', '150 Kg'],
                    ['CL001', 'Klorida', '200 Kg', '150 Kg'],
                    ['MG001', 'Magnesium', '200 Kg', '150 Kg'],
                ],
            ],
            'BHOS0002' => [
                'laporan_no' => 'BRMS0002',
                'pj' => 'Bambang Pratama Putra Hadi',
                'tanggal_masuk' => '30/11/2025',
                'id_produksi' => 'BHOS0002',
                'sku' => 'BHOSTB1000',
                'nama_produk' => 'BHOS Turbo',
                'jumlah_produksi' => '150 Kg',
                'items' => [
                    ['CA001', 'Kalsium', '200 Kg', '150 Kg'],
                    ['K001', 'Kalium', '200 Kg', '150 Kg'],
                ],
            ],
            'BHOS0003' => [
                'laporan_no' => 'BRMS0003',
                'pj' => 'Bambang Pratama Putra Hadi',
                'tanggal_masuk' => '30/11/2025',
                'id_produksi' => 'BHOS0003',
                'sku' => 'BHOSEK1000',
                'nama_produk' => 'BHOS Ekstra',
                'jumlah_produksi' => '150 Ltr',
                'items' => [
                    ['CL001', 'Klorida', '200 Kg', '150 Kg'],
                    ['MG001', 'Magnesium', '200 Kg', '150 Kg'],
                ],
            ],
            'BHOS0004' => [
                'laporan_no' => 'BRMS0004',
                'pj' => 'Bambang Pratama Putra Hadi',
                'tanggal_masuk' => '30/11/2025',
                'id_produksi' => 'BHOS0004',
                'sku' => 'BHOSEK1000',
                'nama_produk' => 'BHOS Ekstra',
                'jumlah_produksi' => '150 Ltr',
                'items' => [
                    ['CA001', 'Kalsium', '200 Kg', '150 Kg'],
                    ['CL001', 'Klorida', '200 Kg', '150 Kg'],
                ],
            ],
            'BHOS0005' => [
                'laporan_no' => 'BRMS0005',
                'pj' => 'Bambang Pratama Putra Hadi',
                'tanggal_masuk' => '30/11/2025',
                'id_produksi' => 'BHOS0005',
                'sku' => 'BHOSTB1000',
                'nama_produk' => 'BHOS Turbo',
                'jumlah_produksi' => '150 Kg',
                'items' => [
                    ['K001', 'Kalium', '200 Kg', '150 Kg'],
                    ['MG001', 'Magnesium', '200 Kg', '150 Kg'],
                ],
            ],
        ];
    @endphp

    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Produksi</a>
        </div>
    </section>

    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        {{-- top bar --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between mb-3">
            {{-- search --}}
            <div class="w-full lg:max-w-[520px]">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
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

            {{-- actions --}}
            <div class="flex items-center gap-2 justify-end">
                <a href="#"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2E7E3F] px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-300">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v6h6M20 20v-6h-6M20 8a8 0 00-14.9-3M4 16a8 8 0 0014.9 3" />
                    </svg>
                    Export xlxs.
                </a>

                <a href="{{ route('admin.add-pilih-produk') }}"
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
                    <thead class="bg-[#92d7a1] text-gray-900">
                        <tr class="[&>th]:border-b [&>th]:border-gray-600">
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Tanggal Produksi</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">ID Barang</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">SKU</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Jenis Barang</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Penanggung Jawab</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-left">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="bg-gray-200 divide-y divide-gray-600">
                        @foreach ($rows as $r)
                            @php $sku = $skuOf($r[2]); @endphp
                            <tr class="hover:bg-gray-300">
                                <td class="px-6 py-4 font-semibold">{{ $r[0] }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $r[1] }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $sku }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $r[2] }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $r[3] }}</td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-start gap-6 font-semibold">
                                        <a href="{{ route('admin.edit-produk') }}"
                                            class="text-[#2E7E3F] hover:underline">Sunting</a>

                                        {{-- ✅ LIHAT: buka modal --}}
                                        <button type="button"
                                            onclick='openLaporanModal(@json($details[$r[1]] ?? null))'
                                            class="text-[#2D2ACD] hover:underline">
                                            Lihat
                                        </button>

                                        <a href="#" class="text-[#EC0000] hover:underline">Hapus</a>
                                    </div>
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

                <div class="text-xs sm:text-sm font-semibold text-gray-900">
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

    {{-- ========================= --}}
    {{-- MODAL LIHAT (READ ONLY) --}}
    {{-- ========================= --}}
    <div id="laporanModal"
        class="fixed inset-0 z-[9999] hidden bg-black/50 backdrop-blur-sm items-center justify-center p-2 sm:p-4">
        <div
            class="bg-[#f3f3f3] rounded-xl shadow-2xl w-full max-w-5xl mx-auto overflow-hidden animate-scale-in
                   max-h-[calc(100dvh-16px)] sm:max-h-[calc(100dvh-32px)] flex flex-col border border-gray-300">

            {{-- header --}}
            <div class="px-6 py-4 border-b border-gray-300 bg-[#f3f3f3] flex items-center justify-between">
                <div class="text-sm font-bold text-gray-800">
                    Laporan Produksi Nomor: <span id="lm_no" class="font-extrabold">-</span>
                </div>
                <button type="button" onclick="closeLaporanModal()"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-full text-gray-700 hover:bg-gray-200">
                    <span class="text-2xl leading-none">×</span>
                </button>
            </div>

            {{-- body scroll --}}
            <div class="p-6 overflow-y-auto">
                {{-- top fields --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Nama Penanggung Jawab</label>
                        <input id="lm_pj" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold text-gray-900">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Tanggal Masuk</label>
                        <input id="lm_tanggal" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold text-gray-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">ID Produksi</label>
                        <input id="lm_id_produksi" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold text-gray-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Stock Keeping Unit</label>
                        <input id="lm_sku" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold text-gray-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Nama Produk</label>
                        <input id="lm_produk" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold text-gray-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Jumlah Produksi</label>
                        <input id="lm_jumlah" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold text-gray-900">
                    </div>
                </div>

                {{-- item list --}}
                <div class="mt-5">
                    <div class="grid grid-cols-4 gap-4 text-xs font-bold text-gray-700 mb-2">
                        <div>ID Barang</div>
                        <div>Nama Barang</div>
                        <div>Stok Tersedia</div>
                        <div>Input Barang Masuk</div>
                    </div>

                    <div id="lm_items" class="space-y-2">
                        {{-- injected --}}
                    </div>
                </div>

                {{-- footer button --}}
                <div class="flex justify-end pt-6">
                    <button type="button" onclick="closeLaporanModal()"
                        class="inline-flex items-center justify-center rounded-lg bg-red-600 px-10 py-3 text-sm font-bold text-white hover:bg-red-700">
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
        const laporanModal = document.getElementById('laporanModal');

        const lmNo = document.getElementById('lm_no');
        const lmPj = document.getElementById('lm_pj');
        const lmTanggal = document.getElementById('lm_tanggal');
        const lmIdProduksi = document.getElementById('lm_id_produksi');
        const lmSku = document.getElementById('lm_sku');
        const lmProduk = document.getElementById('lm_produk');
        const lmJumlah = document.getElementById('lm_jumlah');
        const lmItems = document.getElementById('lm_items');

        function renderLaporanItems(items) {
            lmItems.innerHTML = '';
            (items || []).forEach((v) => {
                const row = document.createElement('div');
                row.className = 'grid grid-cols-1 md:grid-cols-4 gap-3 md:gap-4';

                row.innerHTML = `
                    <input value="${v[0] ?? ''}" readonly
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold text-gray-900" />
                    <input value="${v[1] ?? ''}" readonly
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold text-gray-900" />
                    <input value="${v[2] ?? ''}" readonly
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold text-gray-900" />
                    <input value="${v[3] ?? ''}" readonly
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold text-gray-900" />
                `;
                lmItems.appendChild(row);
            });
        }

        function openLaporanModal(data) {
            if (!data) return;

            lmNo.textContent = data.laporan_no ?? '-';
            lmPj.value = data.pj ?? '';
            lmTanggal.value = data.tanggal_masuk ?? '';
            lmIdProduksi.value = data.id_produksi ?? '';
            lmSku.value = data.sku ?? '';
            lmProduk.value = data.nama_produk ?? '';
            lmJumlah.value = data.jumlah_produksi ?? '';

            renderLaporanItems(data.items ?? []);

            laporanModal.classList.remove('hidden');
            laporanModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeLaporanModal() {
            laporanModal.classList.add('hidden');
            laporanModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        // close on backdrop click
        laporanModal?.addEventListener('click', (e) => {
            if (e.target === laporanModal) closeLaporanModal();
        });

        // close on ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && laporanModal && !laporanModal.classList.contains('hidden')) {
                closeLaporanModal();
            }
        });
    </script>
@endsection
