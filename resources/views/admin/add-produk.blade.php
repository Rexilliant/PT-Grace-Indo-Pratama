@extends('admin.layout.master')

@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-laporan-produksi', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    @php
        $readonlyClass =
            'w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700';
        $inputClass =
            'w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-500';
        $sectionClass = 'p-5 shadow border border-gray-300 rounded-xl';
    @endphp

    <div x-data="productionForm()" x-init="init()">
        <section class="mb-5">
            <div class="text-xl font-semibold text-gray-800">
                <span>Gudang</span>
                <span class="mx-1 text-gray-400">›</span>
                <a href="#" class="hover:underline">Produksi</a>
                <span class="mx-1 text-gray-400">›</span>
                <a href="#" class="hover:underline">Pilih Produk</a>
                <span class="mx-1 text-gray-400">›</span>
                <span class="text-blue-600 font-bold">Tambah Produk</span>
            </div>
        </section>

        <form action="{{ route('admin.production.store') }}" method="POST" class="space-y-5">
            @csrf

            <input type="hidden" name="product_variant_id" value="{{ $productVariant->id }}">

            {{-- ROW 1 --}}
            <section class="bg-gray-200/80 {{ $sectionClass }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2">Nama Penerima</label>
                        <input type="text" value="{{ $personResponsible->name }}" readonly
                            class="{{ $readonlyClass }} cursor-not-allowed" />
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2">Gudang</label>
                        <select name="warehouse_id" x-model="selectedWarehouse" @change="fetchMaterials()"
                            class="{{ $inputClass }}">
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

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2">Tanggal Produksi</label>
                        <input type="date" name="entry_date" value="{{ old('entry_date', date('Y-m-d')) }}"
                            class="{{ $inputClass }}">
                        @error('entry_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- ROW 2 --}}
            <section class="bg-[#53BF6A]/55 {{ $sectionClass }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2">ID Barang</label>
                        <input type="text" value="{{ $productVariant->product?->code ?? '-' }}" readonly
                            class="{{ $readonlyClass }}">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2">Stock Keeping Unit</label>
                        <input type="text" value="{{ $productVariant->sku }}" readonly class="{{ $readonlyClass }}">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2">Nama Produk</label>
                        <input type="text" value="{{ $productVariant->name }}" readonly class="{{ $readonlyClass }}">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2">Jumlah Produksi</label>
                        <input type="number" min="1" name="quantity" value="{{ old('quantity') }}"
                            class="{{ $inputClass }}">
                        @error('quantity')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- MATERIALS --}}
            <div class="space-y-4">
                <template x-if="!selectedWarehouse">
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-xl p-4 text-sm">
                        Pilih gudang dulu, baru bahan baku akan dimuat.
                    </div>
                </template>

                <template x-if="selectedWarehouse && loading">
                    <div class="bg-gray-100 border border-gray-200 text-gray-700 rounded-xl p-4 text-sm">
                        Memuat bahan baku...
                    </div>
                </template>

                <template x-if="selectedWarehouse && !loading && errorMessage">
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm" x-text="errorMessage">
                    </div>
                </template>

                <template x-if="selectedWarehouse && !loading && !errorMessage && materials.length === 0">
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm">
                        Tidak ada stok bahan baku untuk gudang ini.
                    </div>
                </template>

                <template x-for="(item, index) in materials" :key="item.raw_material_id">
                    <section class="bg-gray-200/80 {{ $sectionClass }}">
                        <input type="hidden" :name="`items[${index}][raw_material_id]`" :value="item.raw_material_id">

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-bold mb-2">ID Barang</label>
                                <input type="text" :value="item.id_barang" readonly class="{{ $readonlyClass }}">
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-2">Nama Barang</label>
                                <input type="text" :value="item.nama_barang" readonly class="{{ $readonlyClass }}">
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-2">Stok Tersedia</label>
                                <input type="text" :value="`${item.stok_tersedia} ${item.unit}`" readonly
                                    class="{{ $readonlyClass }}">
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-2">Stok Digunakan</label>
                                <input type="number" min="0" :name="`items[${index}][quantity_use]`"
                                    class="{{ $inputClass }}" placeholder="Masukkan jumlah">
                            </div>
                        </div>
                    </section>
                </template>
            </div>

            {{-- NOTE --}}
            <div>
                <label class="block text-sm font-bold text-gray-800 mb-2">Catatan</label>
                <textarea name="note" rows="3"
                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-500">{{ old('note') }}</textarea>
                @error('note')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- ACTION --}}
            <div class="flex items-center justify-end gap-4 pt-2">
                <button type="button" @click="cancelModal = true"
                    class="inline-flex items-center justify-center rounded-lg bg-red-600 px-10 py-3 text-sm font-bold text-white hover:bg-red-700">
                    Batal
                </button>

                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                    Simpan
                </button>
            </div>
        </form>

        {{-- MODAL BATAL --}}
        <div x-show="cancelModal" x-transition @keydown.escape.window="cancelModal = false"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm p-2 sm:p-4"
            style="display: none;">
            <div @click.outside="cancelModal = false"
                class="bg-white rounded-xl shadow-xl w-full max-w-md mx-auto overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800">Batalkan Pemesanan?</h3>
                </div>

                <div class="px-6 py-4 text-sm text-gray-700 leading-relaxed">
                    Data yang sudah kamu isi <span class="font-semibold">belum disimpan</span>.
                    Kalau dibatalkan, semua perubahan akan hilang.
                </div>

                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                    <button type="button" @click="cancelModal = false"
                        class="px-4 py-2 rounded-lg text-sm font-semibold bg-gray-200 hover:bg-gray-300">
                        Tetap di Halaman
                    </button>

                    <a href="{{ route('admin.gudang-laporan-produksi') }}"
                        class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 text-white hover:bg-red-700">
                        Ya, Batalkan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function productionForm() {
            return {
                selectedWarehouse: @json(old('warehouse_id') ?? ''),
                materials: [],
                loading: false,
                errorMessage: '',
                cancelModal: false,

                init() {
                    if (this.selectedWarehouse) {
                        this.fetchMaterials();
                    }
                },

                async fetchMaterials() {
                    if (!this.selectedWarehouse) {
                        this.materials = [];
                        this.errorMessage = '';
                        return;
                    }

                    this.loading = true;
                    this.errorMessage = '';
                    this.materials = [];

                    try {
                        const url =
                            `{{ route('admin.production.materials') }}?warehouse_id=${encodeURIComponent(this.selectedWarehouse)}`;
                        const response = await fetch(url, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }

                        const result = await response.json();

                        if (!result.success) {
                            throw new Error(result.message || 'Gagal mengambil data bahan baku.');
                        }

                        this.materials = Array.isArray(result.materials) ? result.materials : [];
                    } catch (error) {
                        console.error('Fetch materials error:', error);
                        this.errorMessage = 'Gagal mengambil data bahan baku.';
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
@endsection
