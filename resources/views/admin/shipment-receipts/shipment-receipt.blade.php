@extends('admin.layout.master')

@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-penerimaan-pengiriman-produk', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    <section class="mb-5">
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Gudang</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Shipment Receipt</a>
        </div>
    </section>

    <section class="mb-5 rounded-lg border border-gray-300 bg-white p-5 shadow">
        <form method="GET" class="mb-4">
            <div class="grid grid-cols-1 items-end gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                <div class="flex w-full flex-col">
                    <label class="mb-1 text-xs font-semibold text-gray-700">
                        Nama Penerima
                    </label>
                    <input type="text" name="name" value="{{ request('name') }}" placeholder="Nama penerima"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#5aba6f]" />
                </div>

                <div class="flex w-full flex-col">
                    <label class="mb-1 text-xs font-semibold text-gray-700">
                        Code Shipment
                    </label>
                    <input type="text" name="code" value="{{ request('code') }}" placeholder="Code shipment"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#5aba6f]" />
                </div>

                <div class="flex w-full flex-col">
                    <label class="mb-1 text-xs font-semibold text-gray-700">
                        Status
                    </label>
                    <select name="status"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#5aba6f]">
                        <option value="">Semua Status</option>
                        @foreach ($statuses as $st)
                            <option value="{{ $st }}" @selected(request('status') == $st)>
                                {{ $st }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex w-full flex-col">
                    <label class="mb-1 text-xs font-semibold text-gray-700">
                        Warehouse
                    </label>
                    <select name="warehouse_id"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#5aba6f]">
                        <option value="">Semua Gudang</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" @selected(request('warehouse_id') == $warehouse->id)>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex w-full flex-col">
                    <label class="mb-1 text-xs font-semibold text-gray-700">
                        Tampilkan
                    </label>
                    <select name="per_page"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#5aba6f]"
                        onchange="this.form.submit()">
                        @foreach ([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" @selected((int) request('per_page', 10) === $n)>
                                {{ $n }} / halaman
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                    class="rounded-md bg-green-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-green-800">
                    Filter
                </button>

                <a href="{{ route('shipment-receipts') }}"
                    class="rounded-md bg-red-600 px-5 py-2 text-center text-sm font-semibold text-white transition hover:bg-red-800">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="mb-5 rounded-lg border border-gray-300 bg-white p-5 shadow">
        <div class="mb-5 flex items-center gap-5">
            <a href="{{ route('shipment-receipts.export') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-[#2E7E3F] px-5 py-2 text-sm font-semibold text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-300">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v6h6M20 20v-6h-6M20 8a8 0 00-14.9-3M4 16a8 0 0014.9 3" />
                </svg>
                Export .xlsx
            </a>

            @can('tambah penerimaan pengiriman produk')
                <a href="{{ route('create-shipment-receipt') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2D2ACD] px-6 py-2 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <span class="text-lg leading-none">+</span>
                    Tambah Baru
                </a>
            @endcan
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-900">
                    <thead class="bg-[#5aba6f]/70 text-gray-900">
                        <tr class="[&>th]:border-b [&>th]:border-gray-500">
                            <th scope="col" class="px-6 py-4 text-left font-extrabold">Code Shipment</th>
                            <th scope="col" class="px-6 py-4 text-left font-extrabold">Tanggal Receipt</th>
                            <th scope="col" class="px-6 py-4 text-left font-extrabold">Tanggal Diterima</th>
                            <th scope="col" class="px-6 py-4 text-left font-extrabold">Penerima</th>
                            <th scope="col" class="px-6 py-4 text-left font-extrabold">Gudang</th>
                            <th scope="col" class="px-6 py-4 text-left font-extrabold">Status</th>
                            <th scope="col" class="px-6 py-4 text-left font-extrabold">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-500 bg-gray-200">
                        @forelse ($shipmentReceipts as $shipmentReceipt)
                            <tr class="hover:bg-gray-100 [&>td]:border-b [&>td]:border-gray-400">
                                <td class="px-6 py-4 font-medium">
                                    {{ $shipmentReceipt->shipment->shipment_code ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $shipmentReceipt->created_at?->format('d M Y') ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $shipmentReceipt->received_at?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $shipmentReceipt->receivedBy->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $shipmentReceipt->shipment->warehouse->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClass = match ($shipmentReceipt->status) {
                                            'diterima' => 'bg-blue-100 text-blue-800',
                                            'disetujui' => 'bg-green-100 text-green-800',
                                            'ditolak' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span
                                        class="inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $shipmentReceipt->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @canany([
                                        'edit penerimaan pengiriman produk',
                                        'edit status penerimaan pengiriman
                                        produk',
                                        'baca penerimaan pengiriman produk',
                                        ])
                                        <a href="{{ route('edit-shipment-receipt', $shipmentReceipt->id) }}"
                                            class="text-blue-600 hover:underline">
                                            Sunting
                                        </a>
                                    @endcanany

                                    @can('hapus penerimaan pengiriman produk')
                                        |
                                        <form action="{{ route('delete-shipment-receipt', ['id' => $shipmentReceipt->id]) }}"
                                            method="POST" class="form-delete inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">
                                                Hapus
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Data Tidak Ada</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div
                class="flex flex-col gap-2 border-t border-gray-400 bg-gray-200 px-3 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-4 sm:py-4 md:px-5">
                <div class="text-xs font-semibold text-gray-800 sm:text-sm">
                    Showing {{ $shipmentReceipts->firstItem() ?? 0 }}–{{ $shipmentReceipts->lastItem() ?? 0 }} of
                    {{ $shipmentReceipts->total() }}
                </div>

                <div class="w-full overflow-x-auto sm:w-auto">
                    <div class="pagination">
                        {{ $shipmentReceipts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('addJs')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
            });
        @endif

        document.querySelectorAll('.form-delete').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Anda yakin?',
                    text: 'Data Shipment Receipt yang dihapus tidak bisa dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
