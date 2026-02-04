@extends('admin.layout.master')

@section('open-executive', 'open')
@section('menu-executive', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-executive-pengadaan-barang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    @php
        // status awal: Menunggu | Diterima | Ditolak
        $status = old('status_pengadaan', 'Menunggu');

        $items = [
            ['CA001', 'Kalsium', '200 Kg', '200 Kg'],
            ['K001', 'Kalium', '200 Kg', '150 Kg'],
            ['CL001', 'Klorida', '200 Kg', '100 Kg'],
            ['MG001', 'Magnesium', '200 Kg', '100 Kg'],
        ];
    @endphp

    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-gray-700">Pengadaan Barang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Tambah Pemesanan</a>
        </div>
    </section>

    <form action="#" method="POST" class="space-y-4">
        @csrf

        {{-- BARIS ATAS --}}
        <section class="bg-white p-5 shadow border border-gray-300 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Nama Pemesan</label>
                    <input type="text" name="customer_name" value="Bambang Pratama Putra Hadi" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Provinsi</label>
                    <input type="text" name="provinsi" placeholder="" value="Suamtera Utara" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Pemesanan</label>
                    <input type="date" name="order_date" value="{{ old('order_date', now()->format('Y-m-d')) }}"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                </div>
            </div>
        </section>

        {{-- ITEM ROWS --}}
        @foreach ($items as $i => $it)
            <section class="bg-white p-5 shadow border border-gray-300 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    {{-- ID Barang --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">ID Barang</label>
                        <input name="items[{{ $i }}][id_barang]" value="{{ $it[0] }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900" />
                    </div>

                    {{-- Nama Barang --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Nama Barang</label>
                        <input name="items[{{ $i }}][nama_barang]" value="{{ $it[1] }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900" />
                    </div>

                    {{-- Stok Tersedia --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Stok Tersedia</label>
                        <input name="items[{{ $i }}][stok]" value="{{ $it[2] }}" readonly
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900" />
                    </div>

                    {{-- Jumlah Pesanan --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">
                            Jumlah Pesanan <span class="text-red-500">*</span>
                        </label>
                        <input type="" name="items[{{ $i }}][qty]" value="{{ $it[2] }}" min="1" readonly
                            
                            class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                    </div>
                </div>
            </section>
        @endforeach

        {{-- BLOK UBAH STATUS (seperti struktur kode pertama) --}}
        <section class="bg-white p-5 shadow border border-gray-300 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Ubah Status</label>
                    <input type="text" value="{{ now()->format('d/m/Y') }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed" />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Penanggung Jawab</label>
                    <input type="text" value="Bambang Pratama Putra Hadi" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed" />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Status Pengadaan</label>
                    <select id="statusPengadaan" name="status_pengadaan"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                        <option value="Menunggu" {{ $status === 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="Diterima" {{ $status === 'Diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="Ditolak" {{ $status === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
            </div>
        </section>

        {{-- BLOK CATATAN DITOLAK (muncul hanya jika Ditolak) --}}
        <section id="blokDitolak" class="space-y-4 {{ $status === 'Ditolak' ? '' : 'hidden' }}">
            <section class="bg-white p-5 shadow border border-gray-300 rounded-lg">
                <div class="text-xs font-bold text-gray-800 mb-3">Catatan Penolakan</div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Catatan</label>
                    <textarea id="catatanDitolak" name="catatan_ditolak" rows="4"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500"
                        placeholder="Tulis catatan penolakan...">{{ old('catatan_ditolak') }}</textarea>
                    <div class="mt-2 text-[11px] text-gray-600">
                        Wajib diisi jika status <span class="font-bold">Ditolak</span>.
                    </div>
                </div>
            </section>
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

                <a href="{{ route('admin.gudang-pengadaan-barang') }}"
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

        // =========================
        // STATUS SYNC: tampilkan catatan hanya jika Ditolak
        // =========================
        const statusSelect = document.getElementById('statusPengadaan');
        const blokDitolak = document.getElementById('blokDitolak');
        const catatanDitolak = document.getElementById('catatanDitolak');

        function syncBlocks() {
            const val = statusSelect.value;

            if (val === 'Ditolak') {
                blokDitolak.classList.remove('hidden');
                if (catatanDitolak) catatanDitolak.setAttribute('required', 'required');
            } else {
                blokDitolak.classList.add('hidden');
                if (catatanDitolak) catatanDitolak.removeAttribute('required');
            }
        }

        statusSelect.addEventListener('change', syncBlocks);
        syncBlocks();
    </script>
@endsection
