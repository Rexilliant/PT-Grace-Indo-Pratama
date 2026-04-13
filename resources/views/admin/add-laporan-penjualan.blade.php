@extends('admin.layout.master')

@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-laporan-penjualan', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Pemasaran</span>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-gray-700">Laporan Penjualan</span>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600 font-bold">Tambah Laporan</span>
        </div>
    </section>

    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-red-300 bg-red-50 p-4 text-sm text-red-700">
            <div class="font-bold mb-2">Ada data yang masih bermasalah:</div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.pemasaran-laporan-penjualan.store') }}" method="POST" enctype="multipart/form-data"
        class="space-y-5">
        @csrf

        {{-- BLOK HEADER --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Laporan</label>
                    <input type="date" value="{{ $reportDate }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Penjualan</label>
                    <input type="date" name="sale_date" value="{{ old('sale_date') }}"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Penanggung Jawab</label>
                    <input type="text" value="{{ $personResponsibleName }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Jenis Penjualan</label>
                    <select name="sale_type"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                        @foreach (['Perseorangan', 'Instansi', 'Pesanan'] as $type)
                            <option value="{{ $type }}"
                                {{ old('sale_type', 'Perseorangan') === $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Gudang</label>
                    <select id="warehouseSelect" name="warehouse_id"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                        <option value="">Pilih gudang</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}"
                                {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }} — {{ $warehouse->province }} / {{ $warehouse->city }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Provinsi</label>
                    <select id="customerProvince" name="customer_province"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                        <option value="">Memuat provinsi...</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Daerah</label>
                    <select id="customerCity" name="customer_city"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900"
                        disabled>
                        <option value="">Pilih provinsi terlebih dahulu</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Alamat Lengkap</label>
                    <textarea name="customer_address" rows="3" placeholder="Masukkan alamat lengkap"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">{{ old('customer_address') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Nama Pembeli</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                        placeholder="Masukkan nama pembeli"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Kontak Pembeli</label>
                    <input type="text" name="customer_contact" value="{{ old('customer_contact') }}"
                        placeholder="Masukkan kontak pembeli"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                </div>
            </div>
        </section>

        {{-- DAFTAR BARANG --}}
        <section class="space-y-4">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-sm sm:text-base font-bold text-gray-800">Daftar Barang Terjual</h2>

                <button type="button" id="addItemBtn"
                    class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-4 py-2 text-sm font-bold text-white hover:bg-blue-800">
                    + Tambah Barang
                </button>
            </div>

            <div id="itemsContainer" class="space-y-4"></div>
        </section>

        {{-- TOTAL + STATUS --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Total Pesanan Keseluruhan</label>
                    <input id="grandTotalDisplay" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Status</label>
                    <select id="statusSelect" name="status"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                        <option value="Terhutang" {{ old('status', 'Terhutang') === 'Terhutang' ? 'selected' : '' }}>
                            Terhutang
                        </option>
                        <option value="Lunas" {{ old('status') === 'Lunas' ? 'selected' : '' }}>
                            Lunas
                        </option>
                    </select>
                </div>

                <div id="blokTerhutang">
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Down Payment</label>
                    <input name="down_payment" id="downPayment" value="{{ old('down_payment', 0) }}" inputmode="numeric"
                        placeholder="Contoh: 400000"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                </div>

                <div id="blokTerhutang2">
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Jumlah Terhutang</label>
                    <input id="jumlahTerhutang" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Catatan</label>
                    <textarea name="notes" rows="4"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">{{ old('notes') }}</textarea>
                </div>
            </div>
        </section>

        {{-- INVOICE --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <label for="invoice" class="block text-sm font-bold mb-3 text-gray-800">Bukti Pembayaran</label>

            <div id="dropzone"
                class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-400 bg-gray-100 px-6 py-8 text-center min-h-[220px]">
                <input id="invoice" name="invoice" type="file" accept=".png,.jpg,.jpeg,.pdf"
                    class="absolute inset-0 h-full w-full cursor-pointer opacity-0 z-10" />

                <div id="dropzoneContent" class="flex flex-col items-center gap-3 w-full pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-700" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                            d="M3 15a4 4 0 004 4h10a4 4 0 004-4m-4-4l-4-4m0 0L9 11m4-4v12" />
                    </svg>
                    <div class="text-sm text-gray-800">
                        <span class="font-bold">Click to upload</span> or drag and drop
                    </div>
                    <div class="text-xs text-gray-600">PNG, JPG, JPEG, or PDF (MAX 3 Mb)</div>
                </div>
            </div>
        </section>

        {{-- ACTION --}}
        <div class="flex items-center justify-end gap-4 pt-2">
            <a href="{{ route('admin.pemasaran-laporan-penjualan') }}"
                class="inline-flex items-center justify-center rounded-lg bg-red-600 px-10 py-3 text-sm font-bold text-white hover:bg-red-700">
                Batal
            </a>

            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                Simpan
            </button>
        </div>
    </form>

    {{-- TEMPLATE ITEM --}}
    <template id="saleItemTemplate">
        <section class="sale-item-card bg-[#a7dfb2] p-5 shadow border border-[#68b97a] rounded-xl" data-index="__INDEX__">
            <input type="hidden" name="items[__INDEX__][product_stock_id]" value=""
                class="product-stock-id-input">

            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Barang #__NUMBER__</h3>
                    <p class="text-xs text-gray-700 mt-1">Pilih barang sesuai gudang yang dipilih.</p>
                </div>

                <button type="button"
                    class="remove-item-btn inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-xs sm:text-sm font-bold text-white hover:bg-red-700">
                    Hapus
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Jenis Barang</label>
                    <select
                        class="item-stock-select w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                        <option value="">Pilih gudang dulu</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">SKU</label>
                    <input type="text" readonly
                        class="item-sku w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Stok Tersedia</label>
                    <input type="text" readonly
                        class="item-stock-available w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Nama Produk</label>
                    <input type="text" readonly
                        class="item-name w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Harga Satuan</label>
                    <input type="text" readonly
                        class="item-price-display w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                    <input type="hidden" class="item-price-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Jumlah Terjual</label>
                    <input type="number" min="0" step="1" name="items[__INDEX__][quantity]"
                        value="1"
                        class="item-quantity w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Diskon Satuan</label>
                    <input type="text" name="items[__INDEX__][discount]" value="0" inputmode="numeric"
                        placeholder="Contoh: 200000"
                        class="item-discount w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-xs font-bold text-gray-800 mb-2.5">Subtotal</label>
                <input type="text" readonly
                    class="item-subtotal-display w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
            </div>
        </section>
    </template>

    <script>
        const provinceJsonUrl = '/assets/data/provinceAndCity.json';
        const stocksByWarehouseUrl = @json(route('admin.pemasaran-laporan-penjualan.stocks-by-warehouse'));
        const oldProvince = @json(old('customer_province'));
        const oldCity = @json(old('customer_city'));
        const oldWarehouseId = @json(old('warehouse_id'));
        const oldItems = @json(old('items', []));
    </script>

    <script>
        const warehouseSelect = document.getElementById('warehouseSelect');
        const customerProvince = document.getElementById('customerProvince');
        const customerCity = document.getElementById('customerCity');

        const itemsContainer = document.getElementById('itemsContainer');
        const addItemBtn = document.getElementById('addItemBtn');
        const saleItemTemplate = document.getElementById('saleItemTemplate');
        const grandTotalDisplay = document.getElementById('grandTotalDisplay');
        const statusSelect = document.getElementById('statusSelect');
        const downPayment = document.getElementById('downPayment');
        const jumlahTerhutang = document.getElementById('jumlahTerhutang');
        const blokTerhutang = document.getElementById('blokTerhutang');
        const blokTerhutang2 = document.getElementById('blokTerhutang2');

        const invoiceInput = document.getElementById('invoice');
        const dropzone = document.getElementById('dropzone');
        const dropzoneContent = document.getElementById('dropzoneContent');

        let currentStocks = [];
        let provinceData = [];

        function parseNumber(value) {
            if (value === null || value === undefined) return 0;
            const cleaned = String(value).replace(/[^\d]/g, '');
            return cleaned ? parseInt(cleaned, 10) : 0;
        }

        function formatRupiah(num) {
            return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
        }

        async function loadProvinces() {
            try {
                const response = await fetch(provinceJsonUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();
                provinceData = Array.isArray(data) ? data : [];

                customerProvince.innerHTML = '<option value="">Pilih provinsi</option>';

                if (!provinceData.length) {
                    customerProvince.innerHTML = '<option value="">Data provinsi kosong</option>';
                    return;
                }

                provinceData.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.province_name;
                    option.textContent = item.province_name;

                    if (oldProvince && oldProvince === item.province_name) {
                        option.selected = true;
                    }

                    customerProvince.appendChild(option);
                });

                populateCities(customerProvince.value);
            } catch (error) {
                console.error('Gagal memuat provinceAndCity.json:', error);
                customerProvince.innerHTML = '<option value="">Gagal memuat provinsi</option>';
                customerCity.innerHTML = '<option value="">Gagal memuat daerah</option>';
                customerCity.disabled = true;
            }
        }

        function populateCities(selectedProvince) {
            customerCity.innerHTML = '';

            if (!selectedProvince) {
                customerCity.innerHTML = '<option value="">Pilih provinsi terlebih dahulu</option>';
                customerCity.disabled = true;
                return;
            }

            const province = provinceData.find(item => item.province_name === selectedProvince);

            if (!province || !Array.isArray(province.cities) || !province.cities.length) {
                customerCity.innerHTML = '<option value="">Data daerah tidak tersedia</option>';
                customerCity.disabled = true;
                return;
            }

            customerCity.disabled = false;
            customerCity.innerHTML = '<option value="">Pilih daerah</option>';

            province.cities.forEach(city => {
                const option = document.createElement('option');
                option.value = city.name;
                option.textContent = city.name;

                if (oldCity && oldCity === city.name) {
                    option.selected = true;
                }

                customerCity.appendChild(option);
            });
        }

        async function loadStocksByWarehouse(warehouseId) {
            currentStocks = [];

            if (!warehouseId) {
                updateAllItemOptions();
                recalculateGrandTotal();
                return;
            }

            try {
                const url = `${stocksByWarehouseUrl}?warehouse_id=${encodeURIComponent(warehouseId)}`;
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const json = await response.json();
                currentStocks = Array.isArray(json.data) ? json.data : [];

                updateAllItemOptions();
                recalculateGrandTotal();
            } catch (error) {
                console.error('Gagal memuat stok berdasarkan gudang:', error);
                currentStocks = [];
                updateAllItemOptions();
                recalculateGrandTotal();
            }
        }

        function updateItemNumbers() {
            const cards = itemsContainer.querySelectorAll('.sale-item-card');

            cards.forEach((card, index) => {
                const title = card.querySelector('h3');
                if (title) title.textContent = `Barang #${index + 1}`;
            });

            const removeButtons = itemsContainer.querySelectorAll('.remove-item-btn');
            removeButtons.forEach(btn => {
                btn.disabled = cards.length <= 1;
            });
        }

        function renderItemOptions(card, selectedId = '') {
            const select = card.querySelector('.item-stock-select');
            if (!select) return;

            if (!warehouseSelect.value) {
                select.innerHTML = '<option value="">Pilih gudang dulu</option>';
                syncStockData(card);
                return;
            }

            if (!currentStocks.length) {
                select.innerHTML = '<option value="">Tidak ada barang tersedia di gudang ini</option>';
                syncStockData(card);
                return;
            }

            select.innerHTML = '<option value="">Pilih jenis barang</option>';

            currentStocks.forEach(stock => {
                const option = document.createElement('option');
                option.value = stock.id;
                option.dataset.sku = stock.sku || '';
                option.dataset.name = stock.product_name || '';
                option.dataset.price = stock.price || 0;
                option.dataset.stock = stock.stock || 0;
                option.dataset.unit = stock.unit || '';
                option.textContent =
                    `${stock.product_name} (${stock.sku}) - Stok: ${stock.stock} ${stock.unit ?? ''}`;

                if (String(selectedId) === String(stock.id)) {
                    option.selected = true;
                }

                select.appendChild(option);
            });

            syncStockData(card);
        }

        function updateAllItemOptions() {
            itemsContainer.querySelectorAll('.sale-item-card').forEach(card => {
                const selectedId = card.querySelector('.product-stock-id-input')?.value || '';
                renderItemOptions(card, selectedId);
            });
        }

        function syncStockData(card) {
            const select = card.querySelector('.item-stock-select');
            const selectedOption = select?.options[select.selectedIndex];

            const stockIdInput = card.querySelector('.product-stock-id-input');
            const skuInput = card.querySelector('.item-sku');
            const stockAvailableInput = card.querySelector('.item-stock-available');
            const nameInput = card.querySelector('.item-name');
            const priceHidden = card.querySelector('.item-price-hidden');
            const priceDisplay = card.querySelector('.item-price-display');

            if (!selectedOption || !selectedOption.value) {
                stockIdInput.value = '';
                skuInput.value = '';
                stockAvailableInput.value = '';
                nameInput.value = '';
                priceHidden.value = 0;
                priceDisplay.value = formatRupiah(0);
                return;
            }

            stockIdInput.value = selectedOption.value;
            skuInput.value = selectedOption.dataset.sku || '';
            stockAvailableInput.value = selectedOption.dataset.stock || '0';
            nameInput.value = selectedOption.dataset.name || '';

            const price = parseNumber(selectedOption.dataset.price || 0);
            priceHidden.value = price;
            priceDisplay.value = formatRupiah(price);
        }

        function recalculateCard(card) {
            const price = parseNumber(card.querySelector('.item-price-hidden')?.value || 0);
            const quantityInput = card.querySelector('.item-quantity');
            const discountInput = card.querySelector('.item-discount');
            const subtotalDisplay = card.querySelector('.item-subtotal-display');
            const stockAvailable = parseNumber(card.querySelector('.item-stock-available')?.value || 0);

            let quantity = parseNumber(quantityInput?.value || 0);
            let discount = parseNumber(discountInput?.value || 0);

            if (quantity < 1) quantity = 1;
            if (stockAvailable > 0 && quantity > stockAvailable) {
                quantity = stockAvailable;
            }

            const finalUnitPrice = Math.max(0, price - discount);
            const subtotal = finalUnitPrice * quantity;

            if (quantityInput) quantityInput.value = quantity;
            if (discountInput) discountInput.value = discount;
            if (subtotalDisplay) subtotalDisplay.value = formatRupiah(subtotal);

            recalculateGrandTotal();
        }

        function recalculateGrandTotal() {
            let total = 0;

            itemsContainer.querySelectorAll('.sale-item-card').forEach(card => {
                const stockId = card.querySelector('.product-stock-id-input')?.value || '';
                if (!stockId) return;

                const price = parseNumber(card.querySelector('.item-price-hidden')?.value || 0);
                const qty = parseNumber(card.querySelector('.item-quantity')?.value || 0);
                const discount = parseNumber(card.querySelector('.item-discount')?.value || 0);

                total += Math.max(0, price - discount) * Math.max(1, qty);
            });

            grandTotalDisplay.value = formatRupiah(total);
            syncTerhutang(total);
        }

        function syncTerhutang(grandTotal = 0) {
            const status = statusSelect?.value || 'Terhutang';

            if (status === 'Lunas') {
                blokTerhutang.classList.add('hidden');
                blokTerhutang2.classList.add('hidden');

                if (downPayment) downPayment.value = grandTotal;
                if (jumlahTerhutang) jumlahTerhutang.value = formatRupiah(0);
                return;
            }

            blokTerhutang.classList.remove('hidden');
            blokTerhutang2.classList.remove('hidden');

            let dp = parseNumber(downPayment?.value || 0);
            if (dp > grandTotal) dp = grandTotal;

            if (downPayment) downPayment.value = dp;

            const debt = Math.max(0, grandTotal - dp);
            if (jumlahTerhutang) jumlahTerhutang.value = formatRupiah(debt);
        }

        function bindCardEvents(card) {
            const select = card.querySelector('.item-stock-select');
            const quantityInput = card.querySelector('.item-quantity');
            const discountInput = card.querySelector('.item-discount');
            const removeBtn = card.querySelector('.remove-item-btn');

            if (select) {
                select.addEventListener('change', () => {
                    syncStockData(card);
                    recalculateCard(card);
                });
            }

            if (quantityInput) {
                quantityInput.addEventListener('focus', function() {
                    this.select();
                });

                quantityInput.addEventListener('click', function() {
                    this.select();
                });

                quantityInput.addEventListener('input', () => recalculateCard(card));
            }

            if (discountInput) {
                discountInput.addEventListener('input', () => recalculateCard(card));
            }

            if (removeBtn) {
                removeBtn.addEventListener('click', () => {
                    const cards = itemsContainer.querySelectorAll('.sale-item-card');
                    if (cards.length <= 1) return;

                    card.remove();
                    updateItemNumbers();
                    recalculateGrandTotal();
                });
            }
        }

        function createNewItemCard(selectedData = null) {
            const index = itemsContainer.querySelectorAll('.sale-item-card').length;
            const html = saleItemTemplate.innerHTML
                .replaceAll('__INDEX__', index)
                .replaceAll('__NUMBER__', index + 1);

            const wrapper = document.createElement('div');
            wrapper.innerHTML = html.trim();

            const card = wrapper.firstElementChild;
            itemsContainer.appendChild(card);

            bindCardEvents(card);

            const selectedId = selectedData?.product_stock_id ?? '';
            renderItemOptions(card, selectedId);

            if (selectedData?.quantity) {
                card.querySelector('.item-quantity').value = selectedData.quantity;
            }

            if (selectedData?.discount !== undefined) {
                card.querySelector('.item-discount').value = selectedData.discount;
            }

            syncStockData(card);
            recalculateCard(card);
            updateItemNumbers();
        }

        customerProvince?.addEventListener('change', function() {
            populateCities(this.value);
        });

        warehouseSelect?.addEventListener('change', async function() {
            await loadStocksByWarehouse(this.value);

            itemsContainer.querySelectorAll('.sale-item-card').forEach(card => {
                renderItemOptions(card);
                recalculateCard(card);
            });
        });

        addItemBtn?.addEventListener('click', () => {
            createNewItemCard();
        });

        statusSelect?.addEventListener('change', () => recalculateGrandTotal());
        downPayment?.addEventListener('input', () => recalculateGrandTotal());

        async function initForm() {
            await loadProvinces();

            if (oldWarehouseId) {
                warehouseSelect.value = oldWarehouseId;
                await loadStocksByWarehouse(oldWarehouseId);
            }

            if (Array.isArray(oldItems) && oldItems.length) {
                oldItems.forEach(item => createNewItemCard(item));
            } else {
                createNewItemCard();
            }

            updateItemNumbers();
            recalculateGrandTotal();
        }

        initForm();

        function validateInvoiceFile(file) {
            const allowed = ['image/png', 'image/jpeg', 'application/pdf'];

            if (!allowed.includes(file.type)) {
                alert('File harus PNG / JPG / JPEG / PDF');
                return false;
            }

            const maxSize = 3 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('Ukuran file maksimal 3MB');
                return false;
            }

            return true;
        }

        function getDefaultDropzoneContent() {
            return `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-700" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                    d="M3 15a4 4 0 004 4h10a4 4 0 004-4m-4-4l-4-4m0 0L9 11m4-4v12" />
            </svg>
            <div class="text-sm text-gray-800">
                <span class="font-bold">Click to upload</span> or drag and drop
            </div>
            <div class="text-xs text-gray-600">PNG, JPG, JPEG, or PDF (MAX 3 Mb)</div>
        `;
        }

        function showDropzonePreview(file) {
            const isImage = file.type.startsWith('image/');
            const isPdf = file.type === 'application/pdf';
            const fileSizeKb = (file.size / 1024).toFixed(1);

            if (isImage) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    dropzoneContent.innerHTML = `
                    <div class="flex flex-col items-center gap-3 w-full">
                        <img src="${e.target.result}" alt="Preview invoice"
                            class="max-h-40 w-auto rounded-lg border border-gray-300 shadow-sm object-contain bg-white p-1">
                        <div class="text-sm font-bold text-gray-800 break-all">${file.name}</div>
                        <div class="text-xs text-gray-600">${fileSizeKb} KB</div>
                    </div>
                `;
                };
                reader.readAsDataURL(file);
                return;
            }

            if (isPdf) {
                dropzoneContent.innerHTML = `
                <div class="flex flex-col items-center gap-3 w-full">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600 font-bold text-sm border border-red-200">
                        PDF
                    </div>
                    <div class="text-sm font-bold text-gray-800 break-all">${file.name}</div>
                    <div class="text-xs text-gray-600">${fileSizeKb} KB</div>
                </div>
            `;
            }
        }

        if (invoiceInput) {
            invoiceInput.addEventListener('change', function() {
                const file = this.files?.[0];

                if (!file) {
                    dropzoneContent.innerHTML = getDefaultDropzoneContent();
                    return;
                }

                if (!validateInvoiceFile(file)) {
                    this.value = '';
                    dropzoneContent.innerHTML = getDefaultDropzoneContent();
                    return;
                }

                showDropzonePreview(file);
            });
        }

        if (dropzone) {
            ['dragenter', 'dragover'].forEach(evt => {
                dropzone.addEventListener(evt, e => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('border-blue-600');
                });
            });

            ['dragleave', 'dragend'].forEach(evt => {
                dropzone.addEventListener(evt, e => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.remove('border-blue-600');
                });
            });

            dropzone.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('border-blue-600');

                const file = e.dataTransfer?.files?.[0];
                if (!file) return;

                if (!validateInvoiceFile(file)) return;

                const dt = new DataTransfer();
                dt.items.add(file);
                invoiceInput.files = dt.files;

                showDropzonePreview(file);
            });
        }
    </script>

@endsection
