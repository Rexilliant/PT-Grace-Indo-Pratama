@extends('admin.layout.master')

@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-produk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')


@section('addCss')
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">

    <style>
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
        }

        .filepond--drop-label>div {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 !important;
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

        @keyframes scaleIn {
            from {
                transform: scale(0.97);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-scale-in {
            animation: scaleIn 0.2s ease-out forwards;
        }
    </style>
@endsection

@section('content')

    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Executive</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-700 hover:underline">Produk</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600 font-bold">Tambah Produk</span>
        </div>
    </section>

    <form action="{{ route('admin.add-executive-produk-baru.store') }}" method="POST" enctype="multipart/form-data"
        class="space-y-4">
        @csrf

        {{-- FORM CARD --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                {{-- ID Produk --}}
                <div class="sm:col-span-1">
                    <label class="block text-sm font-bold mb-2">ID Produk</label>
                    <input name="code" type="text" placeholder="Contoh: BHOSEXT" value="{{ old('code') }}"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-blue-600 focus:ring-0 @error('code') border-red-500 @enderror">
                    @error('code')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Produk --}}
                <div class="sm:col-span-1">
                    <label class="block text-sm font-bold mb-2">Nama Produk</label>
                    <input name="name" type="text" placeholder="Contoh: BHOS Ekstra" value="{{ old('name') }}"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-blue-600 focus:ring-0 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="sm:col-span-2 lg:col-span-1">
                    <label class="block text-sm font-bold mb-2">Status</label>
                    <select name="status"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-blue-600 focus:ring-0 @error('status') border-red-500 @enderror">
                        <option value="" disabled {{ old('status') ? '' : 'selected' }}>Pilih Status</option>
                        <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Active</option>
                        <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-bold mb-2">Deskripsi Produk</label>
                    <textarea name="description" id="description" rows="5" placeholder="Tuliskan deskripsi produk..."
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-blue-600 focus:ring-0 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-bold mb-2 text-gray-800">
                        Gambar Produk <span class="text-red-500">*</span>
                    </label>

                    <input type="file" name="image" id="imageInput" accept="image/png, image/jpeg, image/jpg" required>

                    @error('image')
                        <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </section>

        {{-- ACTIONS --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 pt-2">
            <button type="button" onclick="openCancelModal()"
                class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl bg-white border border-gray-300 px-10 py-3.5 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                Batal
            </button>
            <button type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl bg-[#2D2ACD] px-10 py-3.5 text-sm font-bold text-white hover:bg-blue-800 transition-all shadow-lg shadow-blue-200">
                Simpan Produk
            </button>
        </div>
    </form>

    {{-- MODAL BATAL --}}
    <div id="cancelModal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/60 backdrop-blur-sm px-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md animate-scale-in">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Batalkan Tambah Produk?</h3>
                <p class="text-gray-500 mt-2">Data yang sudah kamu isi tidak akan disimpan.</p>
            </div>
            <div class="flex gap-3 p-6 pt-0">
                <button type="button" onclick="closeCancelModal()"
                    class="flex-1 px-4 py-3 rounded-xl text-sm font-bold bg-gray-100 text-gray-700 hover:bg-gray-200">Lanjut
                    Isi</button>
                <a href="{{ route('admin.executive-produk') }}"
                    class="flex-1 text-center px-4 py-3 rounded-xl text-sm font-bold bg-red-600 text-white hover:bg-red-700">Ya,
                    Batal</a>
            </div>
        </div>
    </div>
@endsection

@section('addJs')

    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        document.addEventListener('DOMContentLoaded', function() {
            // Registrasi Plugin
            FilePond.registerPlugin(
                FilePondPluginFileValidateType,
                FilePondPluginFileValidateSize,
                FilePondPluginImagePreview
            );

            const inputElement = document.querySelector('#imageInput');

            // Konfigurasi FilePond
            FilePond.create(inputElement, {
                storeAsFile: true,
                acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg'],
                maxFileSize: '2MB',
                labelIdle: `
            <div class="flex flex-col items-center justify-center space-y-4 py-8">
                <div class="p-4 bg-blue-50 rounded-full">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="text-center">
                    <p class="text-base font-bold text-gray-700"><span class="filepond--label-action">Klik</span> atau Tarik file ke sini</p>
                    <p class="text-xs text-gray-500 mt-1 font-medium">PNG, JPG, JPEG (Maksimum 2MB)</p>
                </div>
            </div>
        `,
                labelFileTypeNotAllowed: 'Format file tidak didukung',
                fileValidateTypeLabelExpectedTypes: 'Hanya PNG/JPG/JPEG',
                labelMaxFileSizeExceeded: 'Ukuran file terlalu besar',
                labelMaxFileSize: 'Maksimum 2MB',
            });
        });
    </script>

    <!-- TinyMCE Local -->
    <script src="{{ asset('vendor/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: 'textarea[name="description"]',
            plugins: 'advlist autolink lists link image charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking table emoticons template help',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen preview save print | insertfile image media template link anchor codesample | ltr rtl',
            menubar: 'file edit view insert format tools table help',
            height: 400,
            promotion: false,
            branding: false,
            license_key: 'gpl'
        });
    </script>
@endsection
