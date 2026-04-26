@extends('admin.layout.master')

@section('open-dashboard', 'open')
@section('menu-dashboard', 'bg-gradient-to-r from-[#53BF6A] to-[#275931] text-white')

@section('addCss')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --green-primary: #275931;
            --green-mid: #3a8a50;
            --green-light: #53BF6A;
            --green-soft: #ecfdf3;
            --surface: #ffffff;
            --surface-2: #f4f7f5;
            --border: #dfe9e2;
            --text-primary: #102116;
            --text-secondary: #4b6653;
            --text-muted: #7f9b87;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #0ea5e9;
            --radius: 18px;
            --shadow: 0 12px 35px rgba(39, 89, 49, 0.08);
            --shadow-sm: 0 4px 14px rgba(39, 89, 49, 0.06);
        }

        * {
            box-sizing: border-box;
        }

        .dash-page {
            min-height: 100vh;
            padding: 24px;
            background:
                radial-gradient(circle at top left, rgba(83, 191, 106, 0.18), transparent 34%),
                linear-gradient(180deg, #f8fbf9 0%, var(--surface-2) 100%);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-primary);
        }

        .dash-container {
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
        }

        .dash-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }

        .dash-title-wrap {
            min-width: 0;
        }

        .dash-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(83, 191, 106, 0.13);
            color: var(--green-primary);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .dash-header h1 {
            margin: 0;
            font-size: clamp(24px, 3vw, 34px);
            font-weight: 800;
            letter-spacing: -0.04em;
            color: var(--text-primary);
        }

        .dash-header p {
            margin: 7px 0 0;
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.6;
        }

        .header-date {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 10px 16px;
            border: 1px solid var(--border);
            border-radius: 999px;
            background: rgba(255, 255, 255, .86);
            box-shadow: var(--shadow-sm);
            color: var(--text-secondary);
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
        }

        .alert-strip {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid #fed7aa;
            background: #fff7ed;
            color: #92400e;
            font-size: 13px;
            font-weight: 700;
            box-shadow: var(--shadow-sm);
        }

        .alert-icon {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            background: #ffedd5;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            align-items: end;
            margin-bottom: 22px;
            padding: 18px;
            border: 1px solid var(--border);
            border-radius: 22px;
            background: rgba(255, 255, 255, .92);
            box-shadow: var(--shadow);
            backdrop-filter: blur(8px);
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
            min-width: 0;
        }

        .filter-group label {
            font-size: 12px;
            font-weight: 800;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .filter-input,
        .filter-select {
            width: 100%;
            height: 44px;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: #fff;
            padding: 0 13px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            outline: none;
            transition: .2s ease;
        }

        .filter-input:focus,
        .filter-select:focus {
            border-color: var(--green-light);
            box-shadow: 0 0 0 4px rgba(83, 191, 106, .16);
        }

        .filter-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .btn-filter {
            height: 44px;
            padding: 0 16px;
            border: none;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: .2s ease;
            white-space: nowrap;
        }

        .btn-filter:hover {
            transform: translateY(-1px);
        }

        .btn-filter-primary {
            background: linear-gradient(135deg, var(--green-primary), var(--green-light));
            color: #fff;
            box-shadow: 0 10px 20px rgba(39, 89, 49, .18);
        }

        .btn-filter-secondary {
            background: #eef2ef;
            color: #425147;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 18px;
        }

        .metric-card {
            position: relative;
            overflow: hidden;
            min-height: 145px;
            padding: 20px;
            border: 1px solid var(--border);
            border-radius: 22px;
            background: var(--surface);
            box-shadow: var(--shadow-sm);
        }

        .metric-card::before {
            content: "";
            position: absolute;
            inset: 0 0 auto;
            height: 5px;
            background: linear-gradient(90deg, var(--green-primary), var(--green-light));
        }

        .metric-card::after {
            content: "";
            position: absolute;
            right: -35px;
            bottom: -35px;
            width: 105px;
            height: 105px;
            border-radius: 50%;
            background: rgba(83, 191, 106, .09);
        }

        .metric-card.warning::before {
            background: linear-gradient(90deg, #d97706, #fbbf24);
        }

        .metric-card.danger::before {
            background: linear-gradient(90deg, #dc2626, #f87171);
        }

        .metric-card.info::before {
            background: linear-gradient(90deg, #0369a1, #38bdf8);
        }

        .metric-label {
            position: relative;
            z-index: 1;
            margin-bottom: 12px;
            font-size: 11px;
            font-weight: 800;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: .07em;
        }

        .metric-value {
            position: relative;
            z-index: 1;
            font-size: clamp(18px, 2vw, 23px);
            font-weight: 800;
            line-height: 1.25;
            color: var(--text-primary);
            word-break: break-word;
        }

        .metric-sub {
            position: relative;
            z-index: 1;
            margin-top: 8px;
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .grid-2-1 {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(320px, 1fr);
            gap: 16px;
            margin-bottom: 16px;
        }

        .grid-1-1 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }

        .card {
            min-width: 0;
            border: 1px solid var(--border);
            border-radius: 22px;
            background: var(--surface);
            padding: 20px;
            box-shadow: var(--shadow-sm);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
        }

        .card-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--text-primary);
        }

        .card-subtitle {
            margin-top: 4px;
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .chart-wrap {
            position: relative;
            width: 100%;
            height: 280px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .data-table {
            width: 100%;
            min-width: 720px;
            border-collapse: collapse;
            font-size: 13px;
        }

        .data-table thead th {
            text-align: left;
            color: var(--text-muted);
            font-weight: 800;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .07em;
            padding: 0 12px 12px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        .data-table tbody td {
            padding: 13px 12px;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            white-space: nowrap;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .data-table tbody tr:hover td {
            background: #fbfdfb;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 25px;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
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

        .top-product-row {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 13px 0;
            border-bottom: 1px solid var(--border);
        }

        .top-product-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .rank {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            background: var(--green-soft);
            color: var(--green-primary);
            font-size: 13px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .prod-info {
            flex: 1;
            min-width: 0;
        }

        .prod-name {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-primary);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .prod-sub {
            margin-top: 3px;
            font-size: 12px;
            color: var(--text-muted);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .prod-value {
            flex-shrink: 0;
            text-align: right;
        }

        .prod-revenue {
            font-size: 13px;
            font-weight: 800;
            white-space: nowrap;
        }

        .prod-units {
            margin-top: 3px;
            font-size: 12px;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .stock-row {
            margin-bottom: 16px;
        }

        .stock-row:last-child {
            margin-bottom: 0;
        }

        .stock-meta {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 700;
        }

        .stock-name {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .stock-value {
            flex-shrink: 0;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .stock-track {
            height: 10px;
            background: #f0f4f1;
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

        .empty-state {
            padding: 18px;
            border-radius: 16px;
            background: #f8faf9;
            color: var(--text-muted);
            font-size: 14px;
            text-align: center;
            font-weight: 600;
        }

        @media (max-width: 1280px) {
            .metrics-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .filter-form {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 1024px) {
            .grid-2-1,
            .grid-1-1 {
                grid-template-columns: 1fr;
            }

            .chart-wrap {
                height: 260px;
            }
        }

        @media (max-width: 768px) {
            .dash-page {
                padding: 16px;
            }

            .dash-header {
                flex-direction: column;
            }

            .header-date {
                width: 100%;
            }

            .metrics-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            .metric-card,
            .card,
            .filter-form {
                border-radius: 18px;
            }

            .filter-form {
                grid-template-columns: 1fr;
                padding: 15px;
            }

            .filter-actions {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 520px) {
            .dash-page {
                padding: 12px;
            }

            .metrics-grid {
                grid-template-columns: 1fr;
            }

            .metric-card {
                min-height: auto;
                padding: 18px;
            }

            .card {
                padding: 16px;
            }

            .chart-wrap {
                height: 240px;
            }

            .top-product-row {
                align-items: flex-start;
            }

            .prod-value {
                max-width: 130px;
            }

            .prod-revenue,
            .prod-units {
                white-space: normal;
            }
        }
    </style>
@endsection

@section('content')
    <div class="dash-page">
        <div class="dash-container">
            @if ($lowRawMaterialsCount > 0)
                <div class="alert-strip">
                    <span class="alert-icon">⚠️</span>
                    <span>
                        {{ $lowRawMaterialsCount }} stok bahan baku berada di bawah / sama dengan batas minimum
                        {{ $rawMaterialLowStockThreshold }}.
                    </span>
                </div>
            @endif

            <div class="dash-header">
                <div class="dash-title-wrap">
                    <div class="dash-kicker">Dashboard</div>
                    <h1>Dashboard Operasional</h1>
                    <p>Ringkasan penjualan, piutang, stok produk, gudang, dan bahan baku.</p>
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
                    <div class="metric-sub">Data dari product variants</div>
                </div>
            </div>

            <div class="grid-2-1">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Tren Penjualan</div>
                            <div class="card-subtitle">Berdasarkan total amount per bulan</div>
                        </div>
                    </div>

                    <div class="chart-wrap">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Komposisi Status Pembayaran</div>
                            <div class="card-subtitle">Lunas dan terhutang</div>
                        </div>
                    </div>

                    <div class="chart-wrap">
                        <canvas id="paymentChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid-1-1">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Transaksi Penjualan Terbaru</div>
                            <div class="card-subtitle">Diambil dari tabel sales dan sale items</div>
                        </div>
                    </div>

                    <div class="table-responsive">
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
                                        <td>
                                            <span class="badge {{ $paymentBadge }}">{{ $paymentLabel }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" style="text-align:center; color:#8aab92;">
                                            Belum ada data transaksi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Top 5 Produk Terlaris</div>
                            <div class="card-subtitle">Berdasarkan total qty terjual</div>
                        </div>
                    </div>

                    @forelse ($topProducts as $index => $prod)
                        <div class="top-product-row">
                            <div class="rank">{{ $index + 1 }}</div>

                            <div class="prod-info">
                                <div class="prod-name">{{ $prod->product_name }}</div>
                                <div class="prod-sub">{{ $prod->variant_name }}</div>
                            </div>

                            <div class="prod-value">
                                <div class="prod-revenue">Rp {{ number_format($prod->total_revenue, 0, ',', '.') }}</div>
                                <div class="prod-units">{{ number_format($prod->total_qty, 0, ',', '.') }} unit</div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">Belum ada data penjualan.</div>
                    @endforelse
                </div>
            </div>

            <div class="grid-1-1">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Top 5 Gudang dengan Produk Terjual Terbanyak</div>
                            <div class="card-subtitle">Berdasarkan total quantity penjualan per gudang</div>
                        </div>
                    </div>

                    @forelse ($topWarehouses as $index => $warehouse)
                        <div class="top-product-row">
                            <div class="rank">{{ $index + 1 }}</div>

                            <div class="prod-info">
                                <div class="prod-name">{{ $warehouse->name }}</div>
                                <div class="prod-sub">
                                    {{ ucfirst($warehouse->type ?? '-') }} • {{ $warehouse->city ?? '-' }}
                                </div>
                            </div>

                            <div class="prod-value">
                                <div class="prod-revenue">Rp {{ number_format($warehouse->total_revenue, 0, ',', '.') }}</div>
                                <div class="prod-units">
                                    {{ number_format($warehouse->total_qty_terjual, 0, ',', '.') }} unit •
                                    {{ number_format($warehouse->total_transaksi, 0, ',', '.') }} transaksi
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">Belum ada data penjualan gudang.</div>
                    @endforelse
                </div>

                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Distribusi Stok Produk per Gudang</div>
                            <div class="card-subtitle">Akumulasi stok dari product stocks</div>
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
                                <span class="stock-name">{{ $stock->name }}</span>
                                <span class="stock-value">{{ number_format($stock->total_stock, 0, ',', '.') }} unit</span>
                            </div>

                            <div class="stock-track">
                                <div class="stock-fill {{ $fillClass }}" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">Belum ada data stok produk.</div>
                    @endforelse
                </div>
            </div>

            <div class="grid-1-1">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Bahan Baku Stok Rendah</div>
                            <div class="card-subtitle">Threshold ≤ {{ $rawMaterialLowStockThreshold }}</div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="data-table" style="min-width: 420px;">
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
                                        <td colspan="2" style="text-align:center; color:#8aab92;">
                                            Tidak ada bahan baku kritis.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div></div>
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
                            backgroundColor: 'rgba(83, 191, 106, 0.18)',
                            borderColor: '#275931',
                            borderWidth: 2,
                            borderRadius: 12,
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
                                },
                                ticks: {
                                    color: '#7f9b87',
                                    font: {
                                        size: 11,
                                        weight: '600'
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                border: {
                                    display: false
                                },
                                grid: {
                                    color: 'rgba(223, 233, 226, .8)'
                                },
                                ticks: {
                                    color: '#7f9b87',
                                    font: {
                                        size: 11,
                                        weight: '600'
                                    },
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
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '66%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    color: '#4b6653',
                                    font: {
                                        size: 12,
                                        weight: '700'
                                    },
                                    padding: 18
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection