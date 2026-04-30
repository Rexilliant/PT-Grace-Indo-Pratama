@extends('admin.layout.master')

@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-permintaan-pengiriman', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('addCss')
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">

    <style>
        [x-cloak] {
            display: none !important;
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

        $status = old('status', $shipment->status ?? 'Menunggu');
        $statusLower = strtolower($status);

        $statusDate = null;
        $statusUser = '-';

        if ($statusLower === 'ditolak') {
            $statusDate = $shipment->rejected_at;
            $statusUser = optional($shipment->rejectedBy)->name ?? '-';
        } elseif (in_array($statusLower, ['disetujui', 'dikirim', 'selesai'])) {
            $statusDate = $shipment->approved_at;
            $statusUser = optional($shipment->approvedBy)->name ?? '-';
        }

        $canEditShipment = auth()->user()->can('edit pengiriman produk');
        $canEditShipmentStatus = auth()->user()->can('edit status pengiriman produk');
        $canSendShipmentOnly = auth()->user()->can('kirim pengiriman produk');

        /**
         * RULE:
         * - Detail hanya boleh diedit saat status Menunggu + punya permission edit pengiriman produk
         * - Status Menunggu:
         *      - yg punya edit status => bisa ke Disetujui / Ditolak
         * - Status Disetujui:
         *      - yg punya edit status ATAU kirim pengiriman produk => bisa ke Dikirim
         * - Status Ditolak:
         *      - lock total, tidak bisa balik ke Menunggu
         * - Status Dikirim:
         *      - lock total, tidak bisa diubah apa pun lagi
         * - Status Selesai:
         *      - lock total
         */
        $isDetailEditable = $status === 'Menunggu' && $canEditShipment;

        $statusOptions = [$status];
        $canChangeStatus = false;

        if ($status === 'Menunggu' && $canEditShipmentStatus) {
            $statusOptions = ['Menunggu', 'Disetujui', 'Ditolak'];
            $canChangeStatus = true;
        } elseif ($status === 'Disetujui' && ($canEditShipmentStatus || $canSendShipmentOnly)) {
            $statusOptions = ['Disetujui', 'Dikirim'];
            $canChangeStatus = true;
        } else {
            $statusOptions = [$status];
            $canChangeStatus = false;
        }

        $isStatusLocked = !$canChangeStatus;

        $initialItems = old(
            'items',
            $shipment->shipmentItems
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_stock_id' => $item->product_stock_id,
                        'quantity' => $item->quantity,
                        'sku' => $item->productStock->productVariant->sku ?? '-',
                        'product_name' => $item->productStock->productVariant->name ?? '-',
                        'warehouse_name' =>
                            $item->productStock->warehouse->name ?? ($item->productStock->province ?? '-'),
                        'stock' => $item->productStock->stock ?? 0,
                    ];
                })
                ->values()
                ->toArray(),
        );

        $productStocks = $productStocks ?? collect();
        $productStockOptions = $productStocks
            ->map(function ($stock) {
                return [
                    'id' => $stock->id,
                    'sku' => $stock->productVariant->sku ?? '-',
                    'product_name' => $stock->productVariant->name ?? '-',
                    'warehouse_name' => $stock->warehouse->name ?? ($stock->province ?? '-'),
                    'stock' => $stock->stock ?? 0,
                    'label' => ($stock->productVariant->sku ?? '-') . ' - ' . ($stock->productVariant->name ?? '-'),
                ];
            })
            ->values();

        $canUploadInvoice = $status === 'Disetujui' && ($canEditShipmentStatus || $canSendShipmentOnly);
        $canEditShipmentDate = $status === 'Disetujui' && ($canEditShipmentStatus || $canSendShipmentOnly);
        $showSubmitButton = $isDetailEditable || $canChangeStatus;
    @endphp

    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span>Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <span>Permintaan Pengiriman</span>
            <span class="mx-1 text-gray-400">›</span>
            <span class="font-bold text-blue-600">Detail Pengiriman</span>
        </div>
    </section>

    <div x-data="shipmentEditForm({
        status: @js(old('status', $status)),
        successMessage: @js(session('success')),
        errorMessage: @js(session('error')),
        canEditDetail: @js($isDetailEditable),
        items: @js($initialItems),
        productStocks: @js($productStockOptions),
        validationErrors: @js($errors->toArray()),
    })" x-init="init()">
        <form action="{{ route('update-shipment', ['id' => $shipment->id]) }}" method="POST" enctype="multipart/form-data"
            class="space-y-5">
            @csrf
            @method('PUT')

            {{-- INFORMASI UTAMA --}}
            <section class="{{ $sectionClass }}">
                <div class="mb-4 text-sm font-bold text-gray-800">
                    ID Pengajuan: {{ $shipment->shipment_code }}
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                    <div>
                        <label class="{{ $labelClass }}">Tanggal Pengajuan</label>
                        <input type="text" value="{{ $shipment->created_at?->format('Y-m-d') }}" readonly
                            class="{{ $readonlyClass }}">
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Tanggal Permintaan Pengiriman</label>
                        <input type="date" name="shipment_request_at"
                            value="{{ old('shipment_request_at', $shipment->shipment_request_at?->format('Y-m-d')) }}"
                            @if (!$isDetailEditable) readonly disabled @endif
                            class="@error('shipment_request_at') {{ $inputErrorClass }} @else {{ $isDetailEditable ? $inputNormalClass : $readonlyClass }} @enderror">

                        @if (!$isDetailEditable)
                            <input type="hidden" name="shipment_request_at"
                                value="{{ old('shipment_request_at', $shipment->shipment_request_at?->format('Y-m-d')) }}">
                        @endif

                        @error('shipment_request_at')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Penanggung Jawab</label>
                        <input type="text" value="{{ $shipment->personResponsible->name ?? '-' }}" readonly
                            class="{{ $readonlyClass }}">
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Jenis Pengiriman</label>
                        <input type="text" name="shipment_type"
                            value="{{ old('shipment_type', $shipment->shipment_type) }}"
                            @if (!$isDetailEditable) readonly @endif
                            class="@error('shipment_type') {{ $inputErrorClass }} @else {{ $isDetailEditable ? $inputNormalClass : $readonlyClass }} @enderror">
                        @error('shipment_type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Gudang / Tujuan</label>
                        <input type="text" value="{{ $shipment->warehouse->name ?? ($shipment->province ?? '-') }}"
                            readonly class="{{ $readonlyClass }}">
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Armada Pengiriman</label>
                        <input type="text" name="shipping_fleet"
                            value="{{ old('shipping_fleet', $shipment->shipping_fleet) }}"
                            @if (!$isDetailEditable) readonly @endif
                            class="@error('shipping_fleet') {{ $inputErrorClass }} @else {{ $isDetailEditable ? $inputNormalClass : $readonlyClass }} @enderror">
                        @error('shipping_fleet')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Nama Penerima</label>
                        <input type="text" value="{{ $shipment->receivedBy->name ?? '-' }}" readonly
                            class="{{ $readonlyClass }}">
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Kontak Penerima</label>
                        <input type="text" name="contact" value="{{ old('contact', $shipment->contact) }}"
                            @if (!$isDetailEditable) readonly @endif
                            class="@error('contact') {{ $inputErrorClass }} @else {{ $isDetailEditable ? $inputNormalClass : $readonlyClass }} @enderror">
                        @error('contact')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="{{ $labelClass }}">Alamat Lengkap</label>
                        <textarea name="address" rows="3" @if (!$isDetailEditable) readonly @endif
                            class="@error('address') {{ $inputErrorClass }} @else {{ $isDetailEditable ? $inputNormalClass : $readonlyClass }} @enderror">{{ old('address', $shipment->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="{{ $labelClass }}">Catatan</label>
                        <textarea name="notes" rows="3" @if (!$isDetailEditable) readonly @endif
                            class="@error('notes') {{ $inputErrorClass }} @else {{ $isDetailEditable ? $inputNormalClass : $readonlyClass }} @enderror">{{ old('notes', $shipment->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- ITEM PENGIRIMAN --}}
            <section class="{{ $sectionClass }}">
                <div class="mb-4 flex items-center justify-between">
                    <div class="text-xs font-bold text-gray-800">Daftar Item Pengiriman</div>

                    @if ($isDetailEditable)
                        <button type="button" @click="addItem()"
                            class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-4 py-2 text-xs font-bold text-white hover:bg-blue-800">
                            + Tambah Item
                        </button>
                    @endif
                </div>

                @error('items')
                    <p class="mb-3 text-xs text-red-600">{{ $message }}</p>
                @enderror

                <div class="space-y-4">
                    <template x-for="(item, index) in items" :key="item.key">
                        <div class="rounded-lg border border-gray-300 p-4">
                            <input type="hidden" :name="`items[${index}][id]`" :value="item.id || ''">

                            <div class="mb-4 flex items-center justify-between">
                                <div class="text-sm font-bold text-gray-800">
                                    Item <span x-text="index + 1"></span>
                                </div>

                                <template x-if="canEditDetail">
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                        class="inline-flex items-center justify-center rounded-lg bg-red-600 px-3 py-2 text-xs font-bold text-white hover:bg-red-700">
                                        Hapus
                                    </button>
                                </template>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                                <div>
                                    <label class="mb-2 block text-xs font-bold text-gray-800">Produk</label>
                                    <template x-if="canEditDetail">
                                        <select :name="`items[${index}][product_stock_id]`" x-model="item.product_stock_id"
                                            @change="syncProductMeta(index)"
                                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0">
                                            <option value="">-- Pilih Produk --</option>
                                            <template x-for="stock in productStocks" :key="stock.id">
                                                <option :value="String(stock.id)" x-text="stock.label"></option>
                                            </template>
                                        </select>
                                    </template>

                                    <template x-if="!canEditDetail">
                                        <input type="text" :value="`${item.sku ?? '-'} - ${item.product_name ?? '-'}`"
                                            readonly class="{{ $readonlyClass }}">
                                    </template>

                                    <template x-if="fieldError(index, 'product_stock_id')">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="fieldError(index, 'product_stock_id')"></p>
                                    </template>
                                </div>

                                <div>
                                    <label class="mb-2 block text-xs font-bold text-gray-800">Gudang Stok</label>
                                    <input type="text" :value="item.warehouse_name ?? '-'" readonly
                                        class="{{ $readonlyClass }}">
                                </div>

                                <div>
                                    <label class="mb-2 block text-xs font-bold text-gray-800">Stok Saat Ini</label>
                                    <input type="text" :value="item.stock ?? 0" readonly class="{{ $readonlyClass }}">
                                </div>

                                <div>
                                    <label class="mb-2 block text-xs font-bold text-gray-800">Jumlah</label>
                                    <input type="number" min="1" :name="`items[${index}][quantity]`"
                                        x-model="item.quantity" @if (!$isDetailEditable) readonly @endif
                                        :class="fieldError(index, 'quantity') ? 'border-red-500 bg-red-50' :
                                            'border-gray-400 bg-white'"
                                        class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0">

                                    <template x-if="fieldError(index, 'quantity')">
                                        <p class="mt-1 text-xs text-red-600" x-text="fieldError(index, 'quantity')"></p>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            {{-- STATUS --}}
            <section class="{{ $sectionClass }}">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6">
                    <div>
                        <label class="{{ $labelClass }}">Tanggal Ubah Status</label>
                        <input type="text" readonly value="{{ $statusDate ? $statusDate->format('d-m-Y') : '-' }}"
                            class="{{ $readonlyClass }}">
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Penanggung Jawab Status</label>
                        <input type="text" readonly value="{{ $statusUser }}" class="{{ $readonlyClass }}">
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Status Permintaan</label>
                        <select name="status" x-model="statusPermintaan"
                            class="@error('status') {{ $inputErrorClass }} @else {{ $isStatusLocked ? $readonlyClass : $inputNormalClass }} @enderror"
                            @if ($isStatusLocked) disabled @endif>
                            @foreach ($statusOptions as $statusOption)
                                <option value="{{ $statusOption }}"
                                    {{ old('status', $status) == $statusOption ? 'selected' : '' }}>
                                    {{ $statusOption }}
                                </option>
                            @endforeach
                        </select>

                        @if ($isStatusLocked)
                            <input type="hidden" name="status" value="{{ old('status', $status) }}">
                        @endif

                        @error('status')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- ALASAN --}}
            <section class="{{ $sectionClass }}" x-show="showReason" x-cloak>
                <label class="{{ $labelClass }}">Alasan</label>
                <textarea name="reason" rows="3"
                    @if (!($status === 'Menunggu' && $canEditShipmentStatus) || $shipment->reason) readonly @endif
                    class="@error('reason') {{ $inputErrorClass }} @else {{ ($status === 'Menunggu' && $canEditShipmentStatus && !$shipment->reason) ? $inputNormalClass : $readonlyClass }} @enderror">{{ old('reason', $shipment->reason) }}</textarea>
                @error('reason')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </section>

            {{-- INVOICE + TANGGAL PENGIRIMAN --}}
            <section class="{{ $sectionClass }}" x-show="showInvoiceSection" x-cloak>
                <div class="mb-5 overflow-hidden rounded-lg border border-gray-400 shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-800">
                            <thead class="bg-[#5aba6f]/70 text-gray-900">
                                <tr>
                                    <th class="px-6 py-4 text-left font-extrabold">File</th>
                                    <th class="px-6 py-4 text-left font-extrabold">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-500 bg-gray-200">
                                @forelse ($invoices as $invoice)
                                    <tr class="hover:bg-gray-300">
                                        <td class="px-6 py-4 font-semibold">
                                            <a target="_blank" href="{{ $invoice->getUrl() }}">
                                                {{ $invoice->file_name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-3">
                                            <div class="flex items-center justify-start gap-6 font-semibold">
                                                @if ($canUploadInvoice)
                                                    <button type="button" class="text-[#EC0000] hover:underline"
                                                        @click="confirmDelete('{{ route('media.delete', ['mediaId' => $invoice->id]) }}')">
                                                        Hapus
                                                    </button>
                                                @else
                                                    <span class="text-gray-500">-</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-6 py-4 text-center text-gray-500">
                                            Belum ada invoice
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <label class="{{ $labelClass }}">Tanggal Pengiriman</label>
                    <input type="date" name="shipment_at"
                        value="{{ old('shipment_at', $shipment->shipment_at?->format('Y-m-d')) }}"
                        @if (!$canEditShipmentDate || $shipment->shipment_at) readonly disabled @endif
                        class="@error('shipment_at') {{ $inputErrorClass }} @else {{ $canEditShipmentDate && !$shipment->shipment_at ? $inputNormalClass : $readonlyClass }} @enderror">

                    @if (!$canEditShipmentDate || $shipment->shipment_at)
                        <input type="hidden" name="shipment_at"
                            value="{{ old('shipment_at', $shipment->shipment_at?->format('Y-m-d')) }}">
                    @endif

                    @error('shipment_at')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if ($canUploadInvoice)
                    <div class="mt-5">
                        <label class="mb-3 block text-sm font-bold text-gray-800">Invoice Pembelian Barang</label>

                        <input x-ref="invoices" type="file" name="invoices[]" multiple
                            accept="image/png,image/jpeg,application/pdf">

                        <p class="mt-2 text-xs text-gray-600">
                            Format: PNG/JPG/JPEG/PDF • Maks 3MB per file • Bisa upload multiple.
                        </p>

                        @error('invoices')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        @error('invoices.*')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </section>

            @if ($showSubmitButton && $status !== 'Dikirim' && $status !== 'Ditolak' && $status !== 'Selesai')
                <div class="flex justify-end pt-2">
                    <button type="submit" class="{{ $actionBtnClass }} bg-[#2D2ACD] hover:bg-blue-800">
                        Simpan
                    </button>
                </div>
            @endif
        </form>

        <form x-ref="deleteMediaForm" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
@endsection

@section('addJs')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>

    <script>
        function shipmentEditForm(config) {
            return {
                statusPermintaan: config.status || 'Menunggu',
                successMessage: config.successMessage || '',
                errorMessage: config.errorMessage || '',
                pond: null,
                canEditDetail: !!config.canEditDetail,
                items: Array.isArray(config.items) ? config.items.map(item => ({
                    key: crypto.randomUUID(),
                    id: item.id ?? '',
                    product_stock_id: item.product_stock_id ? String(item.product_stock_id) : '',
                    quantity: item.quantity ?? '',
                    sku: item.sku ?? '-',
                    product_name: item.product_name ?? '-',
                    warehouse_name: item.warehouse_name ?? '-',
                    stock: item.stock ?? 0,
                })) : [],
                productStocks: Array.isArray(config.productStocks) ? config.productStocks : [],
                validationErrors: config.validationErrors || {},

                get showInvoiceSection() {
                    const status = (this.statusPermintaan || '').toLowerCase();
                    return status === 'dikirim' || status === 'selesai';
                },

                get showReason() {
                    return (this.statusPermintaan || '').toLowerCase() === 'ditolak';
                },

                init() {
                    if (!this.items.length) {
                        this.items.push(this.emptyItem());
                    }

                    this.items.forEach((item, index) => {
                        this.syncProductMeta(index);
                    });

                    this.$nextTick(() => this.initFilePond());

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

                emptyItem() {
                    return {
                        key: crypto.randomUUID(),
                        id: '',
                        product_stock_id: '',
                        quantity: '',
                        sku: '-',
                        product_name: '-',
                        warehouse_name: '-',
                        stock: 0,
                    };
                },

                addItem() {
                    if (!this.canEditDetail) return;
                    this.items.push(this.emptyItem());
                },

                removeItem(index) {
                    if (!this.canEditDetail) return;
                    this.items.splice(index, 1);

                    if (this.items.length === 0) {
                        this.items.push(this.emptyItem());
                    }
                },

                syncProductMeta(index) {
                    const item = this.items[index];
                    const selected = this.productStocks.find(stock => String(stock.id) === String(item.product_stock_id));

                    if (!selected) {
                        this.items[index].sku = item.sku ?? '-';
                        this.items[index].product_name = item.product_name ?? '-';
                        this.items[index].warehouse_name = item.warehouse_name ?? '-';
                        this.items[index].stock = item.stock ?? 0;
                        return;
                    }

                    this.items[index].sku = selected.sku ?? '-';
                    this.items[index].product_name = selected.product_name ?? '-';
                    this.items[index].warehouse_name = selected.warehouse_name ?? '-';
                    this.items[index].stock = selected.stock ?? 0;
                },

                fieldError(index, field) {
                    const key = `items.${index}.${field}`;
                    return this.validationErrors[key]?.[0] || '';
                },

                confirmDelete(url) {
                    Swal.fire({
                        title: 'Yakin hapus file ini?',
                        text: 'File invoice akan dihapus permanen.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.$refs.deleteMediaForm.action = url;
                            this.$refs.deleteMediaForm.submit();
                        }
                    });
                },

                initFilePond() {
                    const input = this.$refs.invoices;

                    if (!input || typeof FilePond === 'undefined' || this.pond) {
                        return;
                    }

                    FilePond.registerPlugin(
                        FilePondPluginFileValidateType,
                        FilePondPluginFileValidateSize,
                        FilePondPluginImagePreview
                    );

                    this.pond = FilePond.create(input, {
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
                        labelMaxFileSize: 'Maksimum 3MB'
                    });
                }
            }
        }
    </script>
@endsection