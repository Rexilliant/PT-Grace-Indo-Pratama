@extends('admin.layout.master')

{{-- sidebar active (sesuaikan menu kamu) --}}
@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-permintaan-pengiriman', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-700 hover:underline">Permintaan Pengiriman</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600 font-bold">Tambah Pengiriman</span>
        </div>
    </section>

    <form action="{{ route('store-shippment') }}" method="POST" enctype="multipart/form-data" class="space-y-5"
        x-data="shipmentForm()" x-init="init()">
        @csrf

        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Pengajuan</label>
                    <input type="date" value="{{ now()->format('Y-m-d') }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Permintaan Pengiriman</label>
                    <input type="date" name="shippment_request_at" value="{{ old('shippment_request_at') }}"
                        class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 @error('shippment_request_at') border-red-500 bg-red-50 @else border-gray-400 bg-white @enderror">
                    @error('shippment_request_at')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Penanggung Jawab</label>
                    <input type="text" value="{{ auth()->user()->name }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0">
                    <input type="hidden" name="person_responsible_id" value="{{ auth()->id() }}">
                    @error('person_responsible_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Jenis Pengiriman</label>
                    <input type="text" name="shippment_type" value="{{ old('shippment_type') }}"
                        class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 @error('shippment_type') border-red-500 bg-red-50 @else border-gray-400 bg-white @enderror">
                    @error('shippment_type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Provinsi Tujuan</label>
                    <select name="province"
                        class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500 @error('province') border-red-500 bg-red-50 @else border-gray-400 bg-white @enderror">
                        <option value="">-- Pilih Provinsi Tujuan --</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province['name'] }}"
                                {{ old('province') == $province['name'] ? 'selected' : '' }}>
                                {{ $province['name'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('province')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Armada Pengiriman</label>
                    <input type="text" name="shipping_fleet" value="{{ old('shipping_fleet') }}"
                        class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 @error('shipping_fleet') border-red-500 bg-red-50 @else border-gray-400 bg-white @enderror">
                    @error('shipping_fleet')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Nama Penerima</label>
                    <select name="received_by_id"
                        class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500 @error('received_by_id') border-red-500 bg-red-50 @else border-gray-400 bg-white @enderror">
                        <option value="">-- Pilih Penerima --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                {{ old('received_by_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('received_by_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Kontak Penerima</label>
                    <input type="text" name="contact" value="{{ old('contact') }}"
                        class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 @error('contact') border-red-500 bg-red-50 @else border-gray-400 bg-white @enderror">
                    @error('contact')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Alamat Lengkap</label>
                    <textarea rows="3" name="address"
                        class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 @error('address') border-red-500 bg-red-50 @else border-gray-400 bg-white @enderror">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Catatan</label>
                    <textarea name="notes" rows="3" placeholder="Opsional"
                        class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 @error('notes') border-red-500 bg-red-50 @else border-gray-400 bg-white @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="flex items-center justify-between">
                <div class="text-xs font-bold text-gray-800 mb-4">Daftar Item Pengiriman</div>
                <button type="button" @click="addItem()"
                    class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-4 py-2 text-sm font-bold text-white hover:bg-blue-800">
                    + Tambah Item
                </button>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-800 mb-2.5">Provinsi Stok</label>
                <select x-model="stockProvince" @change="resetProductSelections()"
                    class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500 @error('stock_province') border-red-500 bg-red-50 @else border-gray-400 bg-white @enderror">
                    <option value="">-- Pilih Provinsi Stok --</option>
                    @foreach ($provinceProducts as $province)
                        <option value="{{ $province }}">{{ $province }}</option>
                    @endforeach
                </select>

                <input type="hidden" name="stock_province" :value="stockProvince">

                @error('stock_province')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @error('items')
                <p class="mb-3 text-xs text-red-600">{{ $message }}</p>
            @enderror

            <div class="mb-4 text-xs text-gray-600" x-show="!stockProvince">
                Pilih provinsi stok terlebih dahulu agar daftar produk muncul.
            </div>

            <div class="space-y-4">
                <template x-for="(item, index) in items" :key="index">
                    <div class="rounded-lg border border-gray-300 p-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-800">Produk</label>
                                <select :name="`items[${index}][product_stock_id]`" x-model="item.product_stock_id"
                                    :disabled="!stockProvince"
                                    :class="itemError(index, 'product_stock_id') ? 'border-red-500 bg-red-50' :
                                        'border-gray-400 bg-white'"
                                    class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 disabled:bg-gray-100">
                                    <option value="">-- Pilih Produk --</option>
                                    <template x-for="stock in filteredStocks()" :key="stock.id">
                                        <option :value="stock.id"
                                            x-text="`${stock.product_variant.sku} - ${stock.product_variant.name}`">
                                        </option>
                                    </template>
                                </select>

                                <template x-if="itemError(index, 'product_stock_id')">
                                    <p class="mt-1 text-xs text-red-600" x-text="itemError(index, 'product_stock_id')">
                                    </p>
                                </template>
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-800">Stock Tersedia</label>
                                <input type="text" :value="selectedStock(item.product_stock_id)?.stock ?? 0" readonly
                                    class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0">
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-800">Jumlah</label>
                                <input type="number" min="1"
                                    :max="selectedStock(item.product_stock_id)?.stock ?? null"
                                    :name="`items[${index}][quantity]`" x-model="item.quantity"
                                    placeholder="Masukkan jumlah"
                                    :class="itemError(index, 'quantity') ? 'border-red-500 bg-red-50' :
                                        'border-gray-400 bg-white'"
                                    class="w-full rounded-md px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0">

                                <template x-if="itemError(index, 'quantity')">
                                    <p class="mt-1 text-xs text-red-600" x-text="itemError(index, 'quantity')"></p>
                                </template>
                            </div>

                            <div class="flex items-end gap-3">
                                <button type="button" @click="removeItem(index)"
                                    class="inline-flex w-full items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">
                                    Hapus
                                </button>
                            </div>
                        </div>

                        <div class="mt-2 text-[11px] text-gray-600"
                            x-show="item.product_stock_id && item.quantity > (selectedStock(item.product_stock_id)?.stock ?? 0)">
                            Jumlah melebihi stok tersedia.
                        </div>
                    </div>
                </template>
            </div>
        </section>



        <div class="flex items-center justify-end gap-4 pt-2">
            <a href="{{ route('admin.gudang-permintaan-pengiriman') }}"
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function shipmentForm() {
            return {
                stockProvince: @js(old('stock_province', '')),
                productStocks: @json($productStocks),
                validationErrors: @json($errors->toArray()),
                items: @json(old('items', [['product_stock_id' => '', 'quantity' => 1]])),

                init() {
                    this.initFilePond();

                    @if (session('success'))
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: @js(session('success')),
                            confirmButtonColor: '#2D2ACD'
                        });
                    @endif

                    @if (session('error'))
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: @js(session('error')),
                            confirmButtonColor: '#dc2626'
                        });
                    @endif
                },

                addItem() {
                    this.items.push({
                        product_stock_id: '',
                        quantity: 1
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

                filteredStocks() {
                    if (!this.stockProvince) return [];
                    return this.productStocks.filter(stock => stock.province === this.stockProvince);
                },

                selectedStock(productStockId) {
                    return this.productStocks.find(stock => String(stock.id) === String(productStockId)) || null;
                },

                resetProductSelections() {
                    this.items = this.items.map(item => ({
                        ...item,
                        product_stock_id: '',
                        quantity: 1
                    }));
                },

                itemError(index, field) {
                    const key = `items.${index}.${field}`;
                    return this.validationErrors[key] ? this.validationErrors[key][0] : '';
                },

            }
        }
    </script>
@endsection
