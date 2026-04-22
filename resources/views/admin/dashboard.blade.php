@extends('admin.layout.master')

@section('open-dashboard', 'open')
@section('menu-dashboard', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('addCss')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --green-primary: #275931;
            --green-mid: #3a8a50;
            --green-light: #53BF6A;
            --green-pale: #e8f5eb;
            --amber: #f59e0b;
            --red: #ef4444;
            --blue: #0ea5e9;
            --surface: #ffffff;
            --surface-2: #f7f9f8;
            --border: #e4ede6;
            --text-primary: #0f2417;
            --text-secondary: #4b6b55;
            --text-muted: #8aab92;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --shadow-sm: 0 1px 3px rgba(39, 89, 49, 0.06), 0 1px 2px rgba(39, 89, 49, 0.04);
            --shadow-md: 0 4px 12px rgba(39, 89, 49, 0.08), 0 2px 6px rgba(39, 89, 49, 0.05);
        }

        * {
            box-sizing: border-box;
        }

        .dash-page {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--surface-2);
            min-height: 100vh;
            padding: 2rem;
            color: var(--text-primary);
        }

        .dash-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .dash-header h1 {
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0;
        }

        .dash-header p {
            margin-top: 4px;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .header-date {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 8px 14px;
            font-size: 0.8125rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .filter-form {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: end;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 16px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 180px;
        }

        .filter-group label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .filter-input,
        .filter-select {
            height: 42px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: #fff;
            padding: 0 12px;
            font-size: 0.875rem;
            color: var(--text-primary);
            outline: none;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-filter {
            height: 42px;
            padding: 0 18px;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-filter-primary {
            background: linear-gradient(90deg, var(--green-primary), var(--green-light));
            color: white;
        }

        .btn-filter-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .alert-strip {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-left: 4px solid #f59e0b;
            border-radius: var(--radius-md);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.8125rem;
            color: #92400e;
            margin-bottom: 1.5rem;
            font-weight: 500;
            flex-wrap: wrap;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .metric-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.25rem 1.5rem;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .metric-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 3px;
            background: linear-gradient(90deg, var(--green-primary), var(--green-light));
        }

        .metric-card.warning::before {
            background: linear-gradient(90deg, #d97706, #fcd34d);
        }

        .metric-card.danger::before {
            background: linear-gradient(90deg, #dc2626, #f87171);
        }

        .metric-card.info::before {
            background: linear-gradient(90deg, #0369a1, #38bdf8);
        }

        .metric-label {
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 10px;
        }

        .metric-value {
            font-size: 1.1rem;
            font-weight: 800;
            line-height: 1.2;
            color: var(--text-primary);
        }

        .metric-sub {
            margin-top: 6px;
            font-size: 0.77rem;
            color: var(--text-muted);
        }

        .grid-2-1 {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .grid-1-1 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.25rem 1.5rem;
            box-shadow: var(--shadow-sm);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            gap: 1rem;
        }

        .card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .card-subtitle {
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        .chart-wrap {
            position: relative;
            width: 100%;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8125rem;
        }

        .data-table thead th {
            text-align: left;
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            padding: 0 10px 10px;
            border-bottom: 1px solid var(--border);
        }

        .data-table tbody td {
            padding: 10px;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            font-size: 0.68rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .badge-success {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-warning {
            background: #fef3c7;
            color: #a16207;
        }

        .badge-danger {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-info {
            background: #e0f2fe;
            color: #0369a1;
        }

        .top-product-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
        }

        .top-product-row:last-child {
            border-bottom: none;
        }

        .rank {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--green-pale);
            color: var(--green-primary);
            font-size: 0.72rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .prod-name {
            font-size: 0.84rem;
            font-weight: 700;
        }

        .prod-sub {
            font-size: 0.72rem;
            color: var(--text-muted);
        }

        .prod-revenue {
            font-size: 0.85rem;
            font-weight: 800;
            text-align: right;
        }

        .prod-units {
            font-size: 0.72rem;
            color: var(--text-muted);
            text-align: right;
        }

        .stock-row {
            margin-bottom: 14px;
        }

        .stock-row:last-child {
            margin-bottom: 0;
        }

        .stock-meta {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            font-size: 0.81rem;
            margin-bottom: 5px;
        }

        .stock-track {
            height: 8px;
            background: var(--surface-2);
            border-radius: 999px;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .stock-fill {
            height: 100%;
            border-radius: 999px;
        }

        .fill-green {
            background: linear-gradient(90deg, var(--green-primary), var(--green-light));
        }

        .fill-amber {
            background: linear-gradient(90deg, #d97706, #fcd34d);
        }

        .fill-red {
            background: linear-gradient(90deg, #dc2626, #f87171);
        }

        @media (max-width: 1200px) {
            .metrics-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 900px) {

            .grid-2-1,
            .grid-1-1 {
                grid-template-columns: 1fr;
            }

            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .dash-page {
                padding: 1rem;
            }

            .metrics-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <div class="dash-page">
        @if ($lowRawMaterialsCount > 0)
            <div class="alert-strip">
                <span>
                    {{ $lowRawMaterialsCount }} stok bahan baku berada di bawah / sama dengan batas minimum
                    {{ $rawMaterialLowStockThreshold }}.
                </span>
            </div>
        @endif

        <div class="dash-header">
            <div>
                <h1>Dashboard Operasional</h1>
                <p>Ringkasan penjualan, piutang, stok, dan bahan baku</p>
            </div>
            <div class="header-date">
                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </div>
        </div>

        <form method="GET" action="{{ url()->current() }}" class="filter-form">
            <div class="filter-group">
                <label for="date_from">Tanggal Dari</label>
                <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}" class="filter-input">
            </div>

            <div class="filter-group">
                <label for="date_to">Tanggal Sampai</label>
                <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}" class="filter-input">
            </div>

            <div class="filter-group">
                <label for="warehouse_id">Gudang</label>
                <select id="warehouse_id" name="warehouse_id" class="filter-select">
                    <option value="">Semua Gudang</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}"
                            {{ (string) $selectedWarehouse === (string) $warehouse->id ? 'selected' : '' }}>
                            {{ $warehouse->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter btn-filter-primary">Filter</button>
                <a href="{{ url()->current() }}" class="btn-filter btn-filter-secondary">Reset</a>
            </div>
        </form>

        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label">Total Penjualan</div>
                <div class="metric-value">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
                <div class="metric-sub">Periode terpilih</div>
            </div>

            <div class="metric-card info">
                <div class="metric-label">Total Transaksi</div>
                <div class="metric-value">{{ number_format($totalTransactions, 0, ',', '.') }}</div>
                <div class="metric-sub">Jumlah invoice penjualan</div>
            </div>

            <div class="metric-card">
                <div class="metric-label">Pembayaran Masuk</div>
                <div class="metric-value">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
                <div class="metric-sub">Akumulasi paid amount</div>
            </div>

            <div class="metric-card danger">
                <div class="metric-label">Piutang</div>
                <div class="metric-value">Rp {{ number_format($totalDebt, 0, ',', '.') }}</div>
                <div class="metric-sub">Sisa tagihan penjualan</div>
            </div>

            <div class="metric-card warning">
                <div class="metric-label">Variant Produk Aktif</div>
                <div class="metric-value">{{ number_format($totalProdukAktif, 0, ',', '.') }}</div>
                <div class="metric-sub">Data dari product_variants</div>
            </div>
        </div>

        <div class="grid-2-1">
            <div class="card">
                <div class="card-header">
                    <div>
                    <div class="card-title">Tren penjualan</div>
                        <div class="card-subtitle">Berdasarkan total_amount per bulan</div>
                    </div>
                </div>
                <div class="chart-wrap" style="height: 260px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Komposisi status pembayaran</div>
                        <div class="card-subtitle">Lunas, terhutang</div>
                    </div>
                </div>
                <div class="chart-wrap" style="height: 260px;">
                    <canvas id="paymentChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid-1-1">
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Transaksi penjualan terbaru</div>
                        <div class="card-subtitle">Diambil dari tabel sales dan sale_items</div>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($latestSales as $sale)
                            @php
                                $paymentBadge = match ($sale->payment_status) {
                                    'lunas' => 'badge-success',
                                    'terhutang' => 'badge-warning',
                                    default => 'badge-danger',
                                };

                                $paymentLabel = match ($sale->payment_status) {
                                    'lunas' => 'Lunas',
                                    'terhutang' => 'Terhutang',
                                    default => 'Terhutang',
                                };
                            @endphp
                            <tr>
                                <td>{{ $sale->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                                <td>{{ $sale->customer_name }}</td>
                                <td>{{ $sale->product }}</td>
                                <td>{{ $sale->qty }}</td>
                                <td>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                                <td><span class="badge {{ $paymentBadge }}">{{ $paymentLabel }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center; color:#8aab92;">Belum ada data transaksi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Top 5 produk terlaris</div>
                        <div class="card-subtitle">Berdasarkan total qty terjual</div>
                    </div>
                </div>

                @forelse ($topProducts as $index => $prod)
                    <div class="top-product-row">
                        <div class="rank">{{ $index + 1 }}</div>
                        <div style="flex:1;">
                            <div class="prod-name">{{ $prod->product_name }}</div>
                            <div class="prod-sub">{{ $prod->variant_name }}</div>
                        </div>
                        <div>
                            <div class="prod-revenue">Rp {{ number_format($prod->total_revenue, 0, ',', '.') }}</div>
                            <div class="prod-units">{{ number_format($prod->total_qty, 0, ',', '.') }} unit</div>
                        </div>
                    </div>
                @empty
                    <div style="color:#8aab92; font-size:0.85rem;">Belum ada data penjualan.</div>
                @endforelse
            </div>
        </div>

        <div class="grid-1-1">
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Top 5 gudang dengan produk terjual terbanyak</div>
                        <div class="card-subtitle">Berdasarkan total quantity penjualan per gudang</div>
                    </div>
                </div>

                @forelse ($topWarehouses as $index => $warehouse)
                    <div class="top-product-row">
                        <div class="rank">{{ $index + 1 }}</div>
                        <div style="flex:1;">
                            <div class="prod-name">{{ $warehouse->name }}</div>
                            <div class="prod-sub">
                                {{ ucfirst($warehouse->type ?? '-') }} • {{ $warehouse->city ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <div class="prod-revenue">Rp {{ number_format($warehouse->total_revenue, 0, ',', '.') }}</div>
                            <div class="prod-units">
                                {{ number_format($warehouse->total_qty_terjual, 0, ',', '.') }} unit •
                                {{ number_format($warehouse->total_transaksi, 0, ',', '.') }} transaksi
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="color:#8aab92; font-size:0.85rem;">Belum ada data penjualan gudang.</div>
                @endforelse
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Distribusi stok produk per gudang</div>
                        <div class="card-subtitle">Akumulasi stock dari product_stocks</div>
                    </div>
                </div>

                @php
                    $maxWarehouseStock = max((int) ($stockByWarehouse->max('total_stock') ?? 1), 1);
                @endphp

                @forelse ($stockByWarehouse as $stock)
                    @php
                        $percent = round(($stock->total_stock / $maxWarehouseStock) * 100, 1);
                        $fillClass = $percent <= 35 ? 'fill-red' : ($percent <= 70 ? 'fill-amber' : 'fill-green');
                    @endphp
                    <div class="stock-row">
                        <div class="stock-meta">
                            <span>{{ $stock->name }}</span>
                            <span>{{ number_format($stock->total_stock, 0, ',', '.') }} unit</span>
                        </div>
                        <div class="stock-track">
                            <div class="stock-fill {{ $fillClass }}" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @empty
                    <div style="color:#8aab92; font-size:0.85rem;">Belum ada data stok produk.</div>
                @endforelse
            </div>
        </div>

        <div class="grid-1-1">
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Bahan baku stok rendah</div>
                        <div class="card-subtitle">Threshold ≤ {{ $rawMaterialLowStockThreshold }}</div>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Bahan Baku</th>
                            <th>Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lowRawMaterials as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ number_format($item->stock, 0, ',', '.') }} {{ $item->unit }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align:center; color:#8aab92;">Tidak ada bahan baku kritis
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('addJs')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const salesTrendLabels = @json($salesTrend->pluck('label'));
            const salesTrendValues = @json($salesTrend->pluck('total'));

            const salesCtx = document.getElementById('salesChart');
            if (salesCtx) {
                new Chart(salesCtx, {
                    type: 'bar',
                    data: {
                        labels: salesTrendLabels,
                        datasets: [{
                            label: 'Penjualan',
                            data: salesTrendValues,
                            backgroundColor: 'rgba(39,89,49,0.15)',
                            borderColor: '#275931',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        return ' Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                border: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                border: {
                                    display: false
                                },
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        }
                    }
                });
            }

            const paymentCtx = document.getElementById('paymentChart');
            if (paymentCtx) {
                new Chart(paymentCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Lunas', 'Terhutang'],
                        datasets: [{
                            data: [
                                {{ $paymentComposition['lunas'] }},
                                {{ $paymentComposition['terhutang'] }},
                            ],
                            backgroundColor: ['#275931', '#f59e0b'],
                            borderWidth: 0,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '62%',
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
