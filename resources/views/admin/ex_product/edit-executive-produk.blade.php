@extends('admin.layout.master')

@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-produk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    @php
        $canEditProduct = auth()->user()->can('edit produk');
        $isReadOnly = !$canEditProduct;

        $readonlyClass =
            'w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-700 cursor-not-allowed';
        $inputClass =
            'w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-blue-600 focus:ring-0';
    @endphp

    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span>Executive</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="hover:underline">Produk</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600">Edit Produk</span>
        </div>
    </section>

    <form action="{{ route('admin.edit-executive-produk.update', $product->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <section class="rounded-xl border border-gray-300 bg-gray-200/80 p-5 shadow">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">

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
                <div class="sm:col-span-1">
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
        <div class="w-full max-w-md rounded-xl bg-white shadow-xl">
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
    <script>
        function openCancelModal() {
            document.getElementById('cancelModal').classList.remove('hidden');
            document.getElementById('cancelModal').classList.add('flex');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
            document.getElementById('cancelModal').classList.remove('flex');
        }
    </script>
@endsection
