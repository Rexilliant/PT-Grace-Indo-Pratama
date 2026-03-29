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

        $currentStatus = $shipmentReceipt->status ?? 'received';
        $isLocked = $currentStatus !== 'received';
    @endphp

    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-700 hover:underline">Penerimaan Pengiriman</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="font-bold text-blue-600">Edit Shipment Receipt</span>
        </div>
    </section>

    <form action="{{ route('update-shipment-receipt', $shipmentReceipt->id) }}" method="POST" enctype="multipart/form-data"
        class="space-y-5" x-data="shipmentReceiptEditForm({
            shipment: @js([
    'id' => $shipmentReceipt->shipment?->id,
    'shipment_code' => $shipmentReceipt->shipment?->shipment_code,
    'shipment_type' => $shipmentReceipt->shipment?->shipment_type,
    'shipping_fleet' => $shipmentReceipt->shipment?->shipping_fleet,
    'contact' => $shipmentReceipt->shipment?->contact,
    'address' => $shipmentReceipt->shipment?->address,
    'notes' => $shipmentReceipt->shipment?->notes,
    'warehouse_name' => $shipmentReceipt->shipment?->warehouse?->name,
    'received_by_name' => $shipmentReceipt->shipment?->receivedBy?->name,
]),
            items: @js(
    $shipmentReceipt->items->map(function ($item) {
        return [
            'shipment_receipt_item_id' => $item->id,
            'shipment_item_id' => $item->shipment_item_id,
            'sku' => $item->shipmentItem?->productStock?->productVariant?->sku ?? '-',
            'product_name' => $item->shipmentItem?->productStock?->productVariant?->name ?? '-',
            'quantity' => $item->shipmentItem?->quantity ?? 0,
            'qty_received' => $item->qty_received ?? 0,
            'notes' => $item->notes ?? '',
        ];
    }),
),
            status: @js($shipmentReceipt->status ?? 'received'),
            receivedAt: @js(optional($shipmentReceipt->received_at)->format('Y-m-d\TH:i')),
            notes: @js($shipmentReceipt->notes),
            rejectReason: @js($shipmentReceipt->reject_reason ?? ''),
            validationErrors: @js($errors->toArray()),
            successMessage: @js(session('success')),
            errorMessage: @js(session('error')),
            isLocked: @js($isLocked),
        })" x-init="init()">
        @csrf
        @method('PUT')

        <input type="hidden" name="shipment_id" value="{{ $shipmentReceipt->shipment_id }}">

        <section class="{{ $sectionClass }}">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                <div>
                    <label class="{{ $labelClass }}">Tanggal Receipt</label>
                    <input type="date" value="{{ optional($shipmentReceipt->created_at)->format('Y-m-d') }}" readonly
                        class="{{ $readonlyClass }}">
                </div>

                <div>
                    <label class="{{ $labelClass }}">Kode Shipment</label>
                    <input type="text" :value="shipment?.shipment_code ?? '-'" readonly class="{{ $readonlyClass }}">
                </div>

                <div>
                    <label class="{{ $labelClass }}">Status</label>
                    <select name="status" x-model="status"
                        class="@error('status') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">
                        <option value="received">Received</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Tanggal Diterima</label>
                    <input type="datetime-local" name="received_at" x-model="receivedAt" :readonly="isLocked"
                        :class="isLocked ? '{{ $readonlyClass }}' :
                            '@error('received_at') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror'">
                    @error('received_at')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="status === 'rejected'" x-transition class="md:col-span-2">
                    <label class="{{ $labelClass }}">Alasan Penolakan</label>
                    <textarea name="reject_reason" rows="3" x-model="rejectReason" placeholder="Masukkan alasan penolakan"
                        :required="status === 'rejected'"
                        class="@error('reject_reason') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror"></textarea>
                    @error('reject_reason')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="shipment">
                    <label class="{{ $labelClass }}">Jenis Shipment</label>
                    <input type="text" :value="shipment?.shipment_type ?? '-'" readonly class="{{ $readonlyClass }}">
                </div>
                <div x-show="shipment">
                    <label class="{{ $labelClass }}">Gudang Tujuan</label>
                    <input type="text" :value="shipment?.warehouse_name ?? '-'" readonly class="{{ $readonlyClass }}">
                </div>

                <div x-show="shipment">
                    <label class="{{ $labelClass }}">Armada Pengiriman</label>
                    <input type="text" :value="shipment?.shipping_fleet ?? '-'" readonly class="{{ $readonlyClass }}">
                </div>

                <div x-show="shipment">
                    <label class="{{ $labelClass }}">Kontak</label>
                    <input type="text" :value="shipment?.contact ?? '-'" readonly class="{{ $readonlyClass }}">
                </div>

                <div x-show="shipment">
                    <label class="{{ $labelClass }}">Penerima Shipment</label>
                    <input type="text" :value="shipment?.received_by_name ?? '-'" readonly class="{{ $readonlyClass }}">
                </div>

                <div class="md:col-span-2" x-show="shipment">
                    <label class="{{ $labelClass }}">Alamat Shipment</label>
                    <textarea rows="3" readonly class="{{ $readonlyClass }}" x-text="shipment?.address ?? '-'"></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="{{ $labelClass }}">Catatan Receipt</label>
                    <textarea name="notes" rows="3" placeholder="Opsional" x-model="notes" :readonly="isLocked"
                        :class="isLocked ? '{{ $readonlyClass }}' :
                            '@error('notes') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror'"></textarea>
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

            <div class="space-y-4">
                <template x-for="(item, index) in items" :key="item.shipment_receipt_item_id ?? index">
                    <div class="rounded-lg border border-gray-300 p-4">
                        <input type="hidden" :name="`items[${index}][shipment_receipt_item_id]`"
                            :value="item.shipment_receipt_item_id">
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
                                    placeholder="Masukkan qty diterima" :readonly="isLocked"
                                    :class="isLocked ? '{{ $readonlyClass }}' :
                                        (fieldError(index, 'qty_received') ? 'border-red-500 bg-red-50' :
                                            'border-gray-400 bg-white')"
                                    class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0">

                                <template x-if="fieldError(index, 'qty_received') && !isLocked">
                                    <p class="mt-1 text-xs text-red-600" x-text="fieldError(index, 'qty_received')"></p>
                                </template>
                            </div>
                        </div>
                        <div x-show="Number(item.qty_received) > Number(item.quantity ?? 0) && !isLocked"
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
                Update
            </button>
        </div>
    </form>
@endsection

@section('addJs')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function shipmentReceiptEditForm(config) {
            return {
                shipment: config.shipment || null,
                items: Array.isArray(config.items) ? config.items : [],
                status: config.status || 'received',
                receivedAt: config.receivedAt || '',
                notes: config.notes || '',
                rejectReason: config.rejectReason || '',
                isLocked: !!config.isLocked,
                validationErrors: config.validationErrors || {},
                successMessage: config.successMessage || '',
                errorMessage: config.errorMessage || '',

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
                },

                fieldError(index, field) {
                    const key = `items.${index}.${field}`;
                    return this.validationErrors[key]?.[0] || '';
                },
            }
        }
    </script>
@endsection
