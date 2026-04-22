@extends('admin.layout.master')

@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pengadaan', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@php
    $disabledClass = 'bg-gray-100 cursor-not-allowed text-gray-500';

    $user = auth()->user();

    $canEditProcurement = $user->can('edit pengadaan bahan baku');
    $canEditStatus = $user->can('edit status pengadaan bahan baku') && $procurement->status === 'Menunggu';
    $canReadProcurement = $user->can('baca pengadaan bahan baku');

    $isMenunggu = $procurement->status === 'Menunggu';

    // edit data: cukup punya permission edit + status masih menunggu
    $canEditData = $canEditProcurement && $isMenunggu;

    // edit status: khusus permission edit status
    $canEditStatusOnly = $canEditStatus;

    // apakah ada yg bisa diupdate
    $canUpdatePage = $canEditData || $canEditStatusOnly;

    $statusOptions = ['Menunggu', 'Disetujui', 'Ditolak'];
@endphp

@section('addCss')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
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
                    <label class="mb-2 block text-xs font-bold">Nama Pemesan</label>
                    <input type="text" readonly value="{{ $procurement->user->name ?? auth()->user()->name }}"
                        class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $canEditData ? '' : $disabledClass }}">
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold">Gudang</label>
                    <input type="text" readonly value="{{ $procurement->warehouse->name }}"
                        class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $canEditData ? '' : $disabledClass }}">
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold">Tanggal Pemesanan</label>
                    <input type="date" readonly value="{{ $procurement->created_at->format('Y-m-d') }}"
                        class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $canEditData ? '' : $disabledClass }}">
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold">Status</label>
                    <select name="status" x-model="status"
                        class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $canEditStatusOnly ? '' : $disabledClass }}"
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
                <label class="mb-2 block text-xs font-bold">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>

                <textarea name="reason" rows="3" {{ $canEditStatusOnly ? '' : 'readonly' }}
                    :required="status === 'Ditolak' && {{ $canEditStatusOnly ? 'true' : 'false' }}"
                    class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $canEditStatusOnly ? '' : $disabledClass }}"
                    placeholder="Tuliskan alasan penolakan...">{{ old('reason', $procurement->reason) }}</textarea>

                @error('reason')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-5">
                <label class="mb-2 block text-xs font-bold">Catatan</label>
                <textarea name="note" rows="3" {{ $canEditData ? '' : 'readonly' }}
                    class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $canEditData ? '' : $disabledClass }}">{{ old('note', $procurement->note) }}</textarea>

                @error('note')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <section class="rounded-lg border border-gray-300 bg-white p-5 shadow">
            <h3 class="mb-4 text-sm font-bold">Daftar Item</h3>

            <div class="space-y-4">
                @foreach ($procurement->procurement_items as $index => $item)
                    <section class="rounded-lg border p-4">
                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                        <input type="hidden" name="items[{{ $index }}][raw_material_id]"
                            value="{{ $item->raw_material_id }}">

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            <div>
                                <label class="mb-2 block text-xs font-bold">Raw Material</label>
                                <input type="text" readonly
                                    value="{{ ($item->raw_material->code ?? 'RM-' . $item->raw_material->id) . ' - ' . $item->raw_material->name . ' / ' . $item->raw_material->unit }}"
                                    class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold">Stok</label>
                                <input type="number" readonly value="{{ $item->raw_material->stock->stock ?? 0 }}"
                                    class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold">Jumlah</label>
                                <input type="number" name="items[{{ $index }}][quantity_requested]" min="1"
                                    value="{{ old('items.' . $index . '.quantity_requested', $item->quantity_requested) }}"
                                    {{ $canEditData ? '' : 'readonly' }}
                                    class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $canEditData ? '' : $disabledClass }}">

                                @error('items.' . $index . '.quantity_requested')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold">Satuan</label>
                                <input type="text" readonly value="{{ $item->raw_material->unit }}"
                                    class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                            </div>
                        </div>
                    </section>
                @endforeach
            </div>
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

        @if ($canEditStatus)
            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                    Update
                </button>
            </div>
        @endif
    </form>
@endsection
