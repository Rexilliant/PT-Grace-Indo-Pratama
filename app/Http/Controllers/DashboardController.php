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

        $warehouseId = $request->warehouse_id;

        $warehouses = Warehouse::orderBy('name')->get();

        $salesBaseQuery = Sale::query()
            ->when($warehouseId, function ($query) use ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            })
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        $totalSales = (clone $salesBaseQuery)->sum('total_amount');
        $totalPaid = (clone $salesBaseQuery)->sum('paid_amount');
        $totalDebt = (clone $salesBaseQuery)->sum('debt_amount');
        $totalTransactions = (clone $salesBaseQuery)->count();

        $totalProdukAktif = ProductVariant::count();

        $rawMaterialLowStockThreshold = 300;

        $lowRawMaterialsQuery = RawMaterialStock::query()
            ->join('raw_materials', 'raw_material_stocks.raw_material_id', '=', 'raw_materials.id')
            ->when($warehouseId, function ($query) use ($warehouseId) {
                $query->where('raw_material_stocks.warehouse_id', $warehouseId);
            })
            ->whereNull('raw_material_stocks.deleted_at')
            ->whereNull('raw_materials.deleted_at')
            ->where('raw_material_stocks.stock', '<=', $rawMaterialLowStockThreshold);

        $lowRawMaterialsCount = (clone $lowRawMaterialsQuery)->count();

        $lowRawMaterials = (clone $lowRawMaterialsQuery)
            ->select(
                'raw_material_stocks.stock',
                'raw_materials.name',
                'raw_materials.unit',
                'raw_material_stocks.warehouse_id'
            )
            ->orderBy('raw_material_stocks.stock')
            ->limit(7)
            ->get();

        $latestSales = Sale::query()
            ->with([
                'items.productStock.productVariant.product',
                'warehouse',
            ])
            ->when($warehouseId, function ($query) use ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            })
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->latest('sale_date')
            ->limit(5)
            ->get()
            ->map(function ($sale) {
                $firstItem = $sale->items->first();

                $productName = '-';
                $qty = $sale->items->sum('quantity');

                if ($firstItem && $firstItem->productStock && $firstItem->productStock->productVariant) {
                    $variant = $firstItem->productStock->productVariant;
                    $product = $variant->product;

                    $productName = $variant->name;
                }

                $paymentStatus = 'belum_bayar';
                if ((int) $sale->paid_amount >= (int) $sale->total_amount && (int) $sale->total_amount > 0) {
                    $paymentStatus = 'lunas';
                } elseif ((int) $sale->paid_amount > 0) {
                    $paymentStatus = 'sebagian';
                }

                return (object) [
                    'id' => $sale->id,
                    'sale_date' => $sale->sale_date,
                    'customer_name' => $sale->customer_name,
                    'warehouse_name' => optional($sale->warehouse)->name,
                    'product' => $productName,
                    'qty' => $qty,
                    'total_amount' => $sale->total_amount,
                    'status' => $sale->status,
                    'payment_status' => $paymentStatus,
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
            ->select(
                'products.name as product_name',
                'product_variants.name as variant_name',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->groupBy('products.name', 'product_variants.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $stockByWarehouse = DB::table('product_stocks')
            ->join('warehouses', 'product_stocks.warehouse_id', '=', 'warehouses.id')
            ->whereNull('product_stocks.deleted_at')
            ->whereNull('warehouses.deleted_at')
            ->select(
                'warehouses.id',
                'warehouses.name',
                DB::raw('SUM(product_stocks.stock) as total_stock')
            )
            ->groupBy('warehouses.id', 'warehouses.name')
            ->orderByDesc('total_stock')
            ->get();

        $chartMonths = collect(range(5, 0))->map(function ($minus) {
            return now()->startOfMonth()->subMonths($minus);
        })->push(now()->startOfMonth());

        $salesTrend = $chartMonths->map(function ($month) use ($warehouseId) {
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $total = Sale::query()
                ->when($warehouseId, function ($query) use ($warehouseId) {
                    $query->where('warehouse_id', $warehouseId);
                })
                ->whereBetween('sale_date', [$start, $end])
                ->sum('total_amount');

            return [
                'label' => $month->translatedFormat('M Y'),
                'total' => (int) $total,
            ];
        });

        $paymentComposition = [
            'lunas' => (clone $salesBaseQuery)
                ->whereColumn('paid_amount', '>=', 'total_amount')
                ->where('total_amount', '>', 0)
                ->count(),
            'sebagian' => (clone $salesBaseQuery)
                ->where('paid_amount', '>', 0)
                ->whereColumn('paid_amount', '<', 'total_amount')
                ->count(),
            'belum_bayar' => (clone $salesBaseQuery)
                ->where('paid_amount', '<=', 0)
                ->count(),
        ];
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
            ->select(
                'warehouses.id',
                'warehouses.name',
                'warehouses.type',
                'warehouses.city',
                DB::raw('SUM(sale_items.quantity) as total_qty_terjual'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT sales.id) as total_transaksi')
            )
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
