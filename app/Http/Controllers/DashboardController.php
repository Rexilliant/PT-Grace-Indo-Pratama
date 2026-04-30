<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\RawMaterialStock;
use App\Models\Sale;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->date_from)->startOfDay()
            : now()->subDays(29)->startOfDay();

        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->date_to)->endOfDay()
            : now()->endOfDay();

        if ($dateFrom->gt($dateTo)) {
            [$dateFrom, $dateTo] = [$dateTo->copy()->startOfDay(), $dateFrom->copy()->endOfDay()];
        }

        $warehouseId = $request->filled('warehouse_id') ? (int) $request->warehouse_id : null;

        $warehouses = Warehouse::query()
            ->select('id', 'name', 'type', 'city')
            ->orderBy('name')
            ->get();

        $salesFilter = Sale::query()
            ->when($warehouseId, function ($query) use ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            })
            ->whereNull('deleted_at')
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        /*
        |--------------------------------------------------------------------------
        | Summary Penjualan
        |--------------------------------------------------------------------------
        | Status pembayaran ditentukan dari kolom status:
        | - lunas
        | - terhutang
        |--------------------------------------------------------------------------
        */
        $salesSummary = (clone $salesFilter)
            ->selectRaw("
                COALESCE(SUM(total_amount), 0) as total_sales,
                COALESCE(SUM(paid_amount), 0) as total_paid,
                COALESCE(SUM(debt_amount), 0) as total_debt,
                COUNT(*) as total_transactions,
                SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as payment_lunas,
                SUM(CASE WHEN status = 'terhutang' THEN 1 ELSE 0 END) as payment_terhutang
            ")
            ->first();

        $totalSales = (int) ($salesSummary->total_sales ?? 0);
        $totalPaid = (int) ($salesSummary->total_paid ?? 0);
        $totalDebt = (int) ($salesSummary->total_debt ?? 0);
        $totalTransactions = (int) ($salesSummary->total_transactions ?? 0);

        $paymentComposition = [
            'lunas' => (int) ($salesSummary->payment_lunas ?? 0),
            'terhutang' => (int) ($salesSummary->payment_terhutang ?? 0),
        ];

        $totalProdukAktif = ProductVariant::query()
            ->whereNull('deleted_at')
            ->count();

        $rawMaterialLowStockThreshold = 100;

        $lowRawMaterialsBase = RawMaterialStock::query()
            ->join('raw_materials', 'raw_material_stocks.raw_material_id', '=', 'raw_materials.id')
            ->when($warehouseId, function ($query) use ($warehouseId) {
                $query->where('raw_material_stocks.warehouse_id', $warehouseId);
            })
            ->whereNull('raw_material_stocks.deleted_at')
            ->whereNull('raw_materials.deleted_at')
            ->where('raw_material_stocks.stock', '<=', $rawMaterialLowStockThreshold);

        $lowRawMaterialsCount = (clone $lowRawMaterialsBase)->count();

        $lowRawMaterials = (clone $lowRawMaterialsBase)
            ->select([
                'raw_material_stocks.stock',
                'raw_material_stocks.warehouse_id',
                'raw_materials.name',
                'raw_materials.unit',
            ])
            ->orderBy('raw_material_stocks.stock')
            ->limit(7)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Latest Sales
        |--------------------------------------------------------------------------
        | payment_status diambil langsung dari sales.status
        |--------------------------------------------------------------------------
        */
        $latestSalesBase = Sale::query()
            ->leftJoin('warehouses', 'sales.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('sale_items', function ($join) {
                $join->on('sale_items.sale_id', '=', 'sales.id')
                    ->whereNull('sale_items.deleted_at');
            })
            ->when($warehouseId, function ($query) use ($warehouseId) {
                $query->where('sales.warehouse_id', $warehouseId);
            })
            ->whereNull('sales.deleted_at')
            ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
            ->groupBy(
                'sales.id',
                'sales.sale_date',
                'sales.customer_name',
                'sales.total_amount',
                'sales.paid_amount',
                'sales.debt_amount',
                'sales.status',
                'warehouses.name'
            )
            ->orderByDesc('sales.sale_date')
            ->orderByDesc('sales.id')
            ->limit(5)
            ->selectRaw('
                sales.id,
                sales.sale_date,
                sales.customer_name,
                sales.total_amount,
                sales.paid_amount,
                sales.debt_amount,
                sales.status,
                warehouses.name as warehouse_name,
                COALESCE(SUM(sale_items.quantity), 0) as qty
            ')
            ->get();

        $latestSaleIds = $latestSalesBase->pluck('id')->all();

        $firstItemNames = [];
        if (! empty($latestSaleIds)) {
            $firstItemNames = DB::table('sale_items')
                ->join('product_stocks', 'sale_items.product_stock_id', '=', 'product_stocks.id')
                ->join('product_variants', 'product_stocks.product_variant_id', '=', 'product_variants.id')
                ->whereIn('sale_items.sale_id', $latestSaleIds)
                ->whereNull('sale_items.deleted_at')
                ->whereNull('product_stocks.deleted_at')
                ->whereNull('product_variants.deleted_at')
                ->select(
                    'sale_items.sale_id',
                    'sale_items.id as sale_item_id',
                    'product_variants.name as variant_name'
                )
                ->orderBy('sale_items.sale_id')
                ->orderBy('sale_items.id')
                ->get()
                ->groupBy('sale_id')
                ->map(function ($items) {
                    return optional($items->first())->variant_name ?? '-';
                })
                ->toArray();
        }

        $latestSales = $latestSalesBase->map(function ($sale) use ($firstItemNames) {
            return (object) [
                'id' => $sale->id,
                'sale_date' => $sale->sale_date,
                'customer_name' => $sale->customer_name,
                'warehouse_name' => $sale->warehouse_name,
                'product' => $firstItemNames[$sale->id] ?? '-',
                'qty' => (int) $sale->qty,
                'total_amount' => (int) $sale->total_amount,
                'paid_amount' => (int) $sale->paid_amount,
                'debt_amount' => (int) $sale->debt_amount,
                'status' => $sale->status,
                'payment_status' => $sale->status,
            ];
        });

        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('product_stocks', 'sale_items.product_stock_id', '=', 'product_stocks.id')
            ->join('product_variants', 'product_stocks.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->when($warehouseId, function ($query) use ($warehouseId) {
                $query->where('sales.warehouse_id', $warehouseId);
            })
            ->whereNull('sale_items.deleted_at')
            ->whereNull('sales.deleted_at')
            ->whereNull('product_stocks.deleted_at')
            ->whereNull('product_variants.deleted_at')
            ->whereNull('products.deleted_at')
            ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
            ->selectRaw('
                products.name as product_name,
                product_variants.name as variant_name,
                COALESCE(SUM(sale_items.quantity), 0) as total_qty,
                COALESCE(SUM(sale_items.subtotal), 0) as total_revenue
            ')
            ->groupBy('products.name', 'product_variants.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $stockByWarehouse = DB::table('product_stocks')
            ->join('warehouses', 'product_stocks.warehouse_id', '=', 'warehouses.id')
            ->whereNull('product_stocks.deleted_at')
            ->whereNull('warehouses.deleted_at')
            ->selectRaw('
                warehouses.id,
                warehouses.name,
                COALESCE(SUM(product_stocks.stock), 0) as total_stock
            ')
            ->groupBy('warehouses.id', 'warehouses.name')
            ->orderByDesc('total_stock')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Sales Trend
        |--------------------------------------------------------------------------
        | Tetap tampil per bulan-tahun.
        | Total hanya dihitung sesuai date_from dan date_to.
        |--------------------------------------------------------------------------
        */
        $trendRows = Sale::query()
            ->when($warehouseId, function ($query) use ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            })
            ->whereNull('deleted_at')
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->selectRaw("DATE_FORMAT(sale_date, '%Y-%m') as period, COALESCE(SUM(total_amount), 0) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period');

        $salesTrend = collect();

        $cursor = $dateFrom->copy()->startOfMonth();
        $endCursor = $dateTo->copy()->startOfMonth();

        while ($cursor->lte($endCursor)) {
            $key = $cursor->format('Y-m');

            $salesTrend->push([
                'label' => $cursor->translatedFormat('M Y'),
                'total' => (int) ($trendRows[$key] ?? 0),
            ]);

            $cursor->addMonth();
        }

        $topWarehouses = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('warehouses', 'sales.warehouse_id', '=', 'warehouses.id')
            ->when($warehouseId, function ($query) use ($warehouseId) {
                $query->where('sales.warehouse_id', $warehouseId);
            })
            ->whereNull('sale_items.deleted_at')
            ->whereNull('sales.deleted_at')
            ->whereNull('warehouses.deleted_at')
            ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
            ->selectRaw('
                warehouses.id,
                warehouses.name,
                warehouses.type,
                warehouses.city,
                COALESCE(SUM(sale_items.quantity), 0) as total_qty_terjual,
                COALESCE(SUM(sale_items.subtotal), 0) as total_revenue,
                COUNT(DISTINCT sales.id) as total_transaksi
            ')
            ->groupBy('warehouses.id', 'warehouses.name', 'warehouses.type', 'warehouses.city')
            ->orderByDesc('total_qty_terjual')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'warehouses' => $warehouses,
            'dateFrom' => $dateFrom->format('Y-m-d'),
            'dateTo' => $dateTo->format('Y-m-d'),
            'selectedWarehouse' => $warehouseId,

            'totalSales' => $totalSales,
            'totalPaid' => $totalPaid,
            'totalDebt' => $totalDebt,
            'totalTransactions' => $totalTransactions,
            'totalProdukAktif' => $totalProdukAktif,

            'lowRawMaterialsCount' => $lowRawMaterialsCount,
            'lowRawMaterials' => $lowRawMaterials,
            'rawMaterialLowStockThreshold' => $rawMaterialLowStockThreshold,

            'latestSales' => $latestSales,
            'topProducts' => $topProducts,
            'stockByWarehouse' => $stockByWarehouse,
            'salesTrend' => $salesTrend,
            'paymentComposition' => $paymentComposition,
            'topWarehouses' => $topWarehouses,
        ]);
    }
}
