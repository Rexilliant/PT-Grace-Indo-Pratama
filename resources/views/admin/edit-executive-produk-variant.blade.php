@extends('admin.layout.master')

{{-- sidebar active --}}
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
            <span class="text-blue-600 font-bold">Edit Produk</span>
        </div>
    </section>

    <form action="{{ route('admin.executive-produk-variant.update', $variant->id) }}" method="POST"
        enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        {{-- FORM CARD --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">

            {{-- Relasi Produk --}}
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Pilih Produk</label>
                <select name="product_id"
                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900
                           focus:border-blue-600 focus:ring-0 @error('product_id') border-red-500 focus:border-red-600 @enderror">
                    <option value="" disabled {{ old('product_id', $variant->product_id) ? '' : 'selected' }}>
                        Pilih Produk
                    </option>
                    @foreach ($products as $prod)
                        <option value="{{ $prod->id }}"
                            {{ (string) old('product_id', $variant->product_id) === (string) $prod->id ? 'selected' : '' }}>
                            {{ $prod->code }} - {{ $prod->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-4">
                {{-- SKU --}}
                <div>
                    <label class="block text-sm font-bold mb-2">SKU</label>
                    <input name="sku" type="text" value="{{ old('sku', $variant->sku) }}"
                        placeholder="Contoh: BHOS-001"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900
                               focus:border-blue-600 focus:ring-0 @error('sku') border-red-500 focus:border-red-600 @enderror">
                    @error('sku')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Produk --}}
                <div>
                    <label class="block text-sm font-bold mb-2">Nama Produk</label>
                    <input name="name" type="text" value="{{ old('name', $variant->name) }}"
                        placeholder="Contoh: BHOS Ekstra"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900
                               focus:border-blue-600 focus:ring-0 @error('name') border-red-500 focus:border-red-600 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ukuran Kemasan --}}
                <div>
                    <label class="block text-sm font-bold mb-2">Ukuran Kemasan</label>
                    <input name="pack_size" type="number" min="0"
                        value="{{ old('pack_size', $variant->pack_size) }}" placeholder="Contoh: 50"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900
                               focus:border-blue-600 focus:ring-0 @error('pack_size') border-red-500 focus:border-red-600 @enderror">
                    @error('pack_size')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-4">
                {{-- Satuan --}}
                <div>
                    <label class="block text-sm font-bold mb-2">Satuan</label>
                    <input name="unit" type="text" value="{{ old('unit', $variant->unit) }}"
                        placeholder="Contoh: Kg/Liter"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900
                               focus:border-blue-600 focus:ring-0 @error('unit') border-red-500 focus:border-red-600 @enderror">
                    @error('unit')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Harga --}}
                <div>
                    <label class="block text-sm font-bold mb-2">Harga</label>
                    <input name="price" type="number" min="0" value="{{ old('price', $variant->price) }}"
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
                        <option value="" disabled {{ old('status', $variant->status) ? '' : 'selected' }}>
                            Pilih Status
                        </option>
                        <option value="aktif" {{ old('status', $variant->status) === 'aktif' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="nonaktif" {{ old('status', $variant->status) === 'nonaktif' ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Ambil Data Gambar Lama --}}
            @php
                $currentImage = $variant->getFirstMediaUrl('product_variant_image');
                $hasImage = !empty($currentImage);
                $displayImage = $currentImage ?: asset('build/image/bhos-logo.png');
            @endphp

            {{-- Gambar Produk (Drag & Drop Kelas Dunia - Mode Edit) --}}
            <div class="mb-2">
                <label class="block text-sm font-bold mb-2 text-gray-800">
                    Gambar Produk <span class="text-gray-500 text-xs font-normal ml-1">(Opsional)</span>
                </label>

                <label for="image" id="dropzone"
                    class="relative group cursor-pointer mt-1 flex flex-col items-center justify-center min-h-[250px] w-full p-4 border-2 border-dashed rounded-2xl transition-all duration-300 overflow-hidden 
                    @error('image') border-red-400 bg-red-50 @else border-gray-300 bg-white hover:border-blue-500 hover:bg-blue-50/50 @enderror">

                    {{-- TIDAK ADA REQUIRED DI SINI --}}
                    <input id="image" name="image" type="file" class="sr-only" accept="image/*">

                    {{-- Tampilan Sebelum Upload (Placeholder) - Sembunyi jika ada gambar lama --}}
                    <div id="uploadPlaceholder"
                        class="flex flex-col items-center justify-center space-y-4 py-8 {{ $hasImage ? 'hidden' : '' }}">
                        <div class="p-4 bg-blue-50 rounded-full group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="text-base font-bold text-gray-700">Klik di mana saja untuk upload gambar baru</p>
                            <p class="text-xs text-gray-500 mt-1 font-medium italic">Biarkan kosong jika tidak ingin
                                mengubah gambar.</p>
                        </div>
                    </div>

                    {{-- Tampilan Preview (Muncul otomatis jika ada gambar lama) --}}
                    <div id="previewContainer"
                        class="absolute inset-0 flex flex-col items-center justify-center bg-white p-2 animate-scale-in {{ $hasImage ? '' : 'hidden' }}">
                        <div class="relative w-full h-full flex flex-col items-center justify-center">
                            <img id="imagePreview" src="{{ $displayImage }}"
                                class="max-h-[180px] w-auto object-contain rounded-lg shadow-sm" alt="Preview">

                            {{-- Overlay Info Saat Hover --}}
                            <div class="mt-4 flex flex-col items-center">
                                <span id="imageStatusBadge"
                                    class="px-3 py-1 bg-gray-100 text-gray-700 text-[10px] font-bold uppercase tracking-widest rounded-full mb-1">
                                    Gambar Saat Ini
                                </span>
                                <p class="text-xs font-bold text-gray-400 group-hover:text-blue-600 transition-colors">
                                    Klik lagi untuk mengganti gambar
                                </p>
                            </div>
                        </div>
                    </div>
                </label>

                @error('image')
                    <div class="flex items-center mt-3 text-red-600 font-bold">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-[11px] uppercase tracking-wider">{{ $message }}</p>
                    </div>
                @enderror
            </div>
        </section>

        {{-- ACTIONS --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 pt-2">
            <button type="button" onclick="openCancelModal()"
                class="w-full sm:w-auto inline-flex items-center justify-center rounded-lg
                       bg-red-600 px-10 py-3 text-sm font-bold text-white hover:bg-red-700">
                Batal
            </button>

            <button type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center rounded-lg
                       bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                Simpan Perubahan
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
                Perubahan yang kamu buat <span class="font-semibold">belum disimpan</span>.
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
            animation: scaleIn .15s ease-out forwards;
        }
    </style>

    <script>
        // Logic Modal Batal
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

        // Logic Drag & Drop Upload untuk Edit Page
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');
            const previewContainer = document.getElementById('previewContainer');
            const placeholder = document.getElementById('uploadPlaceholder');
            const dropzone = document.getElementById('dropzone');
            const statusBadge = document.getElementById('imageStatusBadge');

            // Simpan state awal gambar
            const defaultImageSrc = "{{ $displayImage }}";
            const hasInitialImage = {{ $hasImage ? 'true' : 'false' }};

            function handleFile(file) {
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        placeholder.classList.add('hidden');
                        previewContainer.classList.remove('hidden');

                        // Ubah badge status jadi menandakan ada gambar baru yang siap diupload
                        if (statusBadge) {
                            statusBadge.textContent = "Gambar Baru Terpilih";
                            statusBadge.className =
                                "px-3 py-1 bg-blue-100 text-blue-700 text-[10px] font-bold uppercase tracking-widest rounded-full mb-1";
                        }
                    }
                    reader.readAsDataURL(file);
                }
            }

            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];

                if (!file) {
                    // Kalau user buka dialog file tapi click Cancel
                    if (hasInitialImage) {
                        // Kembali ke gambar lama
                        imagePreview.src = defaultImageSrc;
                        previewContainer.classList.remove('hidden');
                        placeholder.classList.add('hidden');
                        if (statusBadge) {
                            statusBadge.textContent = "Gambar Saat Ini";
                            statusBadge.className =
                                "px-3 py-1 bg-gray-100 text-gray-700 text-[10px] font-bold uppercase tracking-widest rounded-full mb-1";
                        }
                    } else {
                        // Kalau memang dari awal gak ada gambar
                        imagePreview.src = '';
                        previewContainer.classList.add('hidden');
                        placeholder.classList.remove('hidden');
                    }
                    return;
                }

                handleFile(file);
            });

            // UX Drag & Drop
            ['dragenter', 'dragover'].forEach(name => {
                dropzone.addEventListener(name, (e) => {
                    e.preventDefault();
                    dropzone.classList.add('border-blue-500', 'bg-blue-50/50');
                });
            });

            ['dragleave', 'drop'].forEach(name => {
                dropzone.addEventListener(name, (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('border-blue-500', 'bg-blue-50/50');
                });
            });

            dropzone.addEventListener('drop', (e) => {
                const file = e.dataTransfer.files[0];
                imageInput.files = e.dataTransfer.files; // Assign file ke input
                handleFile(file);
            });
        });
    </script>
@endsection
