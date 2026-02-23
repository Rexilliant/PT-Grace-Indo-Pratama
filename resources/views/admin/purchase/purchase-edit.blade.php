@extends('admin.layout.master')

@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-barang-masuk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('addCss')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
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
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-800">
            <span class="text-gray-800">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-800 hover:underline">Barang Masuk</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600 font-bold">Edit Barang Masuk</span>
        </div>
    </section>

    @php
        $hasProvinces = !empty($provinces) && count($provinces) > 0;

        // ==== INIT ITEMS: old() menang, kalau tidak ada pakai DB ====
        $oldItems = old('items');

        $dbItems = $receipt
            ->items()
            ->orderBy('id')
            ->get()
            ->map(
                fn($it) => [
                    'raw_material_id' => (int) $it->raw_material_id,
                    'quantity_received' => (int) $it->quantity_received,
                ],
            )
            ->values()
            ->all();

        $initItems = is_array($oldItems) ? $oldItems : $dbItems;
        if (empty($initItems)) {
            $initItems = [['raw_material_id' => '', 'quantity_received' => '']];
        }

        $selectedProcId = old('procurement_id', $receipt->procurement_id);
        $selectedProvince = old('province', $receipt->province);
    @endphp

    <form x-data="purchaseReceiptEdit({ initItems: @js($initItems) })" x-init="init()" class="space-y-5 mb-5"
        action="{{ route('update-barang-masuk', $receipt->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- ROW 1 --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="mb-5">
                <p>Nomor Barang Masuk: {{ $receipt->receipt_number }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                {{-- ID Pengadaan --}}
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Id Pengadaan</label>
                    <select name="procurement_id" x-ref="procurementSelect"
                        class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900 @error('procurement_id') border-red-500 @enderror">
                        <option value="">-- Pilih Id Pengadaan --</option>
                        @foreach ($procurements as $procurement)
                            <option value="{{ $procurement->id }}"
                                {{ (int) $selectedProcId === (int) $procurement->id ? 'selected' : '' }}>
                                {{ $procurement->id }}
                            </option>
                        @endforeach
                    </select>
                    @error('procurement_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Provinsi --}}
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Provinsi</label>

                    @if ($hasProvinces)
                        <select name="province" x-ref="provinceSelect"
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 @error('province') border-red-500 @enderror">
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province['name'] }}"
                                    {{ (string) $selectedProvince === (string) $province['name'] ? 'selected' : '' }}>
                                    {{ $province['name'] }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" name="province" value="{{ $selectedProvince }}"
                            placeholder="Tulis provinsi..."
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 @error('province') border-red-500 @enderror" />
                        <p class="mt-1 text-xs text-gray-600">Data provinsi tidak tersedia, silakan input manual.</p>
                    @endif

                    @error('province')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Total Pesanan --}}
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Total Pesanan</label>
                    <input type="number" name="total_price" value="{{ old('total_price', (int) $receipt->total_price) }}"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 @error('total_price') border-red-500 @enderror" />
                    @error('total_price')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Tanggal Barang Masuk</label>
                    <input type="date" name="received_at"
                        value="{{ old('received_at', optional($receipt->received_at)->format('Y-m-d')) }}"
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

        {{-- ITEMS (REPEATABLE) --}}
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

                        <select
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900"
                            x-ref="materialSelects" :data-key="item.key" :name="`items[${index}][raw_material_id]`"
                            :id="`material_${item.key}`">
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($rawMaterials as $rm)
                                <option value="{{ $rm->id }}"
                                    :selected="String(item.raw_material_id) === '{{ (string) $rm->id }}'">
                                    {{ $rm->name }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-1 text-xs text-red-600" x-show="err(`items.${index}.raw_material_id`)"
                            x-text="err(`items.${index}.raw_material_id`)"></p>
                    </div>

                    {{-- Quantity --}}
                    <div>
                        <label class="block text-sm font-bold mb-2">Jumlah Barang Masuk</label>

                        <input :name="`items[${index}][quantity_received]`" x-model.number="item.quantity_received"
                            type="number" min="1" placeholder="Contoh: 150"
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-blue-600 focus:ring-0">

                        <p class="mt-1 text-xs text-red-600" x-show="err(`items.${index}.quantity_received`)"
                            x-text="err(`items.${index}.quantity_received`)"></p>
                    </div>
                </div>
            </section>
        </template>

        {{-- ACTIONS --}}
        <div class="flex items-center justify-end gap-4 pt-2">
            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                Update
            </button>
        </div>
    </form>
    <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm mb-5">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-800">
                <thead class="bg-[#5aba6f]/70 text-gray-900">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-extrabold text-left">File</th>
                        <th scope="col" class="px-6 py-4 font-extrabold text-left">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-gray-200 divide-y divide-gray-500">
                    @forelse ($invoices as $invoice)
                        <tr class="hover:bg-gray-300">
                            <td class="px-6 py-4 font-semibold"><a target="_blank"
                                    href="{{ $invoice->getUrl() }}">{{ $invoice->file_name }}</a></td>
                            <td class="px-6 py-3">
                                <div class="flex items-center justify-start gap-6 font-semibold">
                                    <form action="{{ route('media.delete', $invoice->id) }}" method="post"
                                        class="delete-invoice-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="text-[#EC0000] hover:underline btn-delete-invoice">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- FILEPOND --}}
    <form action="{{ route('purchase-receipts.add-media', $receipt->id) }}" method="post"
        enctype="multipart/form-data">
        @csrf
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <label class="block text-sm font-bold mb-3 text-gray-800">Invoice Pembelian Barang</label>

            <input id="invoicesPond" type="file" name="invoices[]" multiple
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
                Tambah Invoice
            </button>
        </div>
    </form>
@endsection


@section('addJs')
    <script src="{{ asset('assets/js/sweetalert.js') }}"></script>
    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>

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
        function purchaseReceiptEdit({
            initItems
        }) {
            return {
                items: [],
                errors: @js($errors->toArray()),
                _settingSelect2: false,

                init() {
                    const src = Array.isArray(initItems) && initItems.length ?
                        initItems : [{
                            raw_material_id: '',
                            quantity_received: ''
                        }];

                    this.items = src.map(x => ({
                        key: crypto.randomUUID(),
                        raw_material_id: x.raw_material_id ? parseInt(x.raw_material_id, 10) : '',
                        quantity_received: (x.quantity_received ?? '') === '' ? '' : parseInt(x
                            .quantity_received, 10),
                    }));

                    this.initSelect2Static();
                    this.$nextTick(() => this.reInitMaterialSelect2All());
                },

                err(key) {
                    const e = this.errors?.[key];
                    if (!e) return '';
                    return Array.isArray(e) ? e[0] : e;
                },

                hasSelect2() {
                    return (typeof $ !== 'undefined' && typeof $.fn.select2 === 'function');
                },

                addItem() {
                    this.items.push({
                        key: crypto.randomUUID(),
                        raw_material_id: '',
                        quantity_received: ''
                    });
                    this.$nextTick(() => this.reInitMaterialSelect2All());
                },

                removeItem(index) {
                    const key = this.items[index]?.key;
                    if (key) this.destroyMaterialSelect2(key);

                    this.items.splice(index, 1);
                    if (this.items.length === 0) this.addItem();

                    this.$nextTick(() => this.reInitMaterialSelect2All());
                },

                initSelect2Static() {
                    if (!this.hasSelect2()) return;

                    if (this.$refs.procurementSelect) {
                        $(this.$refs.procurementSelect).select2({
                            width: '100%',
                            placeholder: '-- Pilih Id Pengadaan --',
                            allowClear: true
                        });
                    }

                    if (this.$refs.provinceSelect) {
                        $(this.$refs.provinceSelect).select2({
                            width: '100%',
                            placeholder: '-- Pilih Provinsi --',
                            allowClear: true
                        });
                    }
                },

                reInitMaterialSelect2All() {
                    if (!this.hasSelect2()) return;

                    const refs = this.$refs.materialSelects;
                    if (!refs) return;

                    const els = Array.isArray(refs) ? refs : [refs];

                    els.forEach((el) => {
                        const key = el.dataset.key;

                        if (el.dataset.inited === '1') {
                            $(el).off('change.receipt');
                            $(el).select2('destroy');
                            el.dataset.inited = '0';
                        }

                        $(el).select2({
                            width: '100%',
                            placeholder: '-- Pilih Barang --',
                            allowClear: true
                        });

                        el.dataset.inited = '1';

                        this.setSelect2ValueByKey(el, key);

                        $(el).on('change.receipt', () => {
                            if (this._settingSelect2) return;

                            const val = $(el).val() || '';
                            const idx = this.items.findIndex(x => x.key === key);
                            if (idx !== -1) this.items[idx].raw_material_id = val ? parseInt(val, 10) : '';
                        });
                    });
                },

                setSelect2ValueByKey(el, key) {
                    const idx = this.items.findIndex(x => x.key === key);
                    if (idx === -1) return;

                    const v = this.items[idx].raw_material_id ? String(this.items[idx].raw_material_id) : '';
                    this._settingSelect2 = true;

                    this.$nextTick(() => {
                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
                                $(el).val(v).trigger('change');
                                this._settingSelect2 = false;
                            });
                        });
                    });
                },

                destroyMaterialSelect2(key) {
                    if (!this.hasSelect2()) return;

                    const el = document.getElementById(`material_${key}`);
                    if (!el) return;

                    if (el.dataset.inited === '1') {
                        $(el).off('change.receipt');
                        $(el).select2('destroy');
                        el.dataset.inited = '0';
                    }
                },

            }

        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof FilePond === 'undefined') return;

            FilePond.registerPlugin(
                FilePondPluginFileValidateType,
                FilePondPluginFileValidateSize,
                FilePondPluginImagePreview
            );

            const input = document.getElementById('invoicesPond');
            if (!input) return;

            // Hindari double-init kalau halaman kena re-render / turbolinks sejenis
            if (input.dataset.pondInited === '1') return;
            input.dataset.pondInited = '1';

            FilePond.create(input, {
                storeAsFile: true, // biar masuk request normal Laravel (multipart)
                instantUpload: false, // karena submit via form biasa
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
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.btn-delete-invoice').forEach(function(button) {
                button.addEventListener('click', function() {

                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: "File invoice akan dihapus permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });

                });
            });

        });
    </script>
@endsection
