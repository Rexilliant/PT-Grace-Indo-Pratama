@extends('admin.layout.master')

@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-penerimaan-pengiriman-produk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')


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
                        <option value="received" @selected(old('status') == 'received')>Received</option>
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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

                    if (!shipmentId) {
                        return;
                    }

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
            }
        }
    </script>
@endsection
