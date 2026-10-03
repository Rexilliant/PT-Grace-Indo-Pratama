@extends('admin.layout.master')

@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pengadaan', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@php
    $disabledClass = 'bg-gray-100 cursor-not-allowed text-gray-500';

    $user = auth()->user();

    $isMenunggu = $procurement->status === 'Menunggu';

    $canEditProcurement = $user->can('edit pengadaan bahan baku');
    $canEditStatus = $user->can('edit status pengadaan bahan baku');
    $canReadProcurement = $user->can('baca pengadaan bahan baku');

    // edit data: cukup punya permission edit + status masih menunggu
    $canEditData = $canEditProcurement && $isMenunggu;

    // edit status: khusus permission edit status + status masih menunggu
    $canEditStatusOnly = $canEditStatus && $isMenunggu;

    // apakah ada yg bisa diupdate
    $canUpdatePage = $canEditData || $canEditStatusOnly;

    $statusOptions = ['Menunggu', 'Disetujui', 'Ditolak'];

    $oldItems = old('items');
    if (!$oldItems) {
        $oldItems = $procurement->procurement_items->map(function ($item) {
            return [
                'raw_material_id' => $item->raw_material_id,
                'quantity_requested' => $item->quantity_requested,
            ];
        })->toArray();
    }
    if (empty($oldItems)) {
        $oldItems = [['raw_material_id' => null, 'quantity_requested' => null]];
    }
@endphp

