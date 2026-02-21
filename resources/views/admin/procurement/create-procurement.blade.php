@extends('admin.layout.master')

@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pengadaan', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('addCss')
    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .select2-container .select2-selection--single {
            height: 42px;
            border-radius: 0.375rem;
            border: 1px solid #9CA3AF;
            display: flex;
            align-items: center;
            padding-left: 10px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
        }

        .select2-dropdown {
            border-radius: 0.375rem;
        }
    </style>

@endsection
@section('content')

    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-gray-700">Pengadaan Barang</span>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600">Tambah Pemesanan</span>
        </div>
    </section>

    <form action="{{ route('store-procurement') }}" method="POST" class="space-y-4">
        @csrf
        @method('POST')
        {{-- HEADER --}}
        <section class="bg-white p-5 shadow border border-gray-300 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Nama Pemesan</label>
                    <input type="text" value="{{ auth()->user()->name ?? '-' }}" readonly name="request_by"
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                </div>

                {{-- PROVINSI (Search Select - Alpine) --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Provinsi</label>

                    <select name="province" id="provinceSelect"
                        class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900">
                        <option value="">-- Pilih Provinsi --</option>
                        @foreach ($provinces as $prov)
                            <option value="{{ $prov['name'] }}" {{ old('province') == $prov['name'] ? 'selected' : '' }}>
                                {{ $prov['name'] }}
                            </option>
                        @endforeach
                    </select>

                    @error('province')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>


                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Pemesanan</label>
                    <input type="date" value="{{ now()->format('Y-m-d') }}" readonly name="create_at"
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed" />
                </div>

            </div>

            <div class="mt-5">
                <label class="block text-xs font-bold text-gray-700 mb-2">Catatan (opsional)</label>
                <textarea name="note" rows="3"
                    class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500"
                    placeholder="Tambahkan catatan...">{{ old('note') }}</textarea>
                @error('note')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </section>

        {{-- ITEMS --}}
        <section class="bg-white p-5 shadow border border-gray-300 rounded-lg">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-700">Daftar Item</h3>

                <button type="button" id="btnAddItem"
                    class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-4 py-2 text-sm font-bold text-white hover:bg-blue-800">
                    + Add Item
                </button>
            </div>

            {{-- ITEMS CONTAINER --}}
            <div id="itemsContainer" class="space-y-4">

                {{-- ITEM #0 (default) --}}
                <section class="item-row border border-gray-200 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Raw Material</label>

                            <select name="items[0][raw_material_id]"
                                class="rawMaterialSelect w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900">
                                <option value="">-- Pilih Bahan --</option>
                                @foreach ($rawMaterials as $rm)
                                    <option value="{{ $rm->id }}">
                                        {{ $rm->code ?? 'RM-' . $rm->id }} - {{ $rm->name }} / {{ $rm->unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">
                                Jumlah Pesanan <span class="text-red-500">*</span>
                            </label>
                            <input type="number" min="1" placeholder="Masukkan jumlah"
                                name="items[0][quantity_requested]"
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                        </div>

                        <div class="flex gap-3 items-end">
                            <button type="button"
                                class="btnRemoveItem inline-flex w-full items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">
                                Hapus
                            </button>
                        </div>

                    </div>
                </section>

            </div>

            {{-- TEMPLATE (hidden) --}}
            <template id="itemTemplate">
                <section class="item-row border border-gray-200 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Raw Material</label>

                            <select name="items[__INDEX__][raw_material_id]"
                                class="rawMaterialSelect w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900">
                                <option value="">-- Pilih Bahan --</option>
                                @foreach ($rawMaterials as $rm)
                                    <option value="{{ $rm->id }}">
                                        {{ $rm->code ?? 'RM-' . $rm->id }} - {{ $rm->name }} / {{ $rm->unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">
                                Jumlah Pesanan <span class="text-red-500">*</span>
                            </label>
                            <input type="number" min="1" placeholder="Masukkan jumlah"
                                name="items[__INDEX__][quantity_requested]"
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 focus:ring-0 focus:border-gray-500" />
                        </div>

                        <div class="flex gap-3 items-end">
                            <button type="button"
                                class="btnRemoveItem inline-flex w-full items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">
                                Hapus
                            </button>
                        </div>

                    </div>
                </section>
            </template>
        </section>


        {{-- ACTIONS --}}
        <div class="flex items-center justify-end gap-4 pt-2">
            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                Simpan
            </button>
        </div>

    </form>

@endsection
@section('addJs')
    <script src="{{ asset('assets/js/sweetalert.js') }}"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                confirmButtonColor: '#2563eb'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                confirmButtonColor: '#dc2626'
            });
        </script>
    @endif
    <script>
        function initSelect2For($el) {
            $el.select2({
                placeholder: "Cari bahan...",
                allowClear: true,
                width: '100%'
            });
        }

        function reindexItems() {
            $('#itemsContainer .item-row').each(function(index) {
                // update name untuk raw material select
                $(this).find('select.rawMaterialSelect')
                    .attr('name', `items[${index}][raw_material_id]`);

                // update name untuk qty
                $(this).find('input[type="number"]')
                    .attr('name', `items[${index}][quantity_requested]`);
            });
        }

        $(document).ready(function() {

            // province select2 tetap
            $('#provinceSelect').select2({
                placeholder: "Cari provinsi...",
                allowClear: true,
                width: '100%'
            });

            // init select2 untuk item pertama (yang sudah ada)
            initSelect2For($('.rawMaterialSelect').first());

            // ADD ITEM
            $('#btnAddItem').on('click', function() {
                const container = $('#itemsContainer');
                const templateHtml = $('#itemTemplate').html();

                const nextIndex = container.find('.item-row').length;
                const newHtml = templateHtml.replaceAll('__INDEX__', nextIndex);

                const $newItem = $(newHtml);
                container.append($newItem);

                // init select2 hanya untuk select yang baru
                initSelect2For($newItem.find('select.rawMaterialSelect'));
            });

            // REMOVE ITEM (event delegation)
            $(document).on('click', '.btnRemoveItem', function() {
                const $row = $(this).closest('.item-row');

                // destroy select2 supaya ga leak DOM
                const $select = $row.find('select.rawMaterialSelect');
                if ($select.data('select2')) {
                    $select.select2('destroy');
                }

                $row.remove();

                // minimal 1 item harus ada
                if ($('#itemsContainer .item-row').length === 0) {
                    $('#btnAddItem').trigger('click');
                }

                // reindex agar name items[x] rapih
                reindexItems();
            });

        });
    </script>
@endsection
