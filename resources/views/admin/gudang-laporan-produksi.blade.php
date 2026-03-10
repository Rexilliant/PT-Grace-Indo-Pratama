@extends('admin.layout.master')

@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-laporan-produksi', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    <section class="mb-5">
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Produksi</a>
        </div>
    </section>

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-300 bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

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

                <a href="{{ route('admin.add-pilih-produk') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2D2ACD] px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <span class="text-lg leading-none">+</span>
                    Tambah Baru
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-900">
                    <thead class="bg-[#92d7a1] text-gray-900">
                        <tr class="[&>th]:border-b [&>th]:border-gray-600">
                            <th class="px-6 py-4 font-extrabold text-left">Tanggal Produksi</th>
                            <th class="px-6 py-4 font-extrabold text-left">ID Barang</th>
                            <th class="px-6 py-4 font-extrabold text-left">SKU</th>
                            <th class="px-6 py-4 font-extrabold text-left">Jenis Barang</th>
                            <th class="px-6 py-4 font-extrabold text-left">Penanggung Jawab</th>
                            <th class="px-6 py-4 font-extrabold text-left">Provinsi</th>
                            <th class="px-6 py-4 font-extrabold text-left">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="bg-gray-200 divide-y divide-gray-600">
                        @forelse ($productionBatches as $batch)
                            @php
                                $variant = $batch->productStock?->productVariant;
                                $product = $variant?->product;
                                $modalData = [
                                    'laporan_no' => 'PB-' . str_pad($batch->id, 5, '0', STR_PAD_LEFT),
                                    'pj' => $batch->personResponsible?->name ?? '-',
                                    'provinsi' => $batch->province,
                                    'tanggal_masuk' => optional($batch->entry_date)->format('d/m/Y'),
                                    'id_produksi' => $product?->code ?? '-',
                                    'sku' => $variant?->sku ?? '-',
                                    'nama_produk' => $variant?->name ?? '-',
                                    'jumlah_produksi' => $batch->quantity . ' ' . ($variant?->unit ?? ''),
                                    'items' => $batch->materials
                                        ->map(function ($material) {
                                            return [
                                                $material->rawMaterial?->code ?? '-',
                                                $material->rawMaterial?->name ?? '-',
                                                $material->stock . ' ' . ($material->rawMaterial?->unit ?? ''),
                                                $material->quantity_use . ' ' . ($material->rawMaterial?->unit ?? ''),
                                            ];
                                        })
                                        ->values()
                                        ->toArray(),
                                ];
                            @endphp

                            <tr class="hover:bg-gray-300">
                                <td class="px-6 py-4 font-semibold">
                                    {{ optional($batch->entry_date)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 font-semibold">
                                    {{ $product?->code ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-semibold">
                                    {{ $variant?->sku ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-semibold">
                                    {{ $variant?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-semibold">
                                    {{ $batch->personResponsible?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-semibold">
                                    {{ $batch->province }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-start gap-6 font-semibold">
                                        <a href="{{ route('admin.edit-produk', $batch->id) }}"
                                            class="text-[#2E7E3F] hover:underline">
                                            Sunting
                                        </a>

                                        <button type="button" onclick='openLaporanModal(@json($modalData))'
                                            class="text-[#2D2ACD] hover:underline">
                                            Lihat
                                        </button>

                                        <form action="{{ route('admin.production.delete', $batch->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus data produksi ini?')"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-[#EC0000] hover:underline">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-6 text-center text-gray-600 font-semibold">
                                    Data produksi belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $productionBatches->links('vendor.pagination.pagination') }}
        </div>
    </section>



    <div id="laporanModal"
        class="fixed inset-0 z-[9999] hidden bg-black/50 backdrop-blur-sm items-center justify-center p-2 sm:p-4">
        <div
            class="bg-[#f3f3f3] rounded-xl shadow-2xl w-full max-w-5xl mx-auto overflow-hidden animate-scale-in
                   max-h-[calc(100dvh-16px)] sm:max-h-[calc(100dvh-32px)] flex flex-col border border-gray-300">

            <div
                class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-300 bg-[#f3f3f3] flex items-center justify-between sticky top-0 z-10">
                <div class="text-sm sm:text-base font-bold text-gray-800">
                    Laporan Produksi Nomor: <span id="lm_no" class="font-extrabold">-</span>
                </div>
                <button type="button" onclick="closeLaporanModal()"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full text-gray-700 hover:bg-gray-200">
                    <span class="text-2xl leading-none">×</span>
                </button>
            </div>

            <div class="px-4 sm:px-6 py-4 overflow-y-auto overscroll-contain">
                <div class="grid grid-cols-1 mb-5 md:grid-cols-3 gap-4 sm:gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Nama Penanggung Jawab</label>
                        <input id="lm_pj" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Provinsi</label>
                        <input id="lm_provinsi" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Masuk</label>
                        <input id="lm_tanggal" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                    </div>
                </div>

                <div class="rounded-lg border border-gray-300 bg-gray-50 p-3 sm:p-4">
                    <div class="hidden md:grid grid-cols-4 gap-4 text-xs font-bold text-gray-700 mb-3">
                        <div>ID Produksi</div>
                        <div>Stock Keeping Unit</div>
                        <div>Nama Produk</div>
                        <div>Jumlah Produksi</div>
                    </div>
                    <div id="lm_produk_row" class="space-y-3"></div>
                </div>

                <div class="mt-5 rounded-lg border border-gray-300 bg-gray-50 p-3 sm:p-4">
                    <div class="hidden md:grid grid-cols-4 gap-4 text-xs font-bold text-gray-700 mb-3">
                        <div>ID Barang</div>
                        <div>Nama Barang</div>
                        <div>Stok Saat Dipakai</div>
                        <div>Jumlah Stok Digunakan</div>
                    </div>
                    <div id="lm_items" class="space-y-3"></div>
                </div>

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
        const lmProvinsi = document.getElementById('lm_provinsi');
        const lmTanggal = document.getElementById('lm_tanggal');
        const lmProdukRow = document.getElementById('lm_produk_row');
        const lmItems = document.getElementById('lm_items');

        function esc(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderProdukUtamaRow(data) {
            lmProdukRow.innerHTML = `
                <div class="md:hidden rounded-xl border border-gray-300 bg-white p-4 space-y-3">
                    <div>
                        <div class="text-[11px] font-bold text-gray-600 mb-1">ID Produksi</div>
                        <input value="${esc(data?.id_produksi)}" readonly class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-gray-600 mb-1">Stock Keeping Unit</div>
                        <input value="${esc(data?.sku)}" readonly class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-gray-600 mb-1">Nama Produk</div>
                        <input value="${esc(data?.nama_produk)}" readonly class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-gray-600 mb-1">Jumlah Produksi</div>
                        <input value="${esc(data?.jumlah_produksi)}" readonly class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                    </div>
                </div>

                <div class="hidden md:grid grid-cols-4 gap-3">
                    <input value="${esc(data?.id_produksi)}" readonly class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                    <input value="${esc(data?.sku)}" readonly class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                    <input value="${esc(data?.nama_produk)}" readonly class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                    <input value="${esc(data?.jumlah_produksi)}" readonly class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                </div>
            `;
        }

        function renderLaporanItems(items) {
            lmItems.innerHTML = '';

            (items || []).forEach((v) => {
                const wrap = document.createElement('div');
                wrap.innerHTML = `
                    <div class="md:hidden rounded-xl border border-gray-300 bg-white p-4 space-y-3">
                        <div>
                            <div class="text-[11px] font-bold text-gray-600 mb-1">ID Barang</div>
                            <input value="${esc(v[0])}" readonly class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                        </div>
                        <div>
                            <div class="text-[11px] font-bold text-gray-600 mb-1">Nama Barang</div>
                            <input value="${esc(v[1])}" readonly class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                        </div>
                        <div>
                            <div class="text-[11px] font-bold text-gray-600 mb-1">Stok Saat Dipakai</div>
                            <input value="${esc(v[2])}" readonly class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                        </div>
                        <div>
                            <div class="text-[11px] font-bold text-gray-600 mb-1">Jumlah Stok Digunakan</div>
                            <input value="${esc(v[3])}" readonly class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                        </div>
                    </div>

                    <div class="hidden md:grid grid-cols-4 gap-3">
                        <input value="${esc(v[0])}" readonly class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                        <input value="${esc(v[1])}" readonly class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                        <input value="${esc(v[2])}" readonly class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                        <input value="${esc(v[3])}" readonly class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                    </div>
                `;
                lmItems.appendChild(wrap);
            });
        }

        function openLaporanModal(data) {
            if (!data) return;

            lmNo.textContent = data.laporan_no ?? '-';
            lmPj.value = data.pj ?? '';
            lmProvinsi.value = data.provinsi ?? '';
            lmTanggal.value = data.tanggal_masuk ?? '';

            renderProdukUtamaRow(data);
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

        laporanModal?.addEventListener('click', (e) => {
            if (e.target === laporanModal) closeLaporanModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && laporanModal && !laporanModal.classList.contains('hidden')) {
                closeLaporanModal();
            }
        });
    </script>
@endsection
