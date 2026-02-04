@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-gudang-laporan-produksi', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    @php
        // DUMMY DATA (nanti ganti dari DB)
        $products = [
            [
                'id' => 'BHOS002',
                'name' => 'BHOS TURBO',
                'img' => asset('build/image/bhos-logo.png'), // ganti sesuai asset kamu
            ],
            [
                'id' => 'BHOS001',
                'name' => 'BHOS EKSTRA',
                'img' => asset('build/image/bhos-logo.png'),
            ],
        ];
    @endphp

    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-gray-700 hover:underline">Produksi</a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600 font-bold">Pilih Produk</span>
        </div>
    </section>

    {{-- top bar: search + back --}}
    <section class="mb-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            {{-- Search --}}
            <div class="relative w-full max-w-2xl">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input id="searchInput" type="text" placeholder="Search for Name Product"
                    class="block w-full rounded-lg border border-gray-400 bg-gray-100
                       pl-10 pr-3 py-2.5 text-sm text-gray-900
                       focus:border-gray-500 focus:ring-0">
            </div>

            {{-- Button --}}
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center justify-center rounded-lg
                   bg-red-600 px-6 py-2.5 text-sm font-bold text-white
                   hover:bg-red-700 whitespace-nowrap">
                Kembali
            </a>
        </div>
    </section>


    {{-- cards --}}
    <section>
        <div id="productGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($products as $p)
                <a href="{{ route('admin.add-produk') }}"
                    class="product-card block text-left rounded-2xl border border-gray-300
                       bg-white shadow-sm hover:shadow-md transition overflow-hidden">

                    <div class="p-4">
                        <div class="rounded-xl border border-gray-200 overflow-hidden bg-gray-50">
                            <img src="{{ $p['img'] }}" alt="{{ $p['name'] }}" class="w-full h-44 object-cover">
                        </div>

                        <div class="pt-3">
                            <div class="text-sm font-extrabold text-gray-800">
                                {{ $p['name'] }}
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>


    <script>
        const searchInput = document.getElementById('searchInput');
        const cards = document.querySelectorAll('.product-card');

        searchInput.addEventListener('input', () => {
            const q = (searchInput.value || '').toLowerCase().trim();
            cards.forEach((card) => {
                const name = card.getAttribute('data-name') || '';
                card.classList.toggle('hidden', q && !name.includes(q));
            });
        });

        function selectProduct(id, name) {
            // UI dummy: ganti sesuai kebutuhan (redirect / isi form / dsb)
            alert(`Produk dipilih: ${name} (${id})`);
        }
    </script>
@endsection
