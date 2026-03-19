@extends('admin.layout.master')

@section('open-pemasaran', 'open')
@section('menu-pemasaran', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')
@section('menu-pemasaran-laporan-penjualan', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('content')
    @php
        $statusClass =
            $sale->status === 'Terhutang'
                ? 'bg-red-100 text-red-700 border-red-200'
                : 'bg-green-100 text-green-700 border-green-200';

        $statusDot = $sale->status === 'Terhutang' ? 'bg-red-500' : 'bg-green-500';

        $saleNumber = 'SALE-' . str_pad($sale->id, 5, '0', STR_PAD_LEFT);

        $totalItems = $sale->items->sum('quantity');
        $paymentCount = $sale->paymentHistories->count();

        $invoiceUrl = method_exists($sale, 'getFirstMediaUrl') ? $sale->getFirstMediaUrl('invoice_payment') : null;
    @endphp

    {{-- breadcrumb --}}
    <section class="mb-6">
        <div class="text-xl font-semibold text-gray-700">
            <span class="text-gray-700">Pemasaran</span>
            <span class="mx-1 text-gray-400">›</span>
            <a href="{{ route('admin.pemasaran-laporan-penjualan') }}" class="text-gray-700 hover:underline">
                Laporan Penjualan
            </a>
            <span class="mx-1 text-gray-400">›</span>
            <span class="text-blue-600 font-bold">History Pembayaran</span>
        </div>
    </section>

    {{-- hero summary --}}
    <section class="mb-6 rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-[#53BF6A] to-[#275931] px-6 py-5 text-white">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.18em] text-white/80">
                        Payment History
                    </div>
                    <h1 class="mt-2 text-2xl font-bold leading-tight">
                        {{ $saleNumber }}
                    </h1>
                    <p class="mt-2 text-sm text-white/90">
                        Riwayat pembayaran untuk transaksi penjualan
                        <span class="font-semibold">{{ $sale->customer_name }}</span>
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <span
                        class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-bold {{ $statusClass }}">
                        <span class="h-2.5 w-2.5 rounded-full {{ $statusDot }}"></span>
                        {{ $sale->status }}
                    </span>

                    <a href="{{ route('admin.pemasaran-laporan-penjualan.edit', $sale->id) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-white/15 px-4 py-2.5 text-sm font-bold text-white backdrop-blur hover:bg-white/25">
                        Sunting Penjualan
                    </a>

                    <a href="{{ route('admin.pemasaran-laporan-penjualan') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-[#275931] hover:bg-gray-100">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Pesanan</div>
                <div class="mt-2 text-2xl font-extrabold text-gray-900">
                    Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Dibayar</div>
                <div class="mt-2 text-2xl font-extrabold text-gray-900">
                    Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Sisa Tagihan</div>
                <div class="mt-2 text-2xl font-extrabold {{ $sale->debt_amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                    Rp {{ number_format($sale->debt_amount, 0, ',', '.') }}
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Pembayaran Tercatat</div>
                <div class="mt-2 text-2xl font-extrabold text-gray-900">
                    {{ $paymentCount }}
                </div>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
        {{-- left column --}}
        <div class="xl:col-span-8 space-y-6">

            {{-- detail transaksi --}}
            <section class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-bold text-gray-900">Detail Transaksi</h2>
                    <p class="mt-1 text-sm text-gray-500">Informasi utama penjualan dan customer</p>
                </div>

                <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-2">
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Nomor Penjualan</div>
                        <div class="mt-2 text-sm font-bold text-gray-900">{{ $saleNumber }}</div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal Laporan</div>
                        <div class="mt-2 text-sm font-bold text-gray-900">
                            {{ \Carbon\Carbon::parse($sale->report_date)->format('d/m/Y') }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal Penjualan</div>
                        <div class="mt-2 text-sm font-bold text-gray-900">
                            {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Penanggung Jawab</div>
                        <div class="mt-2 text-sm font-bold text-gray-900">
                            {{ $sale->personResponsible?->name ?? '-' }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Jenis Penjualan</div>
                        <div class="mt-2 text-sm font-bold text-gray-900">
                            {{ $sale->sale_type }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Provinsi</div>
                        <div class="mt-2 text-sm font-bold text-gray-900">
                            {{ $sale->customer_province }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Daerah</div>
                        <div class="mt-2 text-sm font-bold text-gray-900">
                            {{ $sale->customer_city }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Nama Pembeli</div>
                        <div class="mt-2 text-sm font-bold text-gray-900">
                            {{ $sale->customer_name }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Kontak Pembeli</div>
                        <div class="mt-2 text-sm font-bold text-gray-900">
                            {{ $sale->customer_contact }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 md:col-span-2">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Alamat Lengkap</div>
                        <div class="mt-2 text-sm font-bold text-gray-900">
                            {{ $sale->customer_address ?: '-' }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 md:col-span-2">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Catatan</div>
                        <div class="mt-2 text-sm font-bold text-gray-900 whitespace-pre-line">
                            {{ $sale->notes ?: '-' }}
                        </div>
                    </div>
                </div>
            </section>

            {{-- item dijual --}}
            <section class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div
                    class="flex flex-col gap-2 border-b border-gray-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Item yang Dijual</h2>
                        <p class="mt-1 text-sm text-gray-500">Rincian barang pada transaksi ini</p>
                    </div>

                    <div
                        class="inline-flex items-center rounded-full bg-gray-100 px-4 py-2 text-sm font-bold text-gray-700">
                        Total Qty: {{ $totalItems }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left text-gray-900">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr class="[&>th]:border-b [&>th]:border-gray-200">
                                <th class="px-6 py-4 font-extrabold">Kode Produk</th>
                                <th class="px-6 py-4 font-extrabold">SKU</th>
                                <th class="px-6 py-4 font-extrabold">Nama Produk</th>
                                <th class="px-6 py-4 font-extrabold">Qty</th>
                                <th class="px-6 py-4 font-extrabold">Harga</th>
                                <th class="px-6 py-4 font-extrabold">Diskon</th>
                                <th class="px-6 py-4 font-extrabold">Subtotal</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($sale->items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-semibold">
                                        {{ $item->productStock?->productVariant?->product?->code ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold">
                                        {{ $item->productStock?->productVariant?->sku ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold">
                                        {{ $item->productStock?->productVariant?->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold">
                                        Rp {{ number_format($item->discount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-900">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-6 text-center font-semibold text-gray-500">
                                        Belum ada item pada transaksi ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- history pembayaran --}}
            <section class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-bold text-gray-900">Riwayat Pembayaran</h2>
                    <p class="mt-1 text-sm text-gray-500">Setiap pembayaran yang pernah masuk untuk transaksi ini</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left text-gray-900">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr class="[&>th]:border-b [&>th]:border-gray-200">
                                <th class="px-6 py-4 font-extrabold">No.</th>
                                <th class="px-6 py-4 font-extrabold">Tanggal Pembayaran</th>
                                <th class="px-6 py-4 font-extrabold">Jumlah</th>
                                <th class="px-6 py-4 font-extrabold">Status Setelah Pembayaran</th>
                                <th class="px-6 py-4 font-extrabold">Bukti Pembayaran</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @php
                                $runningPaid = 0;
                            @endphp

                            @forelse ($sale->paymentHistories as $payment)
                                @php
                                    $runningPaid += $payment->amount;
                                    $remainingAfterPayment = max(0, $sale->total_amount - $runningPaid);
                                    $proofUrl = method_exists($payment, 'getFirstMediaUrl')
                                        ? $payment->getFirstMediaUrl('payment_proof')
                                        : null;
                                @endphp

                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-semibold">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="px-6 py-4 font-semibold">
                                        {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}
                                    </td>

                                    <td class="px-6 py-4 font-bold text-gray-900">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($remainingAfterPayment > 0)
                                            <span
                                                class="inline-flex items-center rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-bold text-red-700">
                                                Masih Terhutang · Rp
                                                {{ number_format($remainingAfterPayment, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center rounded-full border border-green-200 bg-green-50 px-3 py-1 text-xs font-bold text-green-700">
                                                Lunas
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($proofUrl)
                                            <a href="{{ $proofUrl }}" target="_blank"
                                                class="inline-flex items-center justify-center rounded-lg bg-[#2D2ACD] px-3 py-2 text-xs font-bold text-white hover:bg-blue-800">
                                                Lihat Bukti
                                            </a>
                                        @else
                                            <span class="text-xs font-semibold text-gray-400">
                                                Tidak ada bukti
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center font-semibold text-gray-500">
                                        Belum ada riwayat pembayaran
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- right column --}}
        <div class="xl:col-span-4 space-y-6">

            {{-- payment progress --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Progress Pembayaran</h2>
                <p class="mt-1 text-sm text-gray-500">Ringkasan progres dari total tagihan</p>

                @php
                    $progress =
                        $sale->total_amount > 0 ? min(100, round(($sale->paid_amount / $sale->total_amount) * 100)) : 0;
                @endphp

                <div class="mt-5">
                    <div class="mb-2 flex items-center justify-between text-sm font-semibold text-gray-700">
                        <span>Terlunasi</span>
                        <span>{{ $progress }}%</span>
                    </div>

                    <div class="h-3 w-full overflow-hidden rounded-full bg-gray-200">
                        <div class="h-full rounded-full bg-gradient-to-r from-[#53BF6A] to-[#275931]"
                            style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                <div class="mt-5 space-y-4">
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Tagihan</div>
                        <div class="mt-2 text-lg font-extrabold text-gray-900">
                            Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Sudah Dibayar</div>
                        <div class="mt-2 text-lg font-extrabold text-gray-900">
                            Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Sisa Pembayaran</div>
                        <div
                            class="mt-2 text-lg font-extrabold {{ $sale->debt_amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                            Rp {{ number_format($sale->debt_amount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </section>

            {{-- quick facts --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Ringkasan Cepat</h2>
                <p class="mt-1 text-sm text-gray-500">Highlight transaksi ini</p>

                <div class="mt-5 space-y-4">
                    <div class="flex items-start justify-between gap-4">
                        <span class="text-sm font-semibold text-gray-500">Jumlah Jenis Barang</span>
                        <span class="text-sm font-bold text-gray-900">{{ $sale->items->count() }}</span>
                    </div>

                    <div class="flex items-start justify-between gap-4">
                        <span class="text-sm font-semibold text-gray-500">Total Kuantitas</span>
                        <span class="text-sm font-bold text-gray-900">{{ $totalItems }}</span>
                    </div>

                    <div class="flex items-start justify-between gap-4">
                        <span class="text-sm font-semibold text-gray-500">Jumlah Pembayaran</span>
                        <span class="text-sm font-bold text-gray-900">{{ $paymentCount }}</span>
                    </div>

                    <div class="flex items-start justify-between gap-4">
                        <span class="text-sm font-semibold text-gray-500">Terakhir Diupdate</span>
                        <span class="text-sm font-bold text-gray-900">
                            {{ $sale->updated_at?->format('d/m/Y H:i') }}
                        </span>
                    </div>
                </div>
            </section>

            {{-- invoice --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Invoice Pembayaran</h2>
                <p class="mt-1 text-sm text-gray-500">Dokumen yang terlampir pada transaksi ini</p>

                <div class="mt-5">
                    <button type="button"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-[#2D2ACD] px-4 py-3 text-sm font-bold text-white hover:bg-blue-800">

                        <a href="{{ route('admin.pemasaran-laporan-penjualan.invoice', $sale->id) }}">Cetak Invoice</a>
                    </button>

                </div>
            </section>
        </div>
    </div>
@endsection
