@extends('admin.layout.master')

@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-penerimaan-pengiriman-produk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')


@section('addCss')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">

    <style>
        .filepond--root {
            font-family: inherit;
            margin-bottom: 0;
            min-height: 260px;
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

        @media (min-width: 50em) {
            .filepond--item {
                width: calc(33.33% - 0.5em);
            }
        }

        @media (min-width: 64em) {
            .filepond--item {
                width: calc(25% - 0.5em);
            }
        }
    </style>
@endsection

@section('content')
    @php
        $sectionClass = 'rounded-xl border border-gray-300 bg-gray-200/80 p-5 shadow';
        $labelClass = 'mb-2.5 block text-xs font-bold text-gray-800';
        $readonlyClass =
            'w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0';
        $inputBaseClass =
            'w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500';
        $inputNormalClass = $inputBaseClass . ' border border-gray-400 bg-white';
        $inputErrorClass = $inputBaseClass . ' border border-red-500 bg-red-50';
        $actionBtnClass = 'inline-flex items-center justify-center rounded-lg px-10 py-3 text-sm font-bold text-white';
    @endphp

    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-700 hover:underline">Penerimaan Pengiriman</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="font-bold text-blue-600">Tambah Shipment Receipt</span>
        </div>
    </section>

    <form action="{{ route('store-shipment-receipt') }}" method="POST" enctype="multipart/form-data" class="space-y-5"
        x-data="shipmentReceiptForm({
            shipments: @js(
    $shipments->map(function ($shipment) {
        return [
            'id' => $shipment->id,
            'shipment_code' => $shipment->shipment_code,
            'shipment_type' => $shipment->shipment_type,
            'shipping_fleet' => $shipment->shipping_fleet,
            'contact' => $shipment->contact,
            'address' => $shipment->address,
            'notes' => $shipment->notes,
            'warehouse_name' => $shipment->warehouse?->name,
            'received_by_name' => $shipment->receivedBy?->name,
        ];
    }),
),
            selectedShipmentId: @js(old('shipment_id', '')),
            status: @js(old('status', '')),
            rejectReason: @js(old('reject_reason', '')),
            items: @js(old('items', [])),
            validationErrors: @js($errors->toArray()),
            successMessage: @js(session('success')),
            errorMessage: @js(session('error')),
        })" x-init="init()">
        @csrf

        <section class="{{ $sectionClass }}">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                <div>
                    <label class="{{ $labelClass }}">Tanggal Receipt</label>
                    <input type="date" value="{{ now()->format('Y-m-d') }}" readonly class="{{ $readonlyClass }}">
                </div>

                <div>
                    <label class="{{ $labelClass }}">Pilih Shipment</label>
                    <select name="shipment_id" id="shipment_id"
                        class="@error('shipment_id') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">
                        <option value="">-- Pilih Shipment --</option>
                        @foreach ($shipments as $shipment)
                            <option value="{{ $shipment->id }}" @selected(old('shipment_id') == $shipment->id)>
                                {{ $shipment->shipment_code ?? $shipment->id }} -
                                {{ $shipment->shipment_type ?? '-' }} -
                                {{ $shipment->warehouse->name ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    @error('shipment_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Status</label>
                    <select name="status" x-model="status"
                        class="@error('status') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">
                        <option value="diterima" @selected(old('status') == 'diterima')>Diterima</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Tanggal Diterima</label>
                    <input type="datetime-local" name="received_at"
                        value="{{ old('received_at', now()->format('Y-m-d\TH:i')) }}"
                        class="@error('received_at') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">
                    @error('received_at')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="status === 'rejected'" x-transition class="md:col-span-2">
                    <label class="{{ $labelClass }}">Alasan Penolakan</label>
                    <textarea name="reject_reason" rows="3" placeholder="Masukkan alasan penolakan" x-model="rejectReason"
                        :required="status === 'rejected'"
                        class="@error('reject_reason') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">{{ old('reject_reason') }}</textarea>
                    @error('reject_reason')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="selectedShipmentData">
                    <label class="{{ $labelClass }}">Kode Shipment</label>
                    <input type="text" :value="selectedShipmentData?.shipment_code ?? '-'" readonly
                        class="{{ $readonlyClass }}">
                </div>

                <div x-show="selectedShipmentData">
                    <label class="{{ $labelClass }}">Jenis Shipment</label>
                    <input type="text" :value="selectedShipmentData?.shipment_type ?? '-'" readonly
                        class="{{ $readonlyClass }}">
                </div>

                <div x-show="selectedShipmentData">
                    <label class="{{ $labelClass }}">Gudang Tujuan</label>
                    <input type="text" :value="selectedShipmentData?.warehouse_name ?? '-'" readonly
                        class="{{ $readonlyClass }}">
                </div>

                <div x-show="selectedShipmentData">
                    <label class="{{ $labelClass }}">Armada Pengiriman</label>
                    <input type="text" :value="selectedShipmentData?.shipping_fleet ?? '-'" readonly
                        class="{{ $readonlyClass }}">
                </div>

                <div x-show="selectedShipmentData">
                    <label class="{{ $labelClass }}">Kontak</label>
                    <input type="text" :value="selectedShipmentData?.contact ?? '-'" readonly
                        class="{{ $readonlyClass }}">
                </div>

                <div x-show="selectedShipmentData">
                    <label class="{{ $labelClass }}">Penerima Shipment</label>
                    <input type="text" :value="selectedShipmentData?.received_by_name ?? '-'" readonly
                        class="{{ $readonlyClass }}">
                </div>

                <div class="md:col-span-2" x-show="selectedShipmentData">
                    <label class="{{ $labelClass }}">Alamat Shipment</label>
                    <textarea rows="3" readonly class="{{ $readonlyClass }}" x-text="selectedShipmentData?.address ?? '-'"></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="{{ $labelClass }}">Catatan Receipt</label>
                    <textarea name="notes" rows="3" placeholder="Opsional"
                        class="@error('notes') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <section class="{{ $sectionClass }}">
            <div class="mb-4 flex items-center justify-between">
                <div class="text-xs font-bold text-gray-800">Daftar Item Shipment Receipt</div>
            </div>

            @error('items')
                <p class="mb-3 text-xs text-red-600">{{ $message }}</p>
            @enderror

            <div x-show="!selectedShipmentId" class="mb-4 text-xs text-gray-600">
                Pilih shipment terlebih dahulu agar daftar item muncul.
            </div>

            <div x-show="loading" class="mb-4 text-xs text-blue-600">
                Memuat item shipment...
            </div>

            <div class="space-y-4">
                <template x-for="(item, index) in items" :key="index">
                    <div class="rounded-lg border border-gray-300 p-4">
                        <input type="hidden" :name="`items[${index}][shipment_item_id]`" :value="item.shipment_item_id">

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-800">SKU</label>
                                <input type="text" :value="item.sku ?? '-'" readonly class="{{ $readonlyClass }}">
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-800">Produk</label>
                                <input type="text" :value="item.product_name ?? '-'" readonly
                                    class="{{ $readonlyClass }}">
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-800">Qty Dikirim</label>
                                <input type="text" :value="item.quantity ?? 0" readonly class="{{ $readonlyClass }}">
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-800">Qty Diterima</label>
                                <input type="number" min="0" :max="item.quantity ?? null"
                                    :name="`items[${index}][qty_received]`" x-model="item.qty_received"
                                    placeholder="Masukkan qty diterima"
                                    :class="fieldError(index, 'qty_received') ? 'border-red-500 bg-red-50' :
                                        'border-gray-400 bg-white'"
                                    class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0">

                                <template x-if="fieldError(index, 'qty_received')">
                                    <p class="mt-1 text-xs text-red-600" x-text="fieldError(index, 'qty_received')"></p>
                                </template>
                            </div>
                        </div>

                        <div x-show="Number(item.qty_received) > Number(item.quantity ?? 0)"
                            class="mt-2 text-[11px] text-gray-600">
                            Qty diterima tidak boleh melebihi qty dikirim.
                        </div>
                    </div>
                </template>
            </div>
        </section>

        <section class="{{ $sectionClass }}">
            <div class="mb-3 text-xs font-bold text-gray-800">
                Bukti Kerusakan (Opsional)
            </div>

            <input type="file" name="damage_proofs[]" id="damageProofsPond" multiple
                accept="image/png, image/jpeg, image/jpg, application/pdf">

            @error('damage_proofs')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
            @error('damage_proofs.*')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </section>

        <div class="flex items-center justify-end gap-4 pt-2">
            <a href="{{ route('admin.gudang-permintaan-pengiriman') }}"
                class="{{ $actionBtnClass }} bg-red-600 hover:bg-red-700">
                Batal
            </a>

            <button type="submit" class="{{ $actionBtnClass }} bg-[#2D2ACD] hover:bg-blue-800">
                Simpan
            </button>
        </div>
    </form>
@endsection

@section('addJs')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>

    <script>
        function shipmentReceiptForm(config) {
            return {
                shipments: Array.isArray(config.shipments) ? config.shipments : [],
                selectedShipmentId: config.selectedShipmentId || '',
                status: config.status || '',
                rejectReason: config.rejectReason || '',
                items: Array.isArray(config.items) ? config.items : [],
                validationErrors: config.validationErrors || {},
                successMessage: config.successMessage || '',
                errorMessage: config.errorMessage || '',
                loading: false,
                pond: null, 

                init() {
                    if (this.successMessage) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: this.successMessage,
                            confirmButtonColor: '#2D2ACD'
                        });
                    }

                    if (this.errorMessage) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: this.errorMessage,
                            confirmButtonColor: '#dc2626'
                        });
                    }

                    this.$nextTick(() => {
                        this.initSelect2();
                        this.initFilePond(); 
                    });

                    if (this.selectedShipmentId && this.items.length === 0) {
                        this.fetchShipmentItems(this.selectedShipmentId);
                    }
                },

                initSelect2() {
                    const self = this;
                    const el = window.jQuery('#shipment_id');

                    if (!el.length) return;

                    el.select2({
                        placeholder: '-- Pilih Shipment --',
                        width: '100%'
                    });

                    if (this.selectedShipmentId) {
                        el.val(String(this.selectedShipmentId)).trigger('change');
                    }

                    el.on('change', function() {
                        const shipmentId = window.jQuery(this).val();
                        self.selectedShipmentId = shipmentId;
                        self.handleShipmentChange(shipmentId);
                    });
                },

                get selectedShipmentData() {
                    return this.shipments.find(shipment =>
                        String(shipment.id) === String(this.selectedShipmentId)
                    ) || null;
                },

                async handleShipmentChange(shipmentId) {
                    this.items = [];
                    if (!shipmentId) return;
                    await this.fetchShipmentItems(shipmentId);
                },

                async fetchShipmentItems(shipmentId) {
                    this.loading = true;

                    try {
                        const response = await fetch(`/admin/shipments/${shipmentId}/items`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        this.items = (Array.isArray(result) ? result : []).map(item => ({
                            shipment_item_id: item.id,
                            product_stock_id: item.product_stock_id ?? '',
                            sku: item.product_stock?.product_variant?.sku ?? '-',
                            product_name: item.product_stock?.product_variant?.name ?? '-',
                            stock: item.product_stock?.stock ?? 0,
                            quantity: item.quantity ?? 0,
                            qty_received: item.quantity ?? 0,
                            notes: item.notes ?? ''
                        }));
                    } catch (error) {
                        console.error(error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal mengambil item shipment',
                            confirmButtonColor: '#dc2626'
                        });
                    } finally {
                        this.loading = false;
                    }
                },

                fieldError(index, field) {
                    const key = `items.${index}.${field}`;
                    return this.validationErrors[key]?.[0] || '';
                },

                initFilePond() {
                    const input = document.getElementById('damageProofsPond');

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
                                <p class="text-base font-bold text-gray-700"><span class="filepond--label-action">Klik</span> atau Tarik gambar ke sini</p>
                                <p class="text-xs text-gray-500 mt-1 font-medium">PNG, JPG, JPEG, PDF (Maks 3MB/file)</p>
                            </div>

                            <div class="fp-text-mini hidden cursor-pointer hover:underline text-blue-600">
                                <p class="text-sm font-bold m-0 p-0">+ Tambah Gambar Lain</p>
                            </div>
                        </div>
                    `;

                    this.pond = FilePond.create(input, {
                        storeAsFile: true,
                        allowMultiple: true,
                        maxFiles: 5,
                        credits: false,
                        acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf'],
                        maxFileSize: '3MB',

                        labelIdle: customIconPlaceholder,
                        labelFileTypeNotAllowed: 'Format file tidak didukung',
                        fileValidateTypeLabelExpectedTypes: 'Hanya PNG/JPG/JPEG/PDF',
                        labelMaxFileSizeExceeded: 'Ukuran file terlalu besar',
                        labelMaxFileSize: 'Maksimum 3MB',

                        onupdatefiles: (files) => {
                            const rootElement = document.getElementById('damageProofsPond').closest(
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
                }
            }
        }
    </script>
@endsection
