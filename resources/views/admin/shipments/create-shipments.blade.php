@extends('admin.layout.master')

@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-permintaan-pengiriman', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

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
            <a href="#" class="text-gray-700 hover:underline">Permintaan Pengiriman</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="font-bold text-blue-600">Tambah Pengiriman</span>
        </div>
    </section>

    <form action="{{ route('store-shipment') }}" method="POST" enctype="multipart/form-data" class="space-y-5"
        x-data="shipmentForm({
            stockWarehouse: @js(old('stock_warehouse', '')),
            productStocks: @js($productStocks),
            items: @js(old('items', [['product_stock_id' => '', 'quantity' => 1]])),
            validationErrors: @js($errors->toArray()),
            successMessage: @js(session('success')),
            errorMessage: @js(session('error')),
        })" x-init="init()">
        @csrf

        <section class="{{ $sectionClass }}">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                <div>
                    <label class="{{ $labelClass }}">Tanggal Pengajuan</label>
                    <input type="date" value="{{ now()->format('Y-m-d') }}" readonly class="{{ $readonlyClass }}">
                </div>

                <div>
                    <label class="{{ $labelClass }}">Tanggal Permintaan Pengiriman</label>
                    <input type="date" name="shipment_request_at" value="{{ old('shipment_request_at') }}"
                        class="@error('shipment_request_at') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">
                    @error('shipment_request_at')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Penanggung Jawab</label>
                    <input type="text" value="{{ auth()->user()->name }}" readonly class="{{ $readonlyClass }}">
                    <input type="hidden" name="person_responsible_id" value="{{ auth()->id() }}">
                    @error('person_responsible_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Jenis Pengiriman</label>
                    <input type="text" name="shipment_type" value="{{ old('shipment_type') }}"
                        class="@error('shipment_type') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">
                    @error('shipment_type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Gudang Tujuan</label>
                    <select name="warehouse_id"
                        class="@error('warehouse_id') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">
                        <option value="">-- Pilih Gudang Tujuan --</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('warehouse_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Armada Pengiriman</label>
                    <input type="text" name="shipping_fleet" value="{{ old('shipping_fleet') }}"
                        class="@error('shipping_fleet') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">
                    @error('shipping_fleet')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Nama Penerima</label>
                    <select name="received_by_id"
                        class="@error('received_by_id') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">
                        <option value="">-- Pilih Penerima --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('received_by_id') == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('received_by_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Kontak Penerima</label>
                    <input type="text" name="contact" value="{{ old('contact') }}"
                        class="@error('contact') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">
                    @error('contact')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="{{ $labelClass }}">Alamat Lengkap</label>
                    <textarea rows="3" name="address"
                        class="@error('address') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="{{ $labelClass }}">Catatan</label>
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
                <div class="text-xs font-bold text-gray-800">Daftar Item Pengiriman</div>
                <button type="button" @click="addItem"
                    class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-4 py-2 text-sm font-bold text-white hover:bg-blue-800">
                    + Tambah Item
                </button>
            </div>

            <div class="mb-4">
                <label class="{{ $labelClass }}">Gudang Stok</label>
                <select x-model="stockWarehouse" @change="resetItems"
                    class="@error('stock_warehouse') {{ $inputErrorClass }} @else {{ $inputNormalClass }} @enderror">
                    <option value="">-- Pilih Gudang Stok --</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>

                <input type="hidden" name="stock_warehouse" :value="stockWarehouse">

                @error('stock_warehouse')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @error('items')
                <p class="mb-3 text-xs text-red-600">{{ $message }}</p>
            @enderror

            <div x-show="!stockWarehouse" class="mb-4 text-xs text-gray-600">
                Pilih gudang stok terlebih dahulu agar daftar produk muncul.
            </div>

            <div class="space-y-4">
                <template x-for="(item, index) in items" :key="index">
                    <div class="rounded-lg border border-gray-300 p-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-800">Produk</label>
                                <select :name="`items[${index}][product_stock_id]`" x-model="item.product_stock_id"
                                    :disabled="!stockWarehouse"
                                    :class="fieldError(index, 'product_stock_id') ? 'border-red-500 bg-red-50' :
                                        'border-gray-400 bg-white'"
                                    class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 disabled:bg-gray-100">
                                    <option value="">-- Pilih Produk --</option>
                                    <template x-for="stock in availableStocks" :key="stock.id">
                                        <option :value="stock.id"
                                            x-text="`${stock.product_variant?.sku ?? '-'} - ${stock.product_variant?.name ?? '-'}`">
                                        </option>
                                    </template>
                                </select>

                                <template x-if="fieldError(index, 'product_stock_id')">
                                    <p class="mt-1 text-xs text-red-600" x-text="fieldError(index, 'product_stock_id')">
                                    </p>
                                </template>
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-800">Stok Tersedia</label>
                                <input type="text" :value="getSelectedStock(item.product_stock_id)?.stock ?? 0" readonly
                                    class="{{ $readonlyClass }}">
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-800">Jumlah</label>
                                <input type="number" min="1"
                                    :max="getSelectedStock(item.product_stock_id)?.stock ?? null"
                                    :name="`items[${index}][quantity]`" x-model="item.quantity"
                                    placeholder="Masukkan jumlah"
                                    :class="fieldError(index, 'quantity') ? 'border-red-500 bg-red-50' :
                                        'border-gray-400 bg-white'"
                                    class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0">

                                <template x-if="fieldError(index, 'quantity')">
                                    <p class="mt-1 text-xs text-red-600" x-text="fieldError(index, 'quantity')"></p>
                                </template>
                            </div>

                            <div class="flex items-end gap-3">
                                <button type="button" @click="removeItem(index)"
                                    class="inline-flex w-full items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">
                                    Hapus
                                </button>
                            </div>
                        </div>

                        <div x-show="item.product_stock_id && Number(item.quantity) > Number(getSelectedStock(item.product_stock_id)?.stock ?? 0)"
                            class="mt-2 text-[11px] text-gray-600">
                            Jumlah melebihi stok tersedia.
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function shipmentForm(config) {
            return {
                stockWarehouse: config.stockWarehouse || '',
                productStocks: Array.isArray(config.productStocks) ? config.productStocks : [],
                items: Array.isArray(config.items) && config.items.length ?
                    config.items :
                    [{
                        product_stock_id: '',
                        quantity: 1
                    }],
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

                    console.log('productStocks:', this.productStocks);
                },

                get availableStocks() {
                    if (!this.stockWarehouse) return [];

                    return this.productStocks.filter(stock =>
                        String(stock.warehouse_id) === String(this.stockWarehouse)
                    );
                },

                addItem() {
                    this.items.push({
                        product_stock_id: '',
                        quantity: 1,
                    });
                },

                removeItem(index) {
                    if (this.items.length === 1) {
                        this.items = [{
                            product_stock_id: '',
                            quantity: 1
                        }];
                        return;
                    }

                    this.items.splice(index, 1);
                },

                resetItems() {
                    this.items = this.items.map(() => ({
                        product_stock_id: '',
                        quantity: 1,
                    }));
                },

                getSelectedStock(productStockId) {
                    return this.productStocks.find(
                        stock => String(stock.id) === String(productStockId)
                    ) || null;
                },

                fieldError(index, field) {
                    const key = `items.${index}.${field}`;
                    return this.validationErrors[key]?.[0] || '';
                },
            }
        }
    </script>
@endsection
