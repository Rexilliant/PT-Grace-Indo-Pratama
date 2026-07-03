@extends('admin.layout.master')

@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-produk-variant', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('addCss')
    {{-- CSS Wajib FilePond --}}
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">

    <style>
        /* Styling FilePond */
        .filepond--root {
            font-family: inherit;
            margin-bottom: 0;
            min-height: 250px !important;
        }

        .filepond--drop-label {
            background-color: transparent !important;
            cursor: pointer;
            min-height: 250px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 1.5rem !important;
        }

        .filepond--drop-label>div {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 !important;
            padding: 0 !important;
        }

        .filepond--panel-root {
            background-color: #ffffff !important;
            border: 2px dashed #d1d5db !important;
            border-radius: 1rem !important;
            transition: all 0.3s ease;
        }

        .filepond--root:hover .filepond--panel-root {
            border-color: #3b82f6 !important;
            background-color: #eff6ff !important;
        }

        .filepond--label-action {
            text-decoration: none;
            cursor: pointer;
            color: #3b82f6;
            font-weight: 700;
        }

        /* Animasi Modal */
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
@endsection

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

    <form action="{{ route('admin.add-executive-produk-variant.store') }}" method="POST" enctype="multipart/form-data"
        class="space-y-4">
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-4">
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-4">
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

            {{-- Gambar Produk (FilePond) --}}
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="block text-sm font-bold mb-2 text-gray-800">
                    Gambar Produk <span class="text-red-500">*</span>
                </label>

                <div class="mt-1">
                    <input type="file" name="image" id="imageInput"
                        accept="image/png, image/jpeg, image/jpg, image/webp" required>
                </div>

                @error('image')
                    <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
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
                Simpan
            </button>
        </div>
    </form>

    {{-- MODAL BATAL (Tetap menggunakan desain asli variant) --}}
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
@endsection

@section('addJs')
    {{-- FilePond JS --}}
    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>

    <script>
        // Modal Logic
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

        // FilePond Initialization
        document.addEventListener('DOMContentLoaded', function() {
            const inputElement = document.querySelector('#imageInput');

            if (inputElement) {
                FilePond.registerPlugin(
                    FilePondPluginFileValidateType,
                    FilePondPluginFileValidateSize,
                    FilePondPluginImagePreview
                );

                const customIconPlaceholder = `
                <div class="flex flex-col items-center justify-center space-y-4 py-4">
                    <div class="p-4 bg-blue-50 rounded-full transition-transform duration-300 hover:scale-110">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="text-base font-bold text-gray-700"><span class="filepond--label-action">Klik</span> atau Tarik gambar ke sini</p>
                        <p class="text-xs text-gray-500 mt-1 font-medium">PNG, JPG, WEBP (Maksimum 2MB)</p>
                    </div>
                </div>
                `;

                FilePond.create(inputElement, {
                    storeAsFile: true,
                    acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'],
                    maxFileSize: '2MB',
                    labelIdle: customIconPlaceholder,
                    labelFileTypeNotAllowed: 'Format file tidak didukung',
                    fileValidateTypeLabelExpectedTypes: 'Hanya PNG/JPG/WEBP',
                    labelMaxFileSizeExceeded: 'Ukuran file terlalu besar',
                    labelMaxFileSize: 'Maksimum 2MB',
                });
            }
        });
    </script>
@endsection