@section('addCss')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

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
    <section class="mb-5">
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span>Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <span>Pengadaan Barang</span>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600">Edit Pemesanan</span>
        </div>

        @if (!$canUpdatePage)
            <div class="rounded-lg border border-yellow-300 bg-yellow-50 px-4 py-3 text-sm font-medium text-yellow-800">
                @if (!$canEditProcurement && !$canEditStatus && $canReadProcurement)
                    Anda hanya memiliki akses baca. Data pengadaan bahan baku tidak dapat diubah.
                @elseif (!$canEditProcurement && !$canEditStatus)
                    Anda tidak memiliki izin untuk mengubah data ini.
                @elseif (!$isMenunggu)
                    Data tidak dapat diedit karena status sudah <strong>{{ $procurement->status }}</strong>.
                @else
                    Anda tidak memiliki izin untuk mengubah data ini.
                @endif
            </div>
        @endif
    </section>

    <form x-data="{ status: @js(old('status', $procurement->status)) }" action="{{ route('update-procurement', $procurement->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <section class="rounded-lg border border-gray-300 bg-white p-5 shadow">
            <div class="mb-5">
                <h2 class="text-lg font-bold">ID Pengadaan Barang: {{ $procurement->id }}</h2>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">Nama Pemesan</label>
                    <input type="text" readonly value="{{ $procurement->userRequest->name ?? auth()->user()->name }}"
                        class="w-full rounded-md border border-gray-400 bg-gray-100 px-3 py-2.5 text-sm font-semibold text-gray-900 cursor-not-allowed">
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">Gudang</label>
                    @if ($canEditData)
                        <select name="warehouse_id" id="warehouse_id"
                            class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900">
                            <option value="">-- Pilih Gudang --</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}"
                                    {{ old('warehouse_id', $procurement->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" readonly value="{{ $procurement->warehouse->name }}"
                            class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                        <input type="hidden" name="warehouse_id" value="{{ $procurement->warehouse_id }}">
                    @endif

                    @error('warehouse_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">Tanggal Pemesanan</label>
                    <input type="date" name="purchase_at"
                        value="{{ old('purchase_at', optional($procurement->purchase_at)->format('Y-m-d')) }}"
                        {{ $canEditData ? '' : 'readonly' }}
                        class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900 {{ $canEditData ? '' : $disabledClass }}">
                    @error('purchase_at')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold text-gray-700">Status</label>
                    <select name="status" x-model="status"
                        class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900 {{ $canEditStatusOnly ? '' : $disabledClass }}"
                        {{ $canEditStatusOnly ? '' : 'disabled' }}>
                        @foreach ($statusOptions as $st)
                            <option value="{{ $st }}"
                                {{ old('status', $procurement->status) == $st ? 'selected' : '' }}>
                                {{ $st }}
                            </option>
                        @endforeach
                    </select>

                    @unless ($canEditStatusOnly)
                        <input type="hidden" name="status" value="{{ old('status', $procurement->status) }}">
                    @endunless

                    @error('status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-5" x-show="status === 'Ditolak'" x-transition x-cloak>
                <label class="mb-2 block text-xs font-bold text-gray-700">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>

                <textarea name="reason" rows="3" {{ $canEditStatusOnly ? '' : 'readonly' }}
                    :required="status === 'Ditolak' && {{ $canEditStatusOnly ? 'true' : 'false' }}"
                    class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900 {{ $canEditStatusOnly ? '' : $disabledClass }}"
                    placeholder="Tuliskan alasan penolakan...">{{ old('reason', $procurement->reason) }}</textarea>

                @error('reason')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-5">
                <label class="mb-2 block text-xs font-bold text-gray-700">Catatan (opsional)</label>
                <textarea name="note" rows="3" {{ $canEditData ? '' : 'readonly' }}
                    class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900 {{ $canEditData ? '' : $disabledClass }}"
                    placeholder="Tambahkan catatan...">{{ old('note', $procurement->note) }}</textarea>

                @error('note')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <section class="rounded-lg border border-gray-300 bg-white p-5 shadow">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-700">Daftar Item</h3>

                @if ($canEditData)
                    <button type="button" id="btnAddItem"
                        class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-4 py-2 text-sm font-bold text-white hover:bg-blue-800">
                        + Add Item
                    </button>
                @endif
            </div>

            {{-- ITEMS CONTAINER --}}
            <div id="itemsContainer" class="space-y-4">
                @foreach ($oldItems as $i => $item)
                    <section class="item-row rounded-lg border border-gray-200 p-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-700">Bahan Baku</label>
                                @if ($canEditData)
                                    <select name="items[{{ $i }}][raw_material_id]"
                                        class="rawMaterialSelect w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900">
                                        <option value="">-- Pilih Bahan --</option>
                                        @foreach ($rawMaterials as $rm)
                                            <option value="{{ $rm->id }}"
                                                {{ isset($item['raw_material_id']) && $item['raw_material_id'] == $rm->id ? 'selected' : '' }}>
                                                {{ $rm->code ?? 'RM-' . $rm->id }} - {{ $rm->name }} / {{ $rm->unit }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    @php
                                        $selectedRm = $rawMaterials->firstWhere('id', $item['raw_material_id'] ?? null);
                                    @endphp
                                    <input type="text" readonly
                                        value="{{ $selectedRm ? ($selectedRm->code ?? 'RM-' . $selectedRm->id) . ' - ' . $selectedRm->name . ' / ' . $selectedRm->unit : '-' }}"
                                        class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                                    <input type="hidden" name="items[{{ $i }}][raw_material_id]" value="{{ $item['raw_material_id'] ?? '' }}">
                                @endif
                                @error("items.$i.raw_material_id")
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-700">
                                    Jumlah Pesanan <span class="text-red-500">*</span>
                                </label>
                                <input type="number" min="1" placeholder="Masukkan jumlah"
                                    name="items[{{ $i }}][quantity_requested]"
                                    value="{{ $item['quantity_requested'] ?? '' }}"
                                    {{ $canEditData ? '' : 'readonly' }}
                                    class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900 {{ $canEditData ? '' : $disabledClass }}" />
                                @error("items.$i.quantity_requested")
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-end gap-3">
                                @if ($canEditData)
                                    <button type="button"
                                        class="btnRemoveItem inline-flex w-full items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </div>
                    </section>
                @endforeach
            </div>

            @if ($canEditData)
                {{-- TEMPLATE (hidden) --}}
                <template id="itemTemplate">
                    <section class="item-row rounded-lg border border-gray-200 p-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-700">Bahan Baku</label>
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
                                <label class="mb-2 block text-xs font-bold text-gray-700">
                                    Jumlah Pesanan <span class="text-red-500">*</span>
                                </label>
                                <input type="number" min="1" placeholder="Masukkan jumlah"
                                    name="items[__INDEX__][quantity_requested]"
                                    class="w-full rounded-md border border-gray-400 px-3 py-2.5 text-sm font-semibold text-gray-900 focus:border-gray-500 focus:ring-0" />
                            </div>

                            <div class="flex items-end gap-3">
                                <button type="button"
                                    class="btnRemoveItem inline-flex w-full items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </section>
                </template>
            @endif
        </section>

        @if (in_array($procurement->status, ['Ditolak', 'Disetujui']) ||
                in_array(old('status', $procurement->status), ['Ditolak', 'Disetujui']))
            <section class="rounded-lg border border-gray-300 bg-white p-5 shadow"
                x-show="status === 'Ditolak' || status === 'Disetujui'" x-transition x-cloak>
                <h3 class="mb-4 text-sm font-bold">Histori</h3>

                <div x-show="status === 'Ditolak'" x-transition>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-xs font-bold">Rejected By</label>
                            <input type="text" readonly value="{{ $procurement->userRejected->name ?? '-' }}"
                                class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-bold">Rejected At</label>
                            <input type="text" readonly value="{{ $procurement->rejected_at ?? '-' }}"
                                class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                        </div>
                    </div>
                </div>

                <div x-show="status === 'Disetujui'" x-transition>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-xs font-bold">Approved By</label>
                            <input type="text" readonly value="{{ $procurement->userApproved->name ?? '-' }}"
                                class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-bold">Approved At</label>
                            <input type="text" readonly value="{{ $procurement->approved_at ?? '-' }}"
                                class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                        </div>
                    </div>
                </div>
            </section>
        @endif

        @if ($canUpdatePage)
            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                    Update
                </button>
            </div>
        @endif
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

    @if ($canEditData)
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
                    $(this).find('select.rawMaterialSelect')
                        .attr('name', `items[${index}][raw_material_id]`);

                    $(this).find('input[type="number"]')
                        .attr('name', `items[${index}][quantity_requested]`);
                });
            }

            $(document).ready(function() {
                $('#warehouse_id').select2({
                    placeholder: "Cari Gudang",
                    allowClear: true,
                    width: '100%'
                });

                $('.rawMaterialSelect').each(function() {
                    initSelect2For($(this));
                });

                $('#btnAddItem').on('click', function() {
                    const container = $('#itemsContainer');
                    const templateHtml = $('#itemTemplate').html();

                    const nextIndex = container.find('.item-row').length;
                    const newHtml = templateHtml.replaceAll('__INDEX__', nextIndex);

                    const $newItem = $(newHtml);
                    container.append($newItem);

                    initSelect2For($newItem.find('select.rawMaterialSelect'));
                });

                $(document).on('click', '.btnRemoveItem', function() {
                    const $row = $(this).closest('.item-row');

                    const $select = $row.find('select.rawMaterialSelect');
                    if ($select.data('select2')) {
                        $select.select2('destroy');
                    }

                    $row.remove();

                    if ($('#itemsContainer .item-row').length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Minimal satu item harus ada'
                        });

                        return;
                    }

                    reindexItems();
                });
            });
        </script>
    @endif
@endsection
