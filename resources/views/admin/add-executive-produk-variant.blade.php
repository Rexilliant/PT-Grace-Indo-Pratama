@extends('admin.layout.master')

@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-produk-variant', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Executive</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-700 hover:underline">Produk Varian</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600">Tambah Produk</span>
        </div>
    </section>

    <form action="{{ route('admin.add-executive-produk-variant.store') }}" method="POST" class="space-y-4">
        @csrf

        {{-- FORM CARD --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">

            {{-- pilih produk (relasi product_id) --}}
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Pilih Produk</label>
                <select name="product_id"
                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900
                           focus:border-blue-600 focus:ring-0 @error('product_id') border-red-500 focus:border-red-600 @enderror">
                    <option value="" disabled {{ old('product_id') ? '' : 'selected' }}>Pilih Produk</option>
                    @foreach ($products as $prod)
                        <option value="{{ $prod->id }}"
                            {{ (string) old('product_id') === (string) $prod->id ? 'selected' : '' }}>
                            {{ $prod->code }} - {{ $prod->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Row 1 --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-4">
                {{-- SKU --}}
                <div>
                    <label class="block text-sm font-bold mb-2">SKU</label>
                    <input name="sku" type="text" value="{{ old('sku') }}" placeholder="Contoh: BHOS-001"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900
                               focus:border-blue-600 focus:ring-0 @error('sku') border-red-500 focus:border-red-600 @enderror">
                    @error('sku')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Produk (variant name) --}}
                <div>
                    <label class="block text-sm font-bold mb-2">Nama Produk</label>
                    <input name="name" type="text" value="{{ old('name') }}" placeholder="Contoh: BHOS Ekstra"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900
                               focus:border-blue-600 focus:ring-0 @error('name') border-red-500 focus:border-red-600 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ukuran Kemasan (pack_size) --}}
                <div>
                    <label class="block text-sm font-bold mb-2">Ukuran Kemasan</label>
                    <input name="pack_size" type="number" min="0" value="{{ old('pack_size', 0) }}"
                        placeholder="Contoh: 5"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900
                               focus:border-blue-600 focus:ring-0 @error('pack_size') border-red-500 focus:border-red-600 @enderror">
                    @error('pack_size')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Row 2 --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                {{-- Satuan --}}
                <div>
                    <label class="block text-sm font-bold mb-2">Satuan</label>
                    <input name="unit" type="text" value="{{ old('unit') }}" placeholder="Contoh: Kg/Liter"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900
                               focus:border-blue-600 focus:ring-0 @error('unit') border-red-500 focus:border-red-600 @enderror">
                    @error('unit')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Harga --}}
                <div>
                    <label class="block text-sm font-bold mb-2">Harga</label>
                    <input name="price" type="number" min="0" value="{{ old('price', 0) }}"
                        placeholder="Contoh: 125000"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900
                               focus:border-blue-600 focus:ring-0 @error('price') border-red-500 focus:border-red-600 @enderror">
                    <p class="mt-1 text-xs text-gray-600 font-semibold">Masukkan angka tanpa titik/koma.</p>
                    @error('price')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="sm:col-span-2 lg:col-span-1">
                    <label class="block text-sm font-bold mb-2">Status</label>
                    <select name="status"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900
                               focus:border-blue-600 focus:ring-0 @error('status') border-red-500 focus:border-red-600 @enderror">
                        <option value="" disabled {{ old('status') ? '' : 'selected' }}>Pilih Status</option>
                        <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Active</option>
                        <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- ACTIONS --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
            <button type="button" onclick="openCancelModal()"
                class="w-full sm:w-auto inline-flex items-center justify-center rounded-lg
                       bg-red-600 px-10 py-3 text-sm font-bold text-white hover:bg-red-700">
                Batal
            </button>

            <button type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center rounded-lg
                       bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                Simpan
            </button>
        </div>
    </form>

    {{-- MODAL BATAL --}}
    <div id="cancelModal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm px-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md animate-scale-in">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Batalkan?</h3>
            </div>

            <div class="px-6 py-4 text-sm text-gray-700 leading-relaxed">
                Data yang sudah kamu isi <span class="font-semibold">belum disimpan</span>.
                Kalau dibatalkan, semua perubahan akan hilang.
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <button type="button" onclick="closeCancelModal()"
                    class="w-full sm:w-auto px-4 py-2 rounded-lg text-sm font-semibold bg-gray-200 hover:bg-gray-300">
                    Tetap di Halaman
                </button>

                <a href="{{ route('admin.executive-produk-variant') }}"
                    class="w-full sm:w-auto text-center px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 text-white hover:bg-red-700">
                    Ya, Batalkan
                </a>
            </div>
        </div>
    </div>

    <style>
        @keyframes scaleIn {
            from {
                transform: scale(.95);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-scale-in {
            animation: scaleIn .15s ease-out;
        }
    </style>

    <script>
        const modal = document.getElementById('cancelModal');

        function openCancelModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCancelModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeCancelModal();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('flex')) closeCancelModal();
        });
    </script>
@endsection
