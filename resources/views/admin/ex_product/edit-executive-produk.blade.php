@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-produk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('addCss')
    {{-- CSS Wajib FilePond --}}
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
    @php
        $canEditProduct = auth()->user()->can('edit produk');
        $canReadProduct = auth()->user()->can('baca produk');
        $isReadOnly = !$canEditProduct;

        $readonlyClass =
            'w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700 cursor-not-allowed';
        $inputClass =
            'w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-blue-600 focus:ring-0';
    @endphp

    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Executive</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-700 hover:underline">Produk</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600">Edit Produk</span>
        </div>
    </section>

    <form action="{{ route('admin.edit-executive-produk.update', $product->id) }}" method="POST"
        enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <section class="rounded-xl border border-gray-300 bg-gray-200/80 p-5 shadow">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

                {{-- ID Produk --}}
                <div class="sm:col-span-1">
                    <label class="mb-2 block text-sm font-bold">ID Produk</label>
                    <input name="code" type="text" value="{{ old('code', $product->code) }}"
                        @if ($isReadOnly) readonly @endif
                        class="{{ $isReadOnly ? $readonlyClass : $inputClass }} @error('code') border-red-500 @enderror">
                    @error('code')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Produk --}}
                <div class="sm:col-span-1">
                    <label class="mb-2 block text-sm font-bold">Nama Produk</label>
                    <input name="name" type="text" value="{{ old('name', $product->name) }}"
                        @if ($isReadOnly) readonly @endif
                        class="{{ $isReadOnly ? $readonlyClass : $inputClass }} @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="sm:col-span-2 lg:col-span-1">
                    <label class="mb-2 block text-sm font-bold">Status</label>
                    <select name="status" @if ($isReadOnly) disabled @endif
                        class="{{ $isReadOnly ? $readonlyClass : $inputClass }}">
                        <option value="aktif" {{ old('status', $product->status) === 'aktif' ? 'selected' : '' }}>Active
                        </option>
                        <option value="nonaktif" {{ old('status', $product->status) === 'nonaktif' ? 'selected' : '' }}>
                            Inactive</option>
                    </select>

                    @if ($isReadOnly)
                        <input type="hidden" name="status" value="{{ old('status', $product->status) }}">
                    @endif
                </div>

                {{-- Deskripsi --}}
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="mb-2 block text-sm font-bold">Deskripsi Produk</label>
                    <textarea name="description" id="description" rows="5" @if ($isReadOnly) readonly @endif
                        class="{{ $isReadOnly ? $readonlyClass : $inputClass }}">{{ old('description', $product->description) }}</textarea>
                </div>

                {{-- Gambar Produk --}}
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="mb-2 block text-sm font-bold">
                        {{ $isReadOnly ? 'Gambar Produk' : 'Ganti Gambar Produk' }}
                        @if (!$isReadOnly)
                            <span class="text-xs font-normal text-gray-500">(Opsional)</span>
                        @endif
                    </label>

                    @php $currentImage = $product->getFirstMediaUrl('product_image'); @endphp

                    @if ($isReadOnly)
                        <div class="rounded-xl border border-gray-300 bg-white p-4">
                            @if ($currentImage)
                                <img src="{{ $currentImage }}"
                                    class="h-40 w-40 rounded-lg border-2 border-white object-cover shadow-md">
                            @else
                                <div
                                    class="flex h-40 w-40 items-center justify-center rounded-lg border border-gray-300 bg-gray-100 text-sm text-gray-500">
                                    Tidak ada gambar
                                </div>
                            @endif
                        </div>
                    @else
                        {{-- Tampilan Edit (Grid: Kiri Gambar Saat Ini, Kanan FilePond) --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            {{-- Box Menampilkan Gambar yang Sudah Ada --}}
                            <div
                                class="flex flex-col items-center justify-center p-4 bg-white rounded-xl border border-gray-300 shadow-sm min-h-[250px]">
                                <span class="text-xs font-bold text-gray-400 mb-2 uppercase tracking-wider">Gambar Saat
                                    Ini</span>
                                @if ($currentImage)
                                    <img src="{{ $currentImage }}" alt="Foto Produk"
                                        class="max-h-[160px] w-auto object-contain rounded-lg border border-gray-200 p-1 shadow-sm">
                                @else
                                    <div class="text-gray-400 text-sm flex flex-col items-center">
                                        <svg class="w-12 h-12 mb-1 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span>Tidak ada foto</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Box Tempat Upload Baru Menggunakan FilePond --}}
                            <div class="md:col-span-2 flex flex-col justify-center">
                                <input type="file" name="image" id="imageInput"
                                    accept="image/png, image/jpeg, image/jpg">
                                @error('image')
                                    <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
            <button type="button" onclick="openCancelModal()"
                class="inline-flex w-full items-center justify-center rounded-lg bg-red-600 px-10 py-3 text-sm font-bold text-white hover:bg-red-700 sm:w-auto">
                Batal
            </button>

            @if (!$isReadOnly)
                <button type="submit"
                    class="inline-flex w-full items-center justify-center rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800 sm:w-auto">
                    Simpan Perubahan
                </button>
            @endif
        </div>
    </form>

    {{-- MODAL BATAL --}}
    <div id="cancelModal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 px-4 backdrop-blur-sm">
        <div class="animate-scale-in w-full max-w-md rounded-xl bg-white shadow-xl">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-800">Batalkan?</h3>
            </div>
            <div class="px-6 py-4 text-sm text-gray-700">Perubahan yang kamu buat belum disimpan.</div>
            <div class="flex flex-col gap-3 border-t border-gray-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-end">
                <button type="button" onclick="closeCancelModal()"
                    class="w-full rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold hover:bg-gray-300 sm:w-auto">
                    Tetap di Sini
                </button>
                <a href="{{ route('admin.executive-produk') }}"
                    class="w-full rounded-lg bg-red-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-red-700 sm:w-auto">
                    Ya, Batalkan
                </a>
            </div>
        </div>
    </div>
@endsection

@section('addJs')
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

        document.addEventListener('DOMContentLoaded', function() {
            // FilePond Initialization
            const inputElement = document.querySelector('#imageInput');

            if (inputElement && !inputElement.classList.contains('filepond--input')) {
                FilePond.registerPlugin(
                    FilePondPluginFileValidateType,
                    FilePondPluginFileValidateSize,
                    FilePondPluginImagePreview
                );

                const customIconPlaceholder = `
                <div class="flex flex-col items-center justify-center space-y-4">
                    <div class="p-4 bg-blue-50 rounded-full transition-transform duration-300 hover:scale-110">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0l-4 4m4-4v12"></path>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="text-base font-bold text-gray-700"><span class="filepond--label-action">Klik</span> atau Tarik file baru ke sini</p>
                        <p class="text-xs text-gray-500 mt-1 font-medium">Kosongkan jika tidak ingin mengubah foto (Maksimum 3MB)</p>
                    </div>
                </div>
                `;

                FilePond.create(inputElement, {
                    storeAsFile: true,
                    acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg'],
                    maxFileSize: '3MB',
                    labelIdle: customIconPlaceholder,
                    labelFileTypeNotAllowed: 'Format file tidak didukung',
                    fileValidateTypeLabelExpectedTypes: 'Hanya PNG/JPG/JPEG',
                    labelMaxFileSizeExceeded: 'Ukuran file terlalu besar',
                    labelMaxFileSize: 'Maksimum 3MB',
                });
            }
        });
    </script>

    <script src="{{ asset('vendor/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#myTinyMce',
            plugins: 'advlist autolink lists link image charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking table emoticons template help',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen preview save print | insertfile image media template link anchor codesample | ltr rtl',
            menubar: 'file edit view insert format tools table help',
            height: 400,
            promotion: false,
            branding: false,
            readonly: {{ isset($isReadOnly) && $isReadOnly ? 'true' : 'false' }},
            license_key: 'gpl'
        });
    </script>
@endsection
