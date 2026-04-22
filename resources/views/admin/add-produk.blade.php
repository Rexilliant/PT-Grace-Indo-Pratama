@extends('admin.layout.master')

@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-laporan-produksi', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('addCss')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-container .select2-selection--single {
            height: 42px;
            border-radius: 0.375rem;
            border: 1px solid #9CA3AF;
            display: flex;
            align-items: center;
            padding-left: 10px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
        }

        .select2-dropdown {
            border-radius: 0.375rem;
        }
    </style>
@endsection

@section('content')
    @php
        $readonlyClass =
            'w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700';
        $inputClass =
            'w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-500';
        $sectionClass = 'p-5 shadow border border-gray-300 rounded-xl';
    @endphp

    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-800">
            <span>Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="hover:underline">Produksi</a>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="hover:underline">Pilih Produk</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600 font-bold">Tambah Produk</span>
        </div>
    </section>

    <form action="{{ route('admin.production.store') }}" method="POST" class="space-y-5" id="productionForm">
        @csrf

        <input type="hidden" name="product_variant_id" value="{{ $productVariant->id }}">

        {{-- ROW 1 --}}
        <section class="bg-gray-200/80 {{ $sectionClass }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Nama Penerima</label>
                    <input type="text" value="{{ $personResponsible->name }}" readonly
                        class="{{ $readonlyClass }} cursor-not-allowed" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Gudang</label>
                    <select name="warehouse_id" id="warehouse_id" class="{{ $inputClass }}">
                        <option value="">-- Pilih Gudang --</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}"
                                {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('warehouse_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Tanggal Produksi</label>
                    <input type="date" name="entry_date" value="{{ old('entry_date', date('Y-m-d')) }}"
                        class="{{ $inputClass }}">
                    @error('entry_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- ROW 2 --}}
        <section class="bg-[#53BF6A]/55 {{ $sectionClass }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">ID Barang</label>
                    <input type="text" value="{{ $productVariant->product?->code ?? '-' }}" readonly
                        class="{{ $readonlyClass }}">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Stock Keeping Unit</label>
                    <input type="text" value="{{ $productVariant->sku }}" readonly class="{{ $readonlyClass }}">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Nama Produk</label>
                    <input type="text" value="{{ $productVariant->name }}" readonly class="{{ $readonlyClass }}">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Jumlah Produksi</label>
                    <input type="number" min="1" name="quantity" value="{{ old('quantity') }}"
                        class="{{ $inputClass }}">
                    @error('quantity')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- ITEMS --}}
        <section class="bg-gray-200/80 {{ $sectionClass }}">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-700">Daftar Bahan Baku</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Pilih gudang terlebih dahulu, lalu tambahkan bahan baku yang ingin digunakan.
                    </p>
                </div>

                <button type="button" id="btnAddItem"
                    class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-4 py-2 text-sm font-bold text-white hover:bg-blue-800">
                    + Add Item
                </button>
            </div>

            <div id="warehouseWarning"
                class="hidden mb-4 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-xl p-4 text-sm">
                Pilih gudang dulu, baru bahan baku bisa ditambahkan.
            </div>

            <div id="loadingMaterials"
                class="hidden mb-4 bg-gray-100 border border-gray-200 text-gray-700 rounded-xl p-4 text-sm">
                Memuat bahan baku...
            </div>

            <div id="materialsError"
                class="hidden mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm">
            </div>

            <div id="materialsInfo"
                class="hidden mb-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-xl p-4 text-sm">
            </div>

            <div id="itemsContainer" class="space-y-4">
                @if (old('items'))
                    @foreach (old('items') as $i => $item)
                        <section class="item-row border border-gray-300 rounded-xl p-4 bg-white">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Bahan Baku</label>
                                    <select name="items[{{ $i }}][raw_material_id]"
                                        class="rawMaterialSelect {{ $inputClass }}">
                                        <option value="">-- Pilih Bahan Baku --</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">ID Barang</label>
                                    <input type="text" class="materialCodeDisplay {{ $readonlyClass }}" readonly
                                        value="">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Stok Tersedia</label>
                                    <input type="text" class="materialStockDisplay {{ $readonlyClass }}" readonly
                                        value="">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Stok Digunakan</label>
                                    <div class="flex gap-2">
                                        <input type="number" min="0" name="items[{{ $i }}][quantity_use]"
                                            value="{{ $item['quantity_use'] ?? '' }}" class="{{ $inputClass }}"
                                            placeholder="Masukkan jumlah">
                                        <button type="button"
                                            class="btnRemoveItem inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700 whitespace-nowrap">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </section>
                    @endforeach
                @else
                    <section class="item-row border border-gray-300 rounded-xl p-4 bg-white">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-2">Bahan Baku</label>
                                <select name="items[0][raw_material_id]" class="rawMaterialSelect {{ $inputClass }}">
                                    <option value="">-- Pilih Bahan Baku --</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-2">ID Barang</label>
                                <input type="text" class="materialCodeDisplay {{ $readonlyClass }}" readonly
                                    value="">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-2">Stok Tersedia</label>
                                <input type="text" class="materialStockDisplay {{ $readonlyClass }}" readonly
                                    value="">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-2">Stok Digunakan</label>
                                <div class="flex gap-2">
                                    <input type="number" min="0" name="items[0][quantity_use]"
                                        class="{{ $inputClass }}" placeholder="Masukkan jumlah">
                                    <button type="button"
                                        class="btnRemoveItem inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700 whitespace-nowrap">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif
            </div>

            <template id="itemTemplate">
                <section class="item-row border border-gray-300 rounded-xl p-4 bg-white">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">Bahan Baku</label>
                            <select name="items[__INDEX__][raw_material_id]" class="rawMaterialSelect {{ $inputClass }}">
                                <option value="">-- Pilih Bahan Baku --</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">ID Barang</label>
                            <input type="text" class="materialCodeDisplay {{ $readonlyClass }}" readonly
                                value="">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">Stok Tersedia</label>
                            <input type="text" class="materialStockDisplay {{ $readonlyClass }}" readonly
                                value="">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">Stok Digunakan</label>
                            <div class="flex gap-2">
                                <input type="number" min="0" name="items[__INDEX__][quantity_use]"
                                    class="{{ $inputClass }}" placeholder="Masukkan jumlah">
                                <button type="button"
                                    class="btnRemoveItem inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700 whitespace-nowrap">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            </template>
        </section>

        {{-- NOTE --}}
        <div>
            <label class="block text-sm font-bold text-gray-800 mb-2">Catatan</label>
            <textarea name="note" rows="3"
                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-500">{{ old('note') }}</textarea>
            @error('note')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- ACTION --}}
        <div class="flex items-center justify-end gap-4 pt-2">
            <a href="{{ route('admin.gudang-laporan-produksi') }}"
                class="inline-flex items-center justify-center rounded-lg bg-red-600 px-10 py-3 text-sm font-bold text-white hover:bg-red-700">
                Batal
            </a>

            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                Simpan
            </button>
        </div>
    </form>
