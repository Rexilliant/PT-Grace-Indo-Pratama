@extends('admin.layout.master')

@section('open-gudang', 'open')
@section('menu-gudang', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pengadaan', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@php
    $disabledClass = 'bg-gray-100 cursor-not-allowed';
@endphp

@section('addCss')
    {{-- AlpineJS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- anti flicker --}}
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
    </section>

    <form x-data="{ status: @js($procurement->status) }" action="{{ route('update-procurement', $procurement->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        {{-- HEADER --}}
        <section class="bg-white p-5 shadow border border-gray-300 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                <div>
                    <label class="block text-xs font-bold mb-2">Nama Pemesan</label>
                    <input type="text" readonly value="{{ auth()->user()->name }}"
                        class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                </div>

                <div>
                    <label class="block text-xs font-bold mb-2">Provinsi</label>
                    <input type="text" readonly value="{{ $procurement->province }}"
                        class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                </div>

                <div>
                    <label class="block text-xs font-bold mb-2">Tanggal Pemesanan</label>
                    <input type="date" readonly value="{{ $procurement->created_at->format('Y-m-d') }}"
                        class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                </div>

                <div>
                    <label class="block text-xs font-bold mb-2">Status</label>
                    <select name="status" x-model="status"
                        class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold @if ($procurement->status !== 'Menunggu') {{ $disabledClass }} @endif"
                        @if ($procurement->status !== 'Menunggu') disabled @endif>
                        @foreach (['Menunggu', 'Diterima', 'Ditolak'] as $st)
                            <option value="{{ $st }}">{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            {{-- Reason: tampil kalau status dipilih Ditolak --}}
            <div class="mt-5" x-show="status === 'Ditolak'" x-transition x-cloak>
                <label class="block text-xs font-bold mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea name="reason" rows="3" :required="status === 'Ditolak'"
                    class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold" placeholder="Tuliskan alasan penolakan...">{{ $procurement->reason }}</textarea>
            </div>

            <div class="mt-5">
                <label class="block text-xs font-bold mb-2">Catatan</label>
                <textarea rows="3" readonly
                    class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">{{ $procurement->note }}</textarea>
            </div>
        </section>

        {{-- ITEMS --}}
        <section class="bg-white p-5 shadow border border-gray-300 rounded-lg">
            <h3 class="text-sm font-bold mb-4">Daftar Item</h3>

            <div class="space-y-4">
                @foreach ($procurement->procurement_items as $item)
                    <section class="border rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                            <div>
                                <label class="block text-xs font-bold mb-2">Raw Material</label>
                                <input type="text" readonly
                                    value="{{ $item->raw_material->code ?? 'RM-' . $item->raw_material->id }} - {{ $item->raw_material->name }} / {{ $item->raw_material->unit }}"
                                    class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                            </div>

                            <div>
                                <label class="block text-xs font-bold mb-2">Stok</label>
                                <input type="number" readonly value="{{ $item->raw_material->stock ?? 0 }}"
                                    class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                            </div>

                            <div>
                                <label class="block text-xs font-bold mb-2">Jumlah</label>
                                <input type="number" readonly value="{{ $item->quantity_requested }}"
                                    class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                            </div>

                            <div>
                                <label class="block text-xs font-bold mb-2">Satuan</label>
                                <input type="text" readonly value="{{ $item->raw_material->unit }}"
                                    class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                            </div>

                        </div>
                    </section>
                @endforeach
            </div>
        </section>

        {{-- HISTORI: tampil sesuai status yang dipilih --}}
        @if ($procurement->status !== 'Menunggu')
            <section class="bg-white p-5 shadow border border-gray-300 rounded-lg"
                x-show="status === 'Ditolak' || status === 'Diterima'" x-transition x-cloak>
                <h3 class="text-sm font-bold mb-4">Histori</h3>

                {{-- Ditolak --}}
                <div x-show="status === 'Ditolak'" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold mb-2">rejected_by</label>
                            <input type="text" readonly value="{{ $procurement->userRejected->name ?? '-' }}"
                                class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-2">rejected_at</label>
                            <input type="text" readonly value="{{ $procurement->rejected_at ?? '-' }}"
                                class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                        </div>
                    </div>
                </div>

                {{-- Diterima --}}
                <div x-show="status === 'Diterima'" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold mb-2">approved_by</label>
                            <input type="text" readonly value="{{ $procurement->userApproved->name ?? '-' }}"
                                class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-2">approved_at</label>
                            <input type="text" readonly value="{{ $procurement->approved_at ?? '-' }}"
                                class="w-full rounded-md border px-3 py-2.5 text-sm font-semibold {{ $disabledClass }}">
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <div class="flex justify-end pt-2">
            @if ($procurement->status === 'Menunggu')
                <button type="submit"
                    class="rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
                    Update
                </button>
            @endif
        </div>

    </form>
    <form action="" method="post">
        <button type="submit" class="rounded-lg bg-[#2D2ACD] px-10 py-3 text-sm font-bold text-white hover:bg-blue-800">
            Done
        </button>
    </form>
@endsection
