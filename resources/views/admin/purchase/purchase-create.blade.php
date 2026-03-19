@extends('admin.layout.master')

@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-barang-masuk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('addCss')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">

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

        .filepond--root {
            font-family: inherit;
        }
    </style>
@endsection

@section('content')
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-800">
            <span class="text-gray-800">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-800 hover:underline">Barang Masuk</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600 font-bold">Tambah Barang Masuk</span>
        </div>
    </section>

    @php
        $oldItems = old('items');
    @endphp

    <form x-data="barangMasukUI()" x-init="init(@js($oldItems))" class="space-y-5" action="{{ route('store-purchase-receipt') }}"
        method="POST" enctype="multipart/form-data">
        @csrf

        {{-- hidden input kalau warehouse select hanya display --}}
        <input type="hidden" name="warehouse_id" x-model="warehouse_id">

        {{-- ROW 1 --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                {{-- ID Pengadaan --}}
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Id Pengadaan</label>
                    <select name="procurement_id" x-ref="procurementSelect" x-model="procurement_id"
                        class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900 @error('procurement_id') border-red-500 @enderror">
                        <option value="">-- Pilih Id Pengadaan --</option>
                        @foreach ($procurements as $procurement)
                            <option value="{{ $procurement->id }}" data-warehouse-id="{{ $procurement->warehouse_id }}"
                                {{ old('procurement_id') == $procurement->id ? 'selected' : '' }}>
                                {{ $procurement->id }}
                            </option>
                        @endforeach
                    </select>
                    @error('procurement_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Warehouse --}}
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Gudang</label>
                    <select x-ref="warehouseSelect" x-model="warehouse_id" disabled
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 @error('warehouse_id') border-red-500 @enderror">
                        <option value="">-- Pilih Gudang --</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('warehouse_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Total Pesanan --}}
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Total Pesanan</label>
                    <input type="number" name="total_price" value="{{ old('total_price') }}"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 @error('total_price') border-red-500 @enderror" />
                    @error('total_price')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Tanggal Barang Masuk</label>
                    <input type="date" name="received_at" value="{{ old('received_at', now()->format('Y-m-d')) }}"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 @error('received_at') border-red-500 @enderror">
                    @error('received_at')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- ITEMS HEADER --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-base font-bold text-gray-800">Daftar Barang</div>
                    <div class="text-xs text-gray-600">Tambah / hapus item barang masuk</div>
                </div>

                <button type="button" @click="addItem()"
                    class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-4 py-2 text-xs font-bold text-white hover:bg-blue-800">
                    + Tambah Item
                </button>
            </div>

            @error('items')
                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </section>

        {{-- ITEMS --}}
        <template x-for="(item, index) in items" :key="item.key">
            <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-sm font-bold text-gray-800">
                        Item <span x-text="index + 1"></span>
                    </div>

                    <button type="button" @click="removeItem(index)"
                        class="inline-flex items-center justify-center rounded-lg bg-red-600 px-3 py-2 text-xs font-bold text-white hover:bg-red-700"
                        x-show="items.length > 1">
                        Hapus
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Raw Material --}}
                    <div>
                        <label class="block text-sm font-bold mb-2">Nama Barang</label>

                        <select :id="`material_${item.key}`" :name="`items[${index}][raw_material_id]`"
                            x-ref="materialSelects" x-bind:data-key="item.key"
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($rawMaterials as $rm)
                                <option value="{{ $rm->id }}">{{ $rm->name }}</option>
                            @endforeach
                        </select>

                        <p class="mt-1 text-xs text-red-600" x-show="fieldError(`items.${index}.raw_material_id`)"
                            x-text="fieldError(`items.${index}.raw_material_id`)"></p>
                    </div>

                    {{-- Quantity --}}
                    <div>
                        <label class="block text-sm font-bold mb-2">Jumlah Barang Masuk</label>
                        <input :name="`items[${index}][quantity_received]`" x-model="item.quantity_received" type="number"
                            min="1" placeholder="Contoh: 150"
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-blue-600 focus:ring-0">
                        <p class="mt-1 text-xs text-red-600" x-show="fieldError(`items.${index}.quantity_received`)"
                            x-text="fieldError(`items.${index}.quantity_received`)"></p>
                    </div>
                </div>
            </section>
        </template>

        {{-- FILEPOND --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <label class="block text-sm font-bold mb-3 text-gray-800">Invoice Pembelian Barang</label>

            <input x-ref="invoices" type="file" name="invoices[]" multiple
                accept="image/png,image/jpeg,application/pdf" />

            <p class="mt-2 text-xs text-gray-600">
                Format: PNG/JPG/JPEG/PDF • Maks 3MB per file • Bisa upload multiple.
            </p>

            @error('invoices')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
            @error('invoices.*')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </section>

        {{-- ACTIONS --}}
        <div class="flex items-center justify-end gap-4 pt-2">
            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                Simpan
            </button>
        </div>
    </form>
@endsection

@section('addJs')
    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: @js(session('success')),
                confirmButtonColor: '#2563eb'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: @js(session('error')),
                confirmButtonColor: '#dc2626'
            });
        </script>
    @endif

    <script>
        function barangMasukUI() {
            return {
                items: [],
                errors: @js($errors->toArray()),
                procurement_id: @js(old('procurement_id', '')),
                warehouse_id: @js(old('warehouse_id', '')),
                pond: null,

                init(oldItems) {
                    if (Array.isArray(oldItems) && oldItems.length > 0) {
                        this.items = oldItems.map((x) => ({
                            key: crypto.randomUUID(),
                            raw_material_id: x.raw_material_id ? parseInt(x.raw_material_id, 10) : '',
                            quantity_received: x.quantity_received ?? ''
                        }));
                    } else {
                        this.items = [this.createItem()];
                    }

                    this.$nextTick(() => {
                        this.initSelect2Procurement();
                        this.initSelect2Warehouse();
                        this.initMaterialSelects();
                        this.syncWarehouseFromProcurement();
                        this.initFilePond();
                    });
                },

                fieldError(key) {
                    const e = this.errors?.[key];
                    if (!e) return '';
                    return Array.isArray(e) ? e[0] : e;
                },

                hasSelect2() {
                    return typeof $ !== 'undefined' && typeof $.fn.select2 === 'function';
                },

                createItem() {
                    return {
                        key: crypto.randomUUID(),
                        raw_material_id: '',
                        quantity_received: ''
                    };
                },

                addItem() {
                    this.items.push(this.createItem());
                    this.$nextTick(() => this.initMaterialSelects());
                },

                removeItem(index) {
                    const key = this.items[index]?.key;
                    if (key) this.destroyMaterialSelect(key);

                    this.items.splice(index, 1);

                    if (this.items.length === 0) {
                        this.items.push(this.createItem());
                        this.$nextTick(() => this.initMaterialSelects());
                    }
                },

                initSelect2Procurement() {
                    const el = this.$refs.procurementSelect;
                    if (!el || !this.hasSelect2()) return;

                    if ($(el).hasClass('select2-hidden-accessible')) {
                        $(el).select2('destroy');
                    }

                    $(el).select2({
                        width: '100%',
                        placeholder: '-- Pilih Id Pengadaan --',
                        allowClear: true
                    });

                    $(el).on('change', () => {
                        this.procurement_id = $(el).val() || '';
                        this.syncWarehouseFromProcurement();
                    });

                    if (this.procurement_id) {
                        $(el).val(String(this.procurement_id)).trigger('change.select2');
                    }
                },

                initSelect2Warehouse() {
                    const el = this.$refs.warehouseSelect;
                    if (!el || !this.hasSelect2()) return;

                    if ($(el).hasClass('select2-hidden-accessible')) {
                        $(el).select2('destroy');
                    }

                    $(el).select2({
                        width: '100%',
                        placeholder: '-- Pilih Gudang --',
                        allowClear: true,
                        disabled: true
                    });

                    if (this.warehouse_id) {
                        $(el).val(String(this.warehouse_id)).trigger('change.select2');
                    }
                },

                syncWarehouseFromProcurement() {
                    const procurementEl = this.$refs.procurementSelect;
                    const warehouseEl = this.$refs.warehouseSelect;

                    if (!procurementEl || !warehouseEl) return;

                    const selectedOption = procurementEl.options[procurementEl.selectedIndex];
                    const warehouseId = selectedOption ? (selectedOption.dataset.warehouseId || '') : '';

                    this.warehouse_id = warehouseId;

                    if (this.hasSelect2()) {
                        $(warehouseEl).val(warehouseId).trigger('change.select2');
                    } else {
                        warehouseEl.value = warehouseId;
                    }
                },

                initMaterialSelects() {
                    if (!this.hasSelect2()) return;

                    const refs = this.$refs.materialSelects;
                    if (!refs) return;

                    const els = Array.isArray(refs) ? refs : [refs];

                    els.forEach((el) => {
                        if (!el || el.dataset.inited === '1') return;

                        $(el).select2({
                            width: '100%',
                            placeholder: '-- Pilih Barang --',
                            allowClear: true
                        });

                        el.dataset.inited = '1';

                        const key = el.dataset.key;
                        const idxInit = this.items.findIndex(x => x.key === key);

                        if (idxInit !== -1 && this.items[idxInit].raw_material_id) {
                            $(el).val(String(this.items[idxInit].raw_material_id)).trigger('change.select2');
                        }

                        $(el).on('change', () => {
                            const val = $(el).val() || '';
                            const idx = this.items.findIndex(x => x.key === key);

                            if (idx !== -1) {
                                this.items[idx].raw_material_id = val ? parseInt(val, 10) : '';
                            }
                        });
                    });
                },

                destroyMaterialSelect(key) {
                    if (!this.hasSelect2()) return;

                    const el = document.getElementById(`material_${key}`);
                    if (!el) return;

                    if (el.dataset.inited === '1') {
                        $(el).off('change');
                        $(el).select2('destroy');
                        el.dataset.inited = '0';
                    }
                },

                initFilePond() {
                    const input = this.$refs.invoices;

                    if (!input || typeof FilePond === 'undefined') return;
                    if (!input.parentNode) return;
                    if (this.pond) return;

                    FilePond.registerPlugin(
                        FilePondPluginFileValidateType,
                        FilePondPluginFileValidateSize,
                        FilePondPluginImagePreview
                    );

                    this.pond = FilePond.create(input, {
                        required: true,
                        storeAsFile: true,
                        instantUpload: false,
                        stylePanelLayout: 'compact',
                        allowMultiple: true,
                        maxFiles: 10,
                        credits: false,
                        acceptedFileTypes: ['image/png', 'image/jpeg', 'application/pdf'],
                        maxFileSize: '3MB',
                        labelIdle: 'Drag & Drop file atau <span class="filepond--label-action">Browse</span>',
                        labelFileTypeNotAllowed: 'Format file tidak didukung',
                        fileValidateTypeLabelExpectedTypes: 'Hanya PNG/JPG/JPEG/PDF',
                        labelMaxFileSizeExceeded: 'Ukuran file terlalu besar',
                        labelMaxFileSize: 'Maksimum 3MB',
                    });
                },
            }
        }
    </script>
@endsection