@endsection

@section('addJs')
    <script>
        let warehouseMaterials = [];

        function initSelect2For($el) {
            $el.select2({
                placeholder: "Cari bahan baku...",
                allowClear: true,
                width: '100%'
            });
        }

        function buildMaterialOptions(selectedId = '') {
            let html = `<option value="">-- Pilih Bahan Baku --</option>`;

            warehouseMaterials.forEach(item => {
                const selected = String(selectedId) === String(item.raw_material_id) ? 'selected' : '';
                html += `
                    <option value="${item.raw_material_id}" 
                        data-code="${item.id_barang ?? ''}"
                        data-name="${item.nama_barang ?? ''}"
                        data-stock="${item.stok_tersedia ?? 0}"
                        data-unit="${item.unit ?? ''}"
                        ${selected}>
                        ${(item.id_barang ?? '-') + ' - ' + (item.nama_barang ?? '-') + ' / ' + (item.unit ?? '')}
                    </option>
                `;
            });

            return html;
        }

        function updateMaterialInfo($row) {
            const $selected = $row.find('.rawMaterialSelect option:selected');
            const code = $selected.data('code') ?? '';
            const stock = $selected.data('stock') ?? '';
            const unit = $selected.data('unit') ?? '';

            $row.find('.materialCodeDisplay').val(code);
            $row.find('.materialStockDisplay').val(code ? `${stock} ${unit}` : '');
        }

        function reindexItems() {
            $('#itemsContainer .item-row').each(function(index) {
                $(this).find('.rawMaterialSelect')
                    .attr('name', `items[${index}][raw_material_id]`);

                $(this).find('input[type="number"]')
                    .attr('name', `items[${index}][quantity_use]`);
            });
        }

        function refillAllSelectOptions() {
            $('#itemsContainer .item-row').each(function() {
                const $row = $(this);
                const $select = $row.find('.rawMaterialSelect');
                const currentValue = $select.val();

                if ($select.data('select2')) {
                    $select.select2('destroy');
                }

                $select.html(buildMaterialOptions(currentValue));
                initSelect2For($select);
                updateMaterialInfo($row);
            });
        }

        function addItem(selectedMaterialId = '', quantityUse = '') {
            const container = $('#itemsContainer');
            const templateHtml = $('#itemTemplate').html();
            const nextIndex = container.find('.item-row').length;
            const newHtml = templateHtml.replaceAll('__INDEX__', nextIndex);
            const $newItem = $(newHtml);

            container.append($newItem);

            const $select = $newItem.find('.rawMaterialSelect');
            $select.html(buildMaterialOptions(selectedMaterialId));
            initSelect2For($select);

            if (quantityUse !== '') {
                $newItem.find('input[type="number"]').val(quantityUse);
            }

            updateMaterialInfo($newItem);
        }

        async function fetchMaterialsByWarehouse(warehouseId) {
            if (!warehouseId) {
                warehouseMaterials = [];
                refillAllSelectOptions();
                $('#warehouseWarning').removeClass('hidden');
                $('#loadingMaterials').addClass('hidden');
                $('#materialsError').addClass('hidden').text('');
                $('#materialsInfo').addClass('hidden').text('');
                return;
            }

            $('#warehouseWarning').addClass('hidden');
            $('#loadingMaterials').removeClass('hidden');
            $('#materialsError').addClass('hidden').text('');
            $('#materialsInfo').addClass('hidden').text('');

            try {
                const url =
                    `{{ route('admin.production.materials') }}?warehouse_id=${encodeURIComponent(warehouseId)}`;

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const result = await response.json();

                if (!result.success) {
                    throw new Error(result.message || 'Gagal mengambil data bahan baku.');
                }

                warehouseMaterials = Array.isArray(result.materials) ? result.materials : [];
                refillAllSelectOptions();

                if (warehouseMaterials.length === 0) {
                    $('#materialsInfo')
                        .removeClass('hidden')
                        .text('Tidak ada stok bahan baku untuk gudang ini.');
                } else {
                    $('#materialsInfo')
                        .removeClass('hidden')
                        .text(`Bahan baku tersedia: ${warehouseMaterials.length} item.`);
                }
            } catch (error) {
                console.error('Fetch materials error:', error);
                warehouseMaterials = [];
                refillAllSelectOptions();
                $('#materialsError')
                    .removeClass('hidden')
                    .text('Gagal mengambil data bahan baku.');
            } finally {
                $('#loadingMaterials').addClass('hidden');
            }
        }

        $(document).ready(function() {
            $('#warehouse_id').select2({
                placeholder: "Cari Gudang",
                allowClear: true,
                width: '100%'
            });

            initSelect2For($('.rawMaterialSelect'));

            const currentWarehouse = $('#warehouse_id').val();
            if (currentWarehouse) {
                fetchMaterialsByWarehouse(currentWarehouse).then(() => {
                    @if (old('items'))
                        const oldItems = @json(old('items'));
                        $('#itemsContainer').empty();

                        oldItems.forEach((item, index) => {
                            addItem(item.raw_material_id ?? '', item.quantity_use ?? '');
                        });
                    @else
                        refillAllSelectOptions();
                    @endif
                });
            } else {
                $('#warehouseWarning').removeClass('hidden');
            }

            $('#warehouse_id').on('change', async function() {
                const warehouseId = $(this).val();

                await fetchMaterialsByWarehouse(warehouseId);

                $('#itemsContainer').empty();
                addItem();
                reindexItems();
            });

            $('#btnAddItem').on('click', function() {
                const warehouseId = $('#warehouse_id').val();

                if (!warehouseId) {
                    $('#warehouseWarning').removeClass('hidden');
                    return;
                }

                addItem();
                reindexItems();
            });

            $(document).on('change', '.rawMaterialSelect', function() {
                const $row = $(this).closest('.item-row');
                updateMaterialInfo($row);
            });

            $(document).on('click', '.btnRemoveItem', function() {
                const $row = $(this).closest('.item-row');
                const $select = $row.find('.rawMaterialSelect');

                if ($select.data('select2')) {
                    $select.select2('destroy');
                }

                $row.remove();

                if ($('#itemsContainer .item-row').length === 0) {
                    addItem();
                }

                reindexItems();
            });
        });
    </script>
@endsection