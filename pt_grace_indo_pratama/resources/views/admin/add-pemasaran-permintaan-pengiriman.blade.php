@extends('admin.layout.master')

{{-- sidebar active (sesuaikan menu kamu) --}}
@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-permintaan-pengiriman', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    @php
        // ===== DUMMY ITEM (ganti dari DB nanti) =====
        // ini boleh tetap dummy karena produk biasanya datang dari "pilih produk"
        $items = [['BHOS001', 'BHOS Ekstra'], ['BHOS002', 'BHOS Turbo']];

        $armadaList = ['Bus', 'Truk', 'Ekspedisi'];
        $jenisList = ['Perseorangan', 'Pesanan', 'Instansi'];

        // tanggal hari ini (untuk ditampilkan & dikirim)
        $today = now()->format('Y-m-d');

        // penanggung jawab dari user login (sesuaikan field user kamu)
        $pjName = auth()->user()->name ?? (auth()->user()->nama ?? 'User');
    @endphp

    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Pemasaran</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-700 hover:underline">Permintaan Pengiriman</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600 font-bold">Tambah Baru</span>
        </div>
    </section>

    <form action="#" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- BLOK HEADER (abu-abu) --}}
        <section class="bg-gray-200/80 p-5 shadow border border-gray-300 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                {{-- Tanggal Pengajuan (hari ini, readonly) --}}
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Pengajuan</label>

                    {{-- kirim value ke server --}}
                    <input type="hidden" name="tgl_pengajuan" value="{{ $today }}">

                    {{-- tampilkan ke user --}}
                    <input type="date" value="{{ $today }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                </div>

                {{-- Tanggal Permintaan Pengiriman (boleh pilih) --}}
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Tanggal Permintaan Pengiriman</label>
                    <input type="date" name="tgl_permintaan" value="{{ old('tgl_permintaan') }}"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                </div>

                {{-- Penanggung Jawab (ambil dari login, readonly) --}}
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Penanggung Jawab</label>

                    {{-- kirim ke server kalau kamu butuh simpan nama PJ --}}
                    <input type="hidden" name="pj" value="{{ $pjName }}">

                    <input type="text" value="{{ $pjName }}" readonly
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                </div>

                {{-- Jenis Pengiriman --}}
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Jenis Pengiriman</label>
                    <select name="jenis_pengiriman"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                        <option value="" {{ old('jenis_pengiriman') ? '' : 'selected' }} disabled>Pilih Jenis</option>
                        @foreach ($jenisList as $j)
                            <option value="{{ $j }}" {{ old('jenis_pengiriman') === $j ? 'selected' : '' }}>
                                {{ $j }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Provinsi (manual input) --}}
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Provinsi</label>
                    <input type="text" name="provinsi" value="{{ old('provinsi') }}" placeholder="Contoh: Riau"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                </div>

                {{-- Armada --}}
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Armada Pengiriman</label>
                    <select name="armada"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                        <option value="" {{ old('armada') ? '' : 'selected' }} disabled>Pilih Armada</option>
                        @foreach ($armadaList as $a)
                            <option value="{{ $a }}" {{ old('armada') === $a ? 'selected' : '' }}>
                                {{ $a }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Nama Penerima (kosong, contoh lewat placeholder saja) --}}
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Nama Penerima</label>
                    <input type="text" name="nama_penerima" value="{{ old('nama_penerima') }}"
                        placeholder="Masukkan nama penerima"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                </div>

                {{-- Kontak Penerima (kosong, contoh lewat placeholder saja) --}}
                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Kontak Penerima</label>
                    <input type="text" name="kontak" value="{{ old('kontak') }}" placeholder="Contoh: 0812xxxxxxx"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Alamat Lengkap</label>
                    <textarea name="alamat" rows="4" placeholder="Masukkan alamat lengkap"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">{{ old('alamat') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-800 mb-2.5">Tinggalkan Pesan?</label>
                    <textarea name="catatan" rows="3" placeholder="Opsional"
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">{{ old('catatan') }}</textarea>
                </div>
            </div>
        </section>

        {{-- LIST ITEM (kartu hijau) --}}
        <section class="space-y-4">
            @foreach ($items as $i => $row)
                <section class="bg-[#a7dfb2] p-5 shadow border border-[#68b97a] rounded-xl">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-2.5">ID Produk</label>
                            <input name="items[{{ $i }}][id_produk]" value="{{ $row[0] }}" readonly
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-2.5">Nama Produk</label>
                            <input name="items[{{ $i }}][nama_produk]" value="{{ $row[1] }}" readonly
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 cursor-not-allowed">
                        </div>

                        {{-- Jumlah Permintaan (kosong, jangan isi contoh langsung) --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-2.5">Jumlah Permintaan</label>
                            <input name="items[{{ $i }}][jumlah]" value="{{ old("items.$i.jumlah") }}"
                                placeholder="Contoh: 150 Ltr"
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500">
                        </div>
                    </div>
                </section>
            @endforeach
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
    <div id="cancelModal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-2 sm:p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-auto animate-scale-in overflow-hidden">
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

                <a href="{{ route('admin.pemasaran-permintaan-pengiriman') }}"
                    class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 text-white hover:bg-red-700">
                    Ya, Batalkan
                </a>
            </div>
        </div>
    </div>

    <style>
        @keyframes scaleIn {
            from {
                transform: scale(.98);
                opacity: .6;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-scale-in {
            animation: scaleIn .12s ease-out;
        }
    </style>

    <script>
        const cancelModal = document.getElementById('cancelModal');

        function openCancelModal() {
            cancelModal.classList.remove('hidden');
            cancelModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeCancelModal() {
            cancelModal.classList.add('hidden');
            cancelModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        // close when click backdrop
        cancelModal?.addEventListener('click', (e) => {
            if (e.target === cancelModal) closeCancelModal();
        });

        // ESC close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && cancelModal && !cancelModal.classList.contains('hidden')) {
                closeCancelModal();
            }
        });
    </script>
@endsection
