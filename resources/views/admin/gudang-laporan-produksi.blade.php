@extends('admin.layout.master')

@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-laporan-produksi', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    @php
        $boxClass = 'rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900';
        $readonlyGray = $boxClass . ' bg-gray-100 cursor-not-allowed';
        $readonlyWhite = $boxClass . ' bg-white cursor-not-allowed';
    @endphp

    <div x-data="{
        open: false,
        detail: {
            laporan_no: '-',
            pj: '',
            gudang: '',
            tanggal_masuk: '',
            id_produksi: '',
            sku: '',
            nama_produk: '',
            jumlah_produksi: '',
            items: []
        },
        showDetail(data) {
            this.detail = {
                laporan_no: data?.laporan_no ?? '-',
                pj: data?.pj ?? '',
                gudang: data?.gudang ?? '',
                tanggal_masuk: data?.tanggal_masuk ?? '',
                id_produksi: data?.id_produksi ?? '',
                sku: data?.sku ?? '',
                nama_produk: data?.nama_produk ?? '',
                jumlah_produksi: data?.jumlah_produksi ?? '',
                items: Array.isArray(data?.items) ? data.items : []
            };
            this.open = true;
            document.body.classList.add('overflow-hidden');
        },
        closeDetail() {
            this.open = false;
            document.body.classList.remove('overflow-hidden');
        }
    }" @keydown.escape.window="if (open) closeDetail()">
        <section class="mb-5">
            <div class="mb-4 text-xl font-semibold text-gray-700">
                <span>Gudang</span>
                <span class="mx-1 text-gray-400">›</span>
                <a href="#" class="text-blue-600 hover:underline">Produksi</a>
            </div>
        </section>
        <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
            {{-- top bar --}}
            <form method="GET" class="mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 items-end">

                    {{-- Search --}}
                    <div class="flex flex-col w-full">
                        <label class="text-xs font-semibold text-gray-700 mb-1">
                            ID Produksi
                        </label>
                        <input type="text" name="id" value="{{ request('id') }}" placeholder="ID Produksi"
                            class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                    </div>
                    <div class="flex flex-col w-full">
                        <label class="text-xs font-semibold text-gray-700 mb-1">
                            Warehouse
                        </label>
                        <select name="warehouse_id"
                            class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none">
                            <option value="">Semua Gudang</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" @selected(request('warehouse_id') == $warehouse->id)>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Date From --}}
                    <div class="flex flex-col w-full">
                        <label class="text-xs font-semibold text-gray-700 mb-1">
                            Tanggal Dari
                        </label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                    </div>

                    {{-- Date To --}}
                    <div class="flex flex-col w-full">
                        <label class="text-xs font-semibold text-gray-700 mb-1">
                            Sampai
                        </label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none" />
                    </div>
                    {{-- Per Page --}}
                    <div class="flex flex-col w-full">
                        <label class="text-xs font-semibold text-gray-700 mb-1">
                            Tampilkan
                        </label>
                        <select name="per_page"
                            class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#5aba6f] focus:outline-none"
                            onchange="this.form.submit()">
                            @foreach ([10, 25, 50, 100] as $n)
                                <option value="{{ $n }}" @selected((int) request('per_page', 10) === $n)>
                                    {{ $n }} / halaman
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                        class="rounded-md bg-green-600 px-5 py-2 text-sm font-semibold text-white hover:bg-green-800 transition">
                        Filter
                    </button>

                    <a href="{{ route('admin.gudang-laporan-produksi') }}"
                        class="rounded-md bg-red-600 px-5 py-2 text-sm font-semibold text-white hover:bg-red-800 transition text-center">
                        Reset
                    </a>
                </div>
            </form>
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

        <section class="mb-5 rounded-lg border border-gray-300 bg-white p-5 shadow">
            <div class="mb-3 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center justify-end gap-2">
                    <a href="#"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#2E7E3F] px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-300">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v6h6M20 20v-6h-6M20 8a8 8 0 00-14.9-3M4 16a8 8 0 0014.9 3" />
                        </svg>
                        Export .xlsx
                    </a>

                    @can('tambah produksi')
                        <a href="{{ route('admin.add-pilih-produk') }}"
                            class="inline-flex items-center gap-2 rounded-lg bg-[#2D2ACD] px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                            <span class="text-lg leading-none">+</span>
                            Tambah Baru
                        </a>
                    @endcan
                </div>
            </div>

            <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-900">
                        <thead class="bg-[#92d7a1] text-gray-900">
                            <tr class="[&>th]:border-b [&>th]:border-gray-600">
                                <th class="px-6 py-4 text-left font-extrabold">Id</th>
                                <th class="px-6 py-4 text-left font-extrabold">Tanggal Produksi</th>
                                <th class="px-6 py-4 text-left font-extrabold">ID Barang</th>
                                <th class="px-6 py-4 text-left font-extrabold">SKU</th>
                                <th class="px-6 py-4 text-left font-extrabold">Jenis Barang</th>
                                <th class="px-6 py-4 text-left font-extrabold">Penanggung Jawab</th>
                                <th class="px-6 py-4 text-left font-extrabold">Gudang</th>
                                <th class="px-6 py-4 text-left font-extrabold">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-600 bg-gray-200">
                            @forelse ($productionBatches as $batch)
                                @php
                                    $variant = $batch->productStock?->productVariant;
                                    $product = $variant?->product;
                                    $detailData = [
                                        'laporan_no' => 'PB-' . str_pad($batch->id, 5, '0', STR_PAD_LEFT),
                                        'pj' => $batch->personResponsible?->name ?? '-',
                                        'gudang' => $batch->warehouse->name ?? '-',
                                        'tanggal_masuk' => optional($batch->entry_date)->format('d/m/Y'),
                                        'id_produksi' => $product?->code ?? '-',
                                        'sku' => $variant?->sku ?? '-',
                                        'nama_produk' => $variant?->name ?? '-',
                                        'jumlah_produksi' => $batch->quantity . ' ' . ($variant?->unit ?? ''),
                                        'items' => $batch->materials
                                            ->map(
                                                fn($material) => [
                                                    'code' => $material->rawMaterial?->code ?? '-',
                                                    'name' => $material->rawMaterial?->name ?? '-',
                                                    'stock' =>
                                                        $material->stock . ' ' . ($material->rawMaterial?->unit ?? ''),
                                                    'used' =>
                                                        $material->quantity_use .
                                                        ' ' .
                                                        ($material->rawMaterial?->unit ?? ''),
                                                ],
                                            )
                                            ->values()
                                            ->toArray(),
                                    ];
                                @endphp

                                <tr class="hover:bg-gray-300">
                                    <td class="px-6 py-4 font-semibold">{{ $batch->id }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold">{{ optional($batch->entry_date)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold">{{ $product?->code ?? '-' }}</td>
                                    <td class="px-6 py-4 font-semibold">{{ $variant?->sku ?? '-' }}</td>
                                    <td class="px-6 py-4 font-semibold">{{ $variant?->name ?? '-' }}</td>
                                    <td class="px-6 py-4 font-semibold">{{ $batch->personResponsible?->name ?? '-' }}</td>
                                    <td class="px-6 py-4 font-semibold">{{ $batch->warehouse->name ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-start gap-6 font-semibold">
                                            @can('edit produksi')
                                                <a href="{{ route('admin.edit-produk', $batch->id) }}"
                                                    class="text-[#2E7E3F] hover:underline">
                                                    Sunting
                                                </a>
                                            @endcan

                                            @can('baca produksi')
                                                <button type="button" @click='showDetail(@json($detailData))'
                                                    class="text-[#2D2ACD] hover:underline">
                                                    Lihat
                                                </button>
                                            @endcan

                                            @can('hapus produksi')
                                                <form action="{{ route('admin.production.delete', $batch->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data produksi ini?')"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-[#EC0000] hover:underline">
                                                    Hapus
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-6 text-center font-semibold text-gray-600">
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

        {{-- MODAL DETAIL --}}
        <div x-show="open" x-transition @click.self="closeDetail()"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-2 backdrop-blur-sm sm:p-4"
            style="display: none;">
            <div
                class="mx-auto flex max-h-[calc(100dvh-16px)] w-full max-w-5xl flex-col overflow-hidden rounded-xl border border-gray-300 bg-[#f3f3f3] shadow-2xl sm:max-h-[calc(100dvh-32px)]">
                <div
                    class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-300 bg-[#f3f3f3] px-4 py-3 sm:px-6 sm:py-4">
                    <div class="text-sm font-bold text-gray-800 sm:text-base">
                        Laporan Produksi Nomor:
                        <span class="font-extrabold" x-text="detail.laporan_no"></span>
                    </div>

                    <button type="button" @click="closeDetail()"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full text-gray-700 hover:bg-gray-200">
                        <span class="text-2xl leading-none">×</span>
                    </button>
                </div>

                <div class="overflow-y-auto overscroll-contain px-4 py-4 sm:px-6">
                    <div class="mb-5 grid grid-cols-1 gap-4 md:grid-cols-3 sm:gap-5">
                        <div>
                            <label class="mb-2 block text-xs font-bold text-gray-700">Nama Penanggung Jawab</label>
                            <input x-bind:value="detail.pj" readonly class="{{ $readonlyGray }}">
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-bold text-gray-700">Gudang</label>
                            <input x-bind:value="detail.gudang" readonly class="{{ $readonlyGray }}">
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-bold text-gray-700">Tanggal Masuk</label>
                            <input x-bind:value="detail.tanggal_masuk" readonly class="{{ $readonlyGray }}">
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-300 bg-gray-50 p-3 sm:p-4">
                        <div class="mb-3 hidden grid-cols-4 gap-4 text-xs font-bold text-gray-700 md:grid">
                            <div>ID Produksi</div>
                            <div>Stock Keeping Unit</div>
                            <div>Nama Produk</div>
                            <div>Jumlah Produksi</div>
                        </div>

                        <div class="space-y-3">
                            <div class="rounded-xl border border-gray-300 bg-white p-4 space-y-3 md:hidden">
                                <div>
                                    <div class="mb-1 text-[11px] font-bold text-gray-600">ID Produksi</div>
                                    <input x-bind:value="detail.id_produksi" readonly class="{{ $readonlyGray }}">
                                </div>
                                <div>
                                    <div class="mb-1 text-[11px] font-bold text-gray-600">Stock Keeping Unit</div>
                                    <input x-bind:value="detail.sku" readonly class="{{ $readonlyGray }}">
                                </div>
                                <div>
                                    <div class="mb-1 text-[11px] font-bold text-gray-600">Nama Produk</div>
                                    <input x-bind:value="detail.nama_produk" readonly class="{{ $readonlyGray }}">
                                </div>
                                <div>
                                    <div class="mb-1 text-[11px] font-bold text-gray-600">Jumlah Produksi</div>
                                    <input x-bind:value="detail.jumlah_produksi" readonly class="{{ $readonlyGray }}">
                                </div>
                            </div>

                            <div class="hidden grid-cols-4 gap-3 md:grid">
                                <input x-bind:value="detail.id_produksi" readonly class="{{ $readonlyWhite }}">
                                <input x-bind:value="detail.sku" readonly class="{{ $readonlyWhite }}">
                                <input x-bind:value="detail.nama_produk" readonly class="{{ $readonlyWhite }}">
                                <input x-bind:value="detail.jumlah_produksi" readonly class="{{ $readonlyWhite }}">
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 rounded-lg border border-gray-300 bg-gray-50 p-3 sm:p-4">
                        <div class="mb-3 hidden grid-cols-4 gap-4 text-xs font-bold text-gray-700 md:grid">
                            <div>ID Barang</div>
                            <div>Nama Barang</div>
                            <div>Stok Saat Dipakai</div>
                            <div>Jumlah Stok Digunakan</div>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(item, index) in detail.items" :key="index">
                                <div>
                                    <div class="rounded-xl border border-gray-300 bg-white p-4 space-y-3 md:hidden">
                                        <div>
                                            <div class="mb-1 text-[11px] font-bold text-gray-600">ID Barang</div>
                                            <input x-bind:value="item.code" readonly class="{{ $readonlyGray }}">
                                        </div>
                                        <div>
                                            <div class="mb-1 text-[11px] font-bold text-gray-600">Nama Barang</div>
                                            <input x-bind:value="item.name" readonly class="{{ $readonlyGray }}">
                                        </div>
                                        <div>
                                            <div class="mb-1 text-[11px] font-bold text-gray-600">Stok Saat Dipakai</div>
                                            <input x-bind:value="item.stock" readonly class="{{ $readonlyGray }}">
                                        </div>
                                        <div>
                                            <div class="mb-1 text-[11px] font-bold text-gray-600">Jumlah Stok Digunakan
                                            </div>
                                            <input x-bind:value="item.used" readonly class="{{ $readonlyGray }}">
                                        </div>
                                    </div>

                                    <div class="hidden grid-cols-4 gap-3 md:grid">
                                        <input x-bind:value="item.code" readonly class="{{ $readonlyWhite }}">
                                        <input x-bind:value="item.name" readonly class="{{ $readonlyWhite }}">
                                        <input x-bind:value="item.stock" readonly class="{{ $readonlyWhite }}">
                                        <input x-bind:value="item.used" readonly class="{{ $readonlyWhite }}">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6">
                        <button type="button" @click="closeDetail()"
                            class="inline-flex items-center justify-center rounded-lg bg-red-600 px-10 py-3 text-sm font-bold text-white hover:bg-red-700">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
