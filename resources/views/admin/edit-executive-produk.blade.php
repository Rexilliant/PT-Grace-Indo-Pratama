@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-produk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
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

        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                {{-- ID Produk --}}
                <div class="sm:col-span-1">
                    <label class="block text-sm font-bold mb-2">ID Produk</label>
                    <input name="code" type="text" value="{{ old('code', $product->code) }}"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-blue-600 focus:ring-0 @error('code') border-red-500 @enderror">
                    @error('code')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Produk --}}
                <div class="sm:col-span-1">
                    <label class="block text-sm font-bold mb-2">Nama Produk</label>
                    <input name="name" type="text" value="{{ old('name', $product->name) }}"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-blue-600 focus:ring-0 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="sm:col-span-2 lg:col-span-1">
                    <label class="block text-sm font-bold mb-2">Status</label>
                    <select name="status"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-blue-600 focus:ring-0">
                        <option value="aktif" {{ old('status', $product->status) === 'aktif' ? 'selected' : '' }}>Active
                        </option>
                        <option value="nonaktif" {{ old('status', $product->status) === 'nonaktif' ? 'selected' : '' }}>
                            Inactive</option>
                    </select>
                </div>

                {{-- Deskripsi --}}
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-bold mb-2">Deskripsi Produk</label>
                    <textarea name="description" rows="5"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-blue-600 focus:ring-0">{{ old('description', $product->description) }}</textarea>
                </div>

                {{-- Gambar Produk (World Class Upload - Logic Edit) --}}
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-bold mb-2">Ganti Gambar Produk <span
                            class="text-xs font-normal text-gray-500">(Opsional)</span></label>
                    <div class="relative group">
                        <div id="dropzone"
                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-xl transition-all duration-300 border-gray-300 bg-white group-hover:border-blue-500 group-hover:bg-blue-50/30">
                            <div class="space-y-2 text-center">
                                @php $currentImage = $product->getFirstMediaUrl('product_image'); @endphp
                                <div class="flex justify-center">
                                    <img id="imagePreview" src="{{ $currentImage }}"
                                        class="{{ $currentImage ? '' : 'hidden' }} h-40 w-40 object-cover rounded-lg shadow-md border-2 border-white transition-all duration-300">
                                    <div id="uploadPlaceholder" class="{{ $currentImage ? 'hidden' : '' }}">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                            viewBox="0 0 48 48">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="image"
                                        class="relative cursor-pointer rounded-md font-bold text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                        <span>Ganti file</span>
                                        <input id="image" name="image" type="file" class="sr-only"
                                            accept="image/*">
                                    </label>
                                    <p class="pl-1 text-gray-500">atau tarik ke sini</p>
                                </div>
                                <p class="text-xs text-gray-400 italic">Kosongkan jika tidak ingin mengganti gambar lama</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
            <button type="button" onclick="openCancelModal()"
                class="w-full sm:w-auto inline-flex items-center justify-center rounded-lg bg-red-600 px-10 py-3 text-sm font-bold text-white hover:bg-red-700">Batal</button>
            <button type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">Simpan
                Perubahan</button>
        </div>
    </form>

    {{-- MODAL BATAL --}}
    <div id="cancelModal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm px-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md animate-scale-in">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Batalkan?</h3>
            </div>
            <div class="px-6 py-4 text-sm text-gray-700">Perubahan yang kamu buat belum disimpan.</div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <button type="button" onclick="closeCancelModal()"
                    class="w-full sm:w-auto px-4 py-2 rounded-lg text-sm font-semibold bg-gray-200 hover:bg-gray-300">Tetap
                    di Sini</button>
                <a href="{{ route('admin.executive-produk') }}"
                    class="w-full sm:w-auto text-center px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 text-white hover:bg-red-700">Ya,
                    Batalkan</a>
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

            // Drag and Drop Logic
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
@endsection
