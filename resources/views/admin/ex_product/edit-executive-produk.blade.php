@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-produk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

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
                    <select name="status"
                        @if ($isReadOnly) disabled @endif
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
                    <textarea name="description" id="description" rows="5"
                        @if ($isReadOnly) readonly @endif
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
                                <img src="{{ $currentImage }}" class="h-40 w-40 rounded-lg border-2 border-white object-cover shadow-md">
                            @else
                                <div class="flex h-40 w-40 items-center justify-center rounded-lg border border-gray-300 bg-gray-100 text-sm text-gray-500">
                                    Tidak ada gambar
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="relative group">
                            <div id="dropzone"
                                class="mt-1 flex justify-center rounded-xl border-2 border-dashed border-gray-300 bg-white px-6 pb-6 pt-5 transition-all duration-300 group-hover:border-blue-500 group-hover:bg-blue-50/30">
                                <div class="space-y-2 text-center">
                                    <div class="flex justify-center">
                                        <img id="imagePreview" src="{{ $currentImage }}"
                                            class="{{ $currentImage ? '' : 'hidden' }} h-40 w-40 rounded-lg border-2 border-white object-cover shadow-md transition-all duration-300">
                                        <div id="uploadPlaceholder" class="{{ $currentImage ? 'hidden' : '' }}">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                                viewBox="0 0 48 48">
                                                <path
                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex justify-center text-sm text-gray-600">
                                        <label for="image"
                                            class="relative cursor-pointer rounded-md font-bold text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                            <span>Ganti file</span>
                                            <input id="image" name="image" type="file" class="sr-only"
                                                accept="image/*">
                                        </label>
                                        <p class="pl-1 text-gray-500">atau tarik ke sini</p>
                                    </div>
                                    <p class="text-xs italic text-gray-400">Kosongkan jika tidak ingin mengganti gambar lama</p>
                                </div>
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
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');
            const placeholder = document.getElementById('uploadPlaceholder');
            const dropzone = document.getElementById('dropzone');

            if (!imageInput || !dropzone) return;

            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    imagePreview.src = URL.createObjectURL(file);
                    imagePreview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                } else {
                    @if ($currentImage)
                        imagePreview.src = "{{ $currentImage }}";
                        imagePreview.classList.remove('hidden');
                        placeholder.classList.add('hidden');
                    @else
                        imagePreview.classList.add('hidden');
                        placeholder.classList.remove('hidden');
                    @endif
                }
            });

            ['dragenter', 'dragover'].forEach(name => dropzone.addEventListener(name, (e) => {
                e.preventDefault();
                dropzone.classList.add('border-blue-500', 'bg-blue-50/50');
            }));

            ['dragleave', 'drop'].forEach(name => dropzone.addEventListener(name, (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-blue-500', 'bg-blue-50/50');
            }));

            dropzone.addEventListener('drop', (e) => {
                const file = e.dataTransfer.files[0];
                imageInput.files = e.dataTransfer.files;
                if (file) {
                    imagePreview.src = URL.createObjectURL(file);
                    imagePreview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
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
            readonly: {{ isset($isReadOnly) && $isReadOnly ? 'true' : 'false' }},
            license_key: 'gpl'
        });
    </script>
@endsection