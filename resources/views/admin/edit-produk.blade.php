@extends('admin.layout.master')

@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-laporan-produksi', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-800">
            <span class="text-gray-800">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-800 hover:underline">Produksi</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600 font-bold">Edit Produk</span>
        </div>
    </section>

    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.production.update', $productionBatch->id) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <input type="hidden" name="product_variant_id" value="{{ $productVariant->id }}">

        {{-- ROW 1 --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Nama Penerima</label>
                    <input type="text" value="{{ $personResponsible->name }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Provinsi</label>
                    <select id="provinceSelect" name="province"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                        <option value="">-- Pilih Provinsi --</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province['name'] }}"
                                {{ old('province', $productionBatch->province) == $province['name'] ? 'selected' : '' }}>
                                {{ $province['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Tanggal Produksi</label>
                    <input type="date" name="entry_date"
                        value="{{ old('entry_date', $productionBatch->entry_date?->format('Y-m-d')) }}"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                </div>
            </div>
        </section>

        {{-- ROW 2 --}}
        <section class="bg-[#53BF6A]/55 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">ID Produk</label>
                    <input type="text" value="{{ $productVariant->product?->code ?? '-' }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Stock Keeping Unit</label>
                    <input type="text" value="{{ $productVariant->sku }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Nama Produk</label>
                    <input type="text" value="{{ $productVariant->name }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Jumlah Produksi</label>
                    <input type="number" min="1" name="quantity"
                        value="{{ old('quantity', $productionBatch->quantity) }}"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                </div>
            </div>
        </section>

        {{-- MATERIALS --}}
        <div id="materialsWrapper" class="space-y-4">
            @foreach ($materials as $index => $item)
                <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
                    <input type="hidden" name="items[{{ $index }}][raw_material_id]"
                        value="{{ $item['raw_material_id'] }}">

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-bold mb-2">ID Barang</label>
                            <input value="{{ $item['id_barang'] }}" readonly
                                class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700">
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Nama Barang</label>
                            <input value="{{ $item['nama_barang'] }}" readonly
                                class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700">
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Stok Tersedia</label>
                            <input value="{{ $item['stok_tersedia'] }} {{ $item['unit'] }}" readonly
                                class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700">
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Stok Digunakan</label>
                            <input type="number" min="1" name="items[{{ $index }}][quantity_use]"
                                value="{{ old("items.$index.quantity_use", $item['quantity_use']) }}"
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                        </div>
                    </div>
                </section>
            @endforeach
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-800 mb-2">Catatan</label>
            <textarea name="note" rows="3"
                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm text-gray-900">{{ old('note', $productionBatch->note) }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-4 pt-2">
            <button type="button" onclick="openCancelModal()"
                class="inline-flex items-center justify-center rounded-lg bg-red-600 px-10 py-3 text-sm font-bold text-white hover:bg-red-700">
                Batal
            </button>

            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                Simpan Perubahan
            </button>
        </div>
    </form>

    <div id="cancelModal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-2 sm:p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-auto animate-scale-in overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Batalkan Pemesanan?</h3>
            </div>

            <div class="px-6 py-4 text-sm text-gray-700 leading-relaxed">
                Data yang sudah kamu isi <span class="font-semibold">belum disimpan</span>.
                Kalau dibatalkan, semua perubahan akan hilang.
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <button type="button" onclick="closeCancelModal()"
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

    <script>
        const provinceSelect = document.getElementById('provinceSelect');
        const materialsWrapper = document.getElementById('materialsWrapper');
        const cancelModal = document.getElementById('cancelModal');

        function openCancelModal() {
            cancelModal.classList.remove('hidden');
            cancelModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeCancelModal() {
            cancelModal.classList.add('hidden');
            cancelModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        // close when click backdrop
        cancelModal?.addEventListener('click', (e) => {
            if (e.target === cancelModal) closeCancelModal();
        });

        // ESC close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && cancelModal && !cancelModal.classList.contains('hidden')) {
                closeCancelModal();
            }
        });

        async function fetchMaterials(province) {
            if (!province) {
                materialsWrapper.innerHTML = `
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-xl p-4 text-sm">
                        Pilih provinsi dulu, baru bahan baku akan dimuat.
                    </div>
                `;
                return;
            }

            materialsWrapper.innerHTML = `
                <div class="bg-gray-100 border border-gray-200 text-gray-700 rounded-xl p-4 text-sm">
                    Memuat bahan baku...
                </div>
            `;

            try {
                const response = await fetch(
                    `{{ route('admin.production.materials') }}?province=${encodeURIComponent(province)}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const result = await response.json();

                if (!result.success || !result.materials || !result.materials.length) {
                    materialsWrapper.innerHTML = `
                        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm">
                            Tidak ada stok bahan baku untuk provinsi ini.
                        </div>
                    `;
                    return;
                }

                materialsWrapper.innerHTML = result.materials.map((item, index) => `
                    <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
                        <input type="hidden" name="items[${index}][raw_material_id]" value="${item.raw_material_id}">

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-bold mb-2">ID Barang</label>
                                <input value="${item.id_barang}" readonly
                                    class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-2">Nama Barang</label>
                                <input value="${item.nama_barang}" readonly
                                    class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-2">Stok Tersedia</label>
                                <input value="${item.stok_tersedia} ${item.unit}" readonly
                                    class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-2">Stok Digunakan</label>
                                <input type="number" min="1" name="items[${index}][quantity_use]"
                                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900"
                                    placeholder="Masukkan jumlah">
                            </div>
                        </div>
                    </section>
                `).join('');
            } catch (error) {
                console.error('Fetch materials error:', error);

                materialsWrapper.innerHTML = `
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm">
                        Gagal mengambil data bahan baku.
                    </div>
                `;
            }
        }

        provinceSelect.addEventListener('change', function() {
            fetchMaterials(this.value);
        });
    </script>
@endsection
