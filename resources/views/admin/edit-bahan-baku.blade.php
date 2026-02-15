@extends('admin.layout.master')

@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-bahan-baku', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')

    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-700 hover:underline">Bahan Baku</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600">
                Edit Bahan Baku
            </span>
        </div>
    </section>

    {{-- FORM EDIT --}}
    <form action="{{ route('admin.update-bahan-baku', $material->id) }}" method="POST">
        @csrf
        @method('PUT')

        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                {{-- Kode Barang --}}
                <div>
                    <label class="block text-sm font-bold mb-2">Kode Barang</label>
                    <input name="kode_barang" type="text" placeholder="Contoh: CA0001"
                        value="{{ old('kode_barang', $material->code) }}"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-blue-600 focus:ring-0">
                </div>

                {{-- Bahan Baku --}}
                <div>
                    <label class="block text-sm font-bold mb-2">Bahan Baku</label>
                    <input name="bahan_baku" type="text" placeholder="Contoh: Kalsium"
                        value="{{ old('bahan_baku', $material->name) }}"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-blue-600 focus:ring-0">
                </div>

                {{-- Stok Tersedia --}}
                <div>
                    <label class="block text-sm font-bold mb-2">Unit</label>
                    <input name="unit" type="text" placeholder="Contoh: Kg / Liter / Box"
                        value="{{ old('bahan_baku', $material->unit) }}"
                        class="w-full rounded-md border border-gray-400 bg-white
                               px-3 py-2.5 text-sm font-semibold text-gray-900
                               focus:border-blue-600 focus:ring-0">
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-bold mb-2">Status</label>
                    <select name="status"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-blue-600 focus:ring-0">

                        <option value="active" {{ old('status', $material->status) == 'active' ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="inactive" {{ old('status', $material->status) == 'inactive' ? 'selected' : '' }}>
                            Inactive
                        </option>

                    </select>
                </div>

            </div>
        </section>

        {{-- ACTIONS --}}
        <div class="flex items-center justify-end gap-4 pt-2">
            <button type="button" onclick="openCancelModal()"
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
    <div id="cancelModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 animate-scale-in">
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

                <a href="{{ route('admin.gudang-bahan-baku') }}"
                    class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 text-white hover:bg-red-700">
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
    </script>

@endsection
