@extends('admin.layout.master')

{{-- sidebar active --}}
@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-laporan-penjualan', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    {{-- breadcrumb --}}
    <section class="mb-5">
        <div class="mb-4 text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Pemasaran</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="#" class="text-blue-600 hover:underline">Laporan Penjualan</a>
        </div>
    </section>

    <section class="bg-white p-5 shadow border border-gray-300 rounded-lg mb-5">
        {{-- top bar --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between mb-3">
            <div class="w-full lg:max-w-[520px]">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text"
                        class="block w-full rounded-lg border border-gray-400 bg-gray-100 pl-10 pr-3 py-2.5 text-sm text-gray-900 focus:border-gray-500 focus:ring-0"
                        placeholder="Search for Name and Date">
                </div>
            </div>

            <div class="flex items-center gap-2 justify-end">
                <a href="#"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2E7E3F] px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-300">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v6h6M20 20v-6h-6M20 8a8 0 00-14.9-3M4 16a8 8 0 0014.9 3" />
                    </svg>
                    Export .xlsx
                </a>

                <a href="{{ route('admin.pemasaran-laporan-penjualan.create') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2D2ACD] px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <span class="text-lg leading-none">+</span>
                    Tambah Baru
                </a>
            </div>
        </div>

        {{-- table --}}
        <div class="overflow-hidden rounded-lg border border-gray-400 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-900">
                    <thead class="bg-[#5aba6f]/70 text-gray-900">
                        <tr class="[&>th]:border-b [&>th]:border-gray-500">
                            <th scope="col" class="px-6 py-3 font-extrabold text-left">Tanggal Laporan</th>
                            <th scope="col" class="px-6 py-3 font-extrabold text-left">Tanggal Penjualan</th>
                            <th scope="col" class="px-6 py-3 font-extrabold text-left">Penanggung Jawab</th>
                            <th scope="col" class="px-6 py-3 font-extrabold text-left">Provinsi</th>
                            <th scope="col" class="px-6 py-3 font-extrabold text-left">Aksi</th>
                            <th scope="col" class="px-6 py-3 font-extrabold text-left">Status</th>
                        </tr>
                    </thead>

                    <tbody class="bg-gray-200 divide-y divide-gray-500">
                        @forelse ($sales as $sale)
                            @php
                                $detail = [
                                    'sale_id' => $sale->id,
                                    'invoice_url' => route('admin.pemasaran-laporan-penjualan.invoice', $sale->id),
                                    'no' => 'SALE-' . str_pad($sale->id, 5, '0', STR_PAD_LEFT),
                                    'tgl_laporan' => $sale->report_date
                                        ? \Carbon\Carbon::parse($sale->report_date)->format('d/m/Y')
                                        : '-',
                                    'tgl_penjualan' => $sale->sale_date
                                        ? \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y')
                                        : '-',
                                    'pj' => $sale->personResponsible?->name ?? '-',
                                    'jenis_penjualan' => $sale->sale_type,
                                    'provinsi' => $sale->customer_province,
                                    'daerah' => $sale->customer_city,
                                    'nama_pembeli' => $sale->customer_name,
                                    'kontak_pembeli' => $sale->customer_contact,
                                    'items' => $sale->items
                                        ->map(function ($item) {
                                            return [
                                                'id' => $item->productStock?->productVariant?->product?->code ?? '-',
                                                'sku' => $item->productStock?->productVariant?->sku ?? '-',
                                                'nama' => $item->productStock?->productVariant?->name ?? '-',
                                                'jumlah' => $item->quantity,
                                                'harga' => 'Rp ' . number_format($item->price, 0, ',', '.'),
                                                'diskon' => 'Rp ' . number_format($item->discount, 0, ',', '.'),
                                                'total' => 'Rp ' . number_format($item->subtotal, 0, ',', '.'),
                                            ];
                                        })
                                        ->values()
                                        ->all(),
                                    'total_keseluruhan' => 'Rp ' . number_format($sale->total_amount, 0, ',', '.'),
                                    'down_payment' => 'Rp ' . number_format($sale->paid_amount, 0, ',', '.'),
                                    'jumlah_terhutang' => 'Rp ' . number_format($sale->debt_amount, 0, ',', '.'),
                                    'catatan' => $sale->notes ?? '',
                                ];

                                $statusClass =
                                    $sale->status === 'Terhutang'
                                        ? 'bg-red-100 text-red-800 border-red-300'
                                        : 'bg-green-100 text-green-800 border-green-300';
                            @endphp

                            <tr class="hover:bg-gray-300">
                                <td class="px-6 py-4 font-semibold">
                                    {{ $sale->created_at->format('d/m/Y') }}
                                </td>

                                <td class="px-6 py-4 font-semibold">
                                    {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}
                                </td>

                                <td class="px-6 py-4 font-semibold leading-tight">
                                    {{ $sale->personResponsible?->name ?? '-' }}
                                </td>

                                <td class="px-6 py-4 font-semibold">
                                    {{ $sale->customer_province }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4 font-semibold">

                                        <button type="button" class="text-[#2D2ACD] hover:underline"
                                            onclick='openDetailModal(@json($detail), @json($sale->status))'>
                                            Lihat
                                        </button>

                                        <a href="{{ route('admin.pemasaran-laporan-penjualan.edit', $sale->id) }}"
                                            class="text-yellow-600 hover:underline">
                                            Sunting
                                        </a>

                                        <a href="#" class="text-[#EC0000] hover:underline"
                                            onclick="event.preventDefault(); confirmDelete({{ $sale->id }})">
                                            Hapus
                                        </a>

                                        <form id="delete-form-{{ $sale->id }}"
                                            action="{{ route('admin.pemasaran-laporan-penjualan.destroy', $sale->id) }}"
                                            method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.pemasaran-laporan-penjualan.history-pembayaran', $sale->id) }}"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $statusClass }} hover:opacity-80 transition">
                                        {{ $sale->status }}
                                    </a>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-6 text-center text-gray-600 font-semibold">
                                    Belum ada data penjualan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- footer / pagination --}}
            <div class="px-4 py-3 bg-gray-200 border-t border-gray-400">
                {{ $sales->links() }}
            </div>

        </div>
    </section>

    {{-- ========================= --}}
    {{-- DETAIL MODAL (VIEW) --}}
    {{-- ========================= --}}
    <div id="detailModal"
        class="fixed inset-0 z-[9999] hidden bg-black/50 backdrop-blur-sm items-center justify-center p-2 sm:p-4">
        <div
            class="bg-[#f3f3f3] rounded-xl shadow-2xl w-full max-w-5xl mx-auto overflow-hidden animate-scale-in
                   max-h-[calc(100dvh-16px)] sm:max-h-[calc(100dvh-32px)] flex flex-col border border-gray-300">

            {{-- header --}}
            <div class="px-4 sm:px-6 py-3 border-b border-gray-300 bg-[#f3f3f3] flex items-center justify-between">
                <div class="text-sm font-bold text-gray-800">
                    Laporan Penjualan Nomor: <span id="dm_no" class="font-extrabold">-</span>
                </div>

                {{-- close pakai SVG X --}}
                <button type="button" onclick="closeDetailModal()"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-600 hover:bg-red-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6L6 18"></path>
                        <path d="M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- body --}}
            <div class="p-4 sm:p-6 overflow-y-auto">
                {{-- status badge --}}
                <div class="mb-4">
                    <span id="dm_status_badge"
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border bg-gray-100 text-gray-700 border-gray-300">
                        -
                    </span>
                </div>

                {{-- header fields --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Laporan</label>
                        <input id="dm_tgl_laporan" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Penjualan</label>
                        <input id="dm_tgl_penjualan" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Penanggung Jawab</label>
                        <input id="dm_pj" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Jenis Penjualan</label>
                        <input id="dm_jenis" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Provinsi</label>
                        <input id="dm_provinsi" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Daerah</label>
                        <input id="dm_daerah" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Nama Pembeli</label>
                        <input id="dm_pembeli" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Kontak Pembeli</label>
                        <input id="dm_kontak" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>
                </div>

                {{-- items (kartu hijau) --}}
                <div class="mt-4 space-y-4" id="dm_items"></div>

                {{-- summary --}}
                <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Total Pesanan Keseluruhan</label>
                        <input id="dm_total_keseluruhan" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    {{-- ✅ TERHUTANG: DP --}}
                    <div id="dm_dp_wrap" class="hidden">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Down Payment</label>
                        <input id="dm_down_payment" value="" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    {{-- ✅ TERHUTANG: Status Terhutang --}}
                    <div id="dm_terhutang_wrap" class="hidden">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Jumlah Terhutang</label>
                        <input id="dm_jumlah_terhutang" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>

                    {{-- ✅ LUNAS: Status Lunas --}}
                    <div id="dm_lunas_wrap" class="hidden">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Status Lunas</label>
                        <input id="dm_status_lunas" readonly
                            class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-xs font-bold text-gray-700 mb-2">Catatan</label>
                    <textarea id="dm_catatan" rows="3" readonly
                        class="w-full rounded-md border border-gray-400 bg-white px-3 py-2 text-sm font-semibold"></textarea>
                </div>

                {{-- actions --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-5">
                    <a id="dm_invoice_link" href="#"
                        class="inline-flex items-center justify-center rounded-lg bg-[#2E7E3F] px-6 sm:px-8 py-2.5 text-sm font-bold text-white hover:bg-green-800">
                        Cetak Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- INVOICE MODAL --}}
    {{-- ========================= --}}
    <div id="invoiceModal"
        class="fixed inset-0 z-[10000] hidden bg-black/60 backdrop-blur-sm items-center justify-center p-2 sm:p-4">
        <div
            class="bg-white rounded-xl shadow-xl w-full max-w-5xl mx-auto overflow-hidden
                   max-h-[calc(100dvh-16px)] sm:max-h-[calc(100dvh-32px)] flex flex-col">

            <div
                class="px-4 sm:px-6 py-3 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
                <div class="text-sm sm:text-base font-bold text-gray-800">Invoice</div>

                <button type="button" onclick="closeInvoiceModal()"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full text-gray-700 hover:bg-gray-100 active:bg-gray-200">
                    <span class="text-2xl leading-none">×</span>
                </button>
            </div>

            <div class="p-3 sm:p-4 bg-gray-100 overflow-y-auto">
                <img id="im_invoice" src="" alt="Invoice"
                    class="w-full max-h-[70dvh] object-contain rounded-lg border border-gray-300 bg-white" />
            </div>
        </div>
    </div>

    <style>
        @keyframes scaleIn {
            from {
                transform: scale(.985);
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
        const invoiceBaseUrl = @json(url('/admin/pemasaran/laporan-penjualan'));
    </script>

    <script>
        // =========================
        // MODAL ELEMENTS
        // =========================
        const detailModal = document.getElementById('detailModal');
        const invoiceModal = document.getElementById('invoiceModal');

        const dmNo = document.getElementById('dm_no');
        const dmTglLaporan = document.getElementById('dm_tgl_laporan');
        const dmTglPenjualan = document.getElementById('dm_tgl_penjualan');
        const dmPj = document.getElementById('dm_pj');
        const dmJenis = document.getElementById('dm_jenis');
        const dmProvinsi = document.getElementById('dm_provinsi');
        const dmDaerah = document.getElementById('dm_daerah');
        const dmPembeli = document.getElementById('dm_pembeli');
        const dmKontak = document.getElementById('dm_kontak');

        const dmItems = document.getElementById('dm_items');
        const dmTotalKeseluruhan = document.getElementById('dm_total_keseluruhan');

        const dmDpWrap = document.getElementById('dm_dp_wrap');
        const dmDownPayment = document.getElementById('dm_down_payment');

        const dmTerhutangWrap = document.getElementById('dm_terhutang_wrap');
        const dmJumlahTerhutang = document.getElementById('dm_jumlah_terhutang');

        const dmLunasWrap = document.getElementById('dm_lunas_wrap');
        const dmStatusLunas = document.getElementById('dm_status_lunas');

        const dmCatatan = document.getElementById('dm_catatan');
        const dmStatusBadge = document.getElementById('dm_status_badge');

        const imInvoice = document.getElementById('im_invoice');
        const dmInvoiceLink = document.getElementById('dm_invoice_link');

        let currentDetail = null;

        function statusBadgeClass(status) {
            if (status === 'Terhutang') return 'bg-red-100 text-red-800 border-red-300';
            return 'bg-green-100 text-green-800 border-green-300';
        }

        function renderItems(items) {
            dmItems.innerHTML = '';
            (items || []).forEach((it) => {
                const card = document.createElement('section');
                card.className = 'bg-[#a7dfb2] p-5 shadow border border-[#68b97a] rounded-xl';

                card.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-2.5">ID Produk</label>
                            <input value="${it.id ?? ''}" readonly
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-2.5">Stock Keeping Unit</label>
                            <input value="${it.sku ?? ''}" readonly
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-2.5">Nama Produk</label>
                            <input value="${it.nama ?? ''}" readonly
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-2.5">Jumlah Terjual</label>
                            <input value="${it.jumlah ?? ''}" readonly
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-2.5">Harga</label>
                            <input value="${it.harga ?? ''}" readonly
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-2.5">Diskon</label>
                            <input value="${it.diskon ?? ''}" readonly
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-2.5">Total</label>
                            <input value="${it.total ?? ''}" readonly
                                class="w-full rounded-md border border-gray-400 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900">
                        </div>
                    </div>
                `;
                dmItems.appendChild(card);
            });
        }

        // =========================
        // OPEN / CLOSE (GLOBAL)
        // =========================
        window.openDetailModal = function(detail, status) {
            if (!detailModal) return;

            currentDetail = detail;

            if (dmInvoiceLink) {
                dmInvoiceLink.href = detail.invoice_url ?? '#';
            }

            dmNo.textContent = detail.no ?? '-';
            dmTglLaporan.value = detail.tgl_laporan ?? '';
            dmTglPenjualan.value = detail.tgl_penjualan ?? '';
            dmPj.value = detail.pj ?? '';
            dmJenis.value = detail.jenis_penjualan ?? '';
            dmProvinsi.value = detail.provinsi ?? '';
            dmDaerah.value = detail.daerah ?? '';
            dmPembeli.value = detail.nama_pembeli ?? '';
            dmKontak.value = detail.kontak_pembeli ?? '';

            renderItems(detail.items ?? []);

            dmTotalKeseluruhan.value = detail.total_keseluruhan ?? '';
            dmCatatan.value = detail.catatan ?? '';

            const st = status ?? 'Lunas';
            dmStatusBadge.textContent = st;
            dmStatusBadge.className =
                `inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border ${statusBadgeClass(st)}`;

            dmDpWrap.classList.add('hidden');
            dmTerhutangWrap.classList.add('hidden');
            dmLunasWrap.classList.add('hidden');

            if (dmDownPayment) dmDownPayment.value = '';
            if (dmJumlahTerhutang) dmJumlahTerhutang.value = '';
            if (dmStatusLunas) dmStatusLunas.value = '';

            if (st === 'Terhutang') {
                dmDpWrap.classList.remove('hidden');
                dmTerhutangWrap.classList.remove('hidden');
                dmDownPayment.value = detail.down_payment ?? '';
                dmJumlahTerhutang.value = detail.jumlah_terhutang ?? '';
            } else {
                dmLunasWrap.classList.remove('hidden');
                dmStatusLunas.value = 'Lunas';
            }

            detailModal.classList.remove('hidden');
            detailModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
        window.closeDetailModal = function() {
            if (!invoiceModal.classList.contains('hidden')) window.closeInvoiceModal();
            detailModal.classList.add('hidden');
            detailModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        window.openInvoiceModal = function() {
            if (!currentDetail) return;
            invoiceModal.classList.remove('hidden');
            invoiceModal.classList.add('flex');
        }

        window.closeInvoiceModal = function() {
            invoiceModal.classList.add('hidden');
            invoiceModal.classList.remove('flex');
        }

        // =========================
        // CETAK INVOICE
        // =========================
        window.printInvoice = function() {
            if (!currentDetail || !currentDetail.invoice_img) return;

            const w = window.open('', '_blank');
            if (!w) return alert('Popup diblokir browser. Izinkan popup untuk cetak.');

            w.document.write(`
                <html>
                <head>
                    <title>Cetak Invoice</title>
                    <style>
                        body { margin: 0; padding: 16px; background: #f3f3f3; }
                        img { width: 100%; max-width: 900px; display: block; margin: 0 auto; background: #fff; border: 1px solid #ccc; border-radius: 8px; }
                    </style>
                </head>
                <body>
                    <img src="${currentDetail.invoice_img}" onload="window.print(); window.close();" />
                </body>
                </html>
            `);
            w.document.close();
        }

        // =========================
        // BACKDROP + ESC
        // =========================
        detailModal?.addEventListener('click', (e) => {
            if (e.target === detailModal) window.closeDetailModal();
        });

        invoiceModal?.addEventListener('click', (e) => {
            if (e.target === invoiceModal) window.closeInvoiceModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (!invoiceModal.classList.contains('hidden')) window.closeInvoiceModal();
                else if (!detailModal.classList.contains('hidden')) window.closeDetailModal();
            }
        });

        function confirmDelete(id) {
            if (confirm('Yakin ingin menghapus laporan ini?\nStok barang akan dikembalikan.')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
@endsection
