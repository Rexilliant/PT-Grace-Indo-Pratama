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
            margin-bottom: 0;
            min-height: 260px;
            /* Area drop awal saat kosong */
            transition: all 0.3s ease;
        }

        .filepond--panel-root {
            background-color: #ffffff !important;
            border: 2px dashed #d1d5db !important;
            border-radius: 1rem !important;
            transition: all 0.3s ease;
        }

        .filepond--root:hover .filepond--panel-root {
            border-color: #3b82f6 !important;
            background-color: #eff6ff !important;
        }

        .filepond--drop-label {
            background-color: transparent !important;
            cursor: pointer;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 1.5rem !important;
            height: 100% !important;
            min-height: 260px;
        }

        .filepond--label-action {
            text-decoration: none;
            cursor: pointer;
            color: #3b82f6;
            font-weight: 700;
        }

        .filepond--root.has-files {
            height: 280px !important;
            min-height: 280px !important;
        }

        .filepond--root.has-files .filepond--panel-root {
            transform: none !important;
            height: 100% !important;
        }

        .filepond--root.has-files .filepond--drop-label {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            min-height: 45px !important;
            height: 45px !important;
            padding: 0 !important;
            border-bottom: 1px dashed #d1d5db;
            background: #f8fafc !important;
            border-radius: 1rem 1rem 0 0 !important;
            z-index: 10;
            opacity: 1 !important;
            transform: none !important;
        }

        .filepond--root.has-files .fp-icon-large,
        .filepond--root.has-files .fp-text-large {
            display: none !important;
        }

        .filepond--root.has-files .fp-text-mini {
            display: flex !important;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }

        .filepond--root.has-files .filepond--list-scroller {
            position: absolute !important;
            top: 45px !important;
            /* Mulai di bawah header */
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            height: auto !important;
            transform: none !important;
            overflow-x: auto !important;
            overflow-y: hidden !important;
            padding: 15px !important;
            margin-top: 0 !important;
        }

        .filepond--root.has-files .filepond--list {
            display: flex !important;
            flex-direction: row !important;
            gap: 15px;
            position: static !important;
            transform: none !important;
            height: 100% !important;
        }

        .filepond--root.has-files .filepond--item {
            position: static !important;
            transform: none !important;
            width: 180px !important;
            height: calc(100% - 10px) !important;
            /* Menyesuaikan ruang scroller */
            flex-shrink: 0;
            margin: 0 !important;
        }

        .filepond--list-scroller::-webkit-scrollbar {
            height: 8px;
        }

        .filepond--list-scroller::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 8px;
        }

        .filepond--list-scroller::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 8px;
        }

        .filepond--list-scroller::-webkit-scrollbar-thumb:hover {
            background: #64748b;
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
                        {{-- <input type="hidden" :name="`items[${index}][raw_material_id]`" :value="item.raw_material_id"> --}}
                        <select :id="`material_${item.key}`" x-ref="materialSelects" x-bind:data-key="item.key"
                            x-model="item.raw_material_id" x-init="item.raw_material_id = item.raw_material_id || ''"
                            :name="`items[${index}][raw_material_id]`"
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900">
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
                            placeholder="Contoh: 150" min="1"
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

            <input id="imageInput" x-ref="invoices" type="file" name="invoices[]" multiple
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
                async fetchProcurementItems() {
                    if (!this.procurement_id) {
                        this.items = [this.createItem()];
                        this.warehouse_id = '';
                        return;
                    }

                    try {
                        const url = `{{ url('/admin/purchase-receipts/procurement-items') }}/${this.procurement_id}`;
                        console.log('Fetching procurement items from:', url);
                        const response = await fetch(url, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        });

                        const result = await response.json();

                        if (!result.success) {
                            throw new Error('Gagal mengambil item pengadaan.');
                        }

                        this.warehouse_id = result.warehouse_id || '';

                        if (this.hasSelect2()) {
                            $(this.$refs.warehouseSelect).val(String(this.warehouse_id)).trigger('change.select2');
                        }

                        this.items = result.items.map((item) => ({
                            key: crypto.randomUUID(),
                            raw_material_id: item.raw_material_id,
                            quantity_received: item.quantity_received,
                        }));

                        if (this.items.length === 0) {
                            this.items = [this.createItem()];
                        }

                        this.$nextTick(() => {
                            this.initMaterialSelects();

                            this.items.forEach((item) => {
                                const el = document.getElementById(`material_${item.key}`);

                                if (el && this.hasSelect2()) {
                                    $(el).val(String(item.raw_material_id)).trigger('change.select2');
                                }
                            });
                        });

                    } catch (error) {
                        console.error(error);
                        alert('Gagal mengambil item pengadaan.');
                    }
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
                        this.fetchProcurementItems();
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
                    const input = document.getElementById('imageInput');

                    if (!input || typeof FilePond === 'undefined') return;
                    if (!input.parentNode) return;
                    if (this.pond) return;

                    FilePond.registerPlugin(
                        FilePondPluginFileValidateType,
                        FilePondPluginFileValidateSize,
                        FilePondPluginImagePreview
                    );

                    const customIconPlaceholder = `
                        <div class="flex flex-col items-center justify-center w-full">
                            <div class="fp-icon-large p-4 bg-blue-50 rounded-full mb-4 transition-transform duration-300 hover:scale-110">
                                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="fp-text-large text-center">
                                <p class="text-base font-bold text-gray-700"><span class="filepond--label-action">Klik</span> atau Tarik dokumen ke sini</p>
                                <p class="text-xs text-gray-500 mt-1 font-medium">PNG, JPG, JPEG, PDF (Bisa multiple, Maks 3MB/file)</p>
                            </div>

                            <div class="fp-text-mini hidden cursor-pointer hover:underline text-blue-600">
                                <p class="text-sm font-bold m-0 p-0">+ Tambah Dokumen Lain</p>
                            </div>
                        </div>
                    `;

                    this.pond = FilePond.create(input, {
                        required: true,
                        storeAsFile: true,
                        instantUpload: false,
                        allowMultiple: true,
                        maxFiles: 10,
                        credits: false,
                        acceptedFileTypes: ['image/png', 'image/jpeg', 'application/pdf'],
                        maxFileSize: '3MB',

                        // Memasukkan custom UI di sini
                        labelIdle: customIconPlaceholder,

                        labelFileTypeNotAllowed: 'Format file tidak didukung',
                        fileValidateTypeLabelExpectedTypes: 'Hanya PNG/JPG/JPEG/PDF',
                        labelMaxFileSizeExceeded: 'Ukuran file terlalu besar',
                        labelMaxFileSize: 'Maksimum 3MB',

                        onupdatefiles: (files) => {
                            const rootElement = document.getElementById('imageInput').closest(
                                '.filepond--root');

                            if (rootElement) {
                                if (files.length > 0) {
                                    rootElement.classList.add('has-files');
                                } else {
                                    rootElement.classList.remove('has-files');
                                }
                            }
                        }
                    });
                },

            }
        }
    </script>
@endsection
