<?php

namespace App\Http\Controllers;

use App\Models\ProductStockMovement;
use App\Models\RawMaterialStockMovement;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category', 'raw_material'); // raw_material | product
        $userWarehouseId = auth()->user()->employee?->warehouse_id;

        // Rows per page
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;

        if ($category === 'product') {
            $q = ProductStockMovement::query()
                ->with(['warehouse', 'productStock.productVariant.product'])
                ->orderBy('created_at', 'desc');

            if ($userWarehouseId) {
                $q->where('warehouse_id', $userWarehouseId);
            } elseif ($request->filled('warehouse_id')) {
                $q->where('warehouse_id', $request->warehouse_id);
            }

            if ($request->filled('type')) {
                $q->where('type', $request->type);
            }

            if ($request->filled('name')) {
                $search = $request->name;
                $q->whereHas('productStock.productVariant', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhereHas('product', function ($p) use ($search) {
                            $p->where('name', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->filled('code')) {
                $code = $request->code;
                $q->whereHas('productStock.productVariant', function ($sub) use ($code) {
                    $sub->where('sku', 'like', "%{$code}%");
                });
            }

            if ($request->filled('note')) {
                $note = $request->note;
                $q->where('note', 'like', "%{$note}%");
            }

            if ($request->filled('date_from')) {
                $q->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $q->whereDate('created_at', '<=', $request->date_to);
            }

            $movements = $q->paginate($perPage)->withQueryString();
        } else {
            // Default: Raw Material
            $q = RawMaterialStockMovement::query()
                ->with(['warehouse', 'rawMaterial', 'responsible'])
                ->orderBy('created_at', 'desc');

            if ($userWarehouseId) {
                $q->where('warehouse_id', $userWarehouseId);
            } elseif ($request->filled('warehouse_id')) {
                $q->where('warehouse_id', $request->warehouse_id);
            }

            if ($request->filled('type')) {
                $q->where('type', $request->type);
            }

            if ($request->filled('name')) {
                $search = $request->name;
                $q->whereHas('rawMaterial', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });
            }

            if ($request->filled('code')) {
                $code = $request->code;
                $q->whereHas('rawMaterial', function ($sub) use ($code) {
                    $sub->where('code', 'like', "%{$code}%");
                });
            }

            if ($request->filled('note')) {
                $note = $request->note;
                $q->where('note', 'like', "%{$note}%");
            }

            if ($request->filled('date_from')) {
                $q->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $q->whereDate('created_at', '<=', $request->date_to);
            }

            $movements = $q->paginate($perPage)->withQueryString();
        }

        // Warehouse list
        if ($userWarehouseId) {
            $warehouses = Warehouse::where('id', $userWarehouseId)->get();
        } else {
            $warehouses = Warehouse::orderBy('name')->get();
        }

        return view('admin.stock_movements.stock-movements', compact('movements', 'warehouses', 'category'));
    }

    public function export(Request $request)
    {
        $category = $request->get('category', 'raw_material');
        $userWarehouseId = auth()->user()->employee?->warehouse_id;

        if ($category === 'product') {
            $q = ProductStockMovement::query()
                ->with(['warehouse', 'productStock.productVariant.product'])
                ->orderBy('created_at', 'desc');

            if ($userWarehouseId) {
                $q->where('warehouse_id', $userWarehouseId);
            } elseif ($request->filled('warehouse_id')) {
                $q->where('warehouse_id', $request->warehouse_id);
            }

            if ($request->filled('type')) {
                $q->where('type', $request->type);
            }

            if ($request->filled('name')) {
                $search = $request->name;
                $q->whereHas('productStock.productVariant', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhereHas('product', function ($p) use ($search) {
                            $p->where('name', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->filled('code')) {
                $code = $request->code;
                $q->whereHas('productStock.productVariant', function ($sub) use ($code) {
                    $sub->where('sku', 'like', "%{$code}%");
                });
            }

            if ($request->filled('note')) {
                $note = $request->note;
                $q->where('note', 'like', "%{$note}%");
            }

            if ($request->filled('date_from')) {
                $q->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $q->whereDate('created_at', '<=', $request->date_to);
            }

            $rows = $q->get()->map(function ($m) {
                $typeLabel = strtolower($m->type) === 'in' ? 'Masuk' : (strtolower($m->type) === 'out' ? 'Keluar' : $m->type);
                $qty = strtolower($m->type) === 'in' ? "+{$m->quantity}" : "-{$m->quantity}";
                $unit = $m->productStock?->productVariant?->unit ?? 'unit';
                $productName = $m->productStock?->productVariant?->name ?? '-';

                return [
                    'Waktu' => $m->created_at ? $m->created_at->timezone(config('app.timezone', 'Asia/Jakarta'))->format('d/m/Y H:i') : '-',
                    'Gudang' => $m->warehouse?->name ?? '-',
                    'SKU' => $m->productStock?->productVariant?->sku ?? '-',
                    'Nama Produk' => $productName,
                    'Tipe' => $typeLabel,
                    'Jumlah' => $qty,
                    'Satuan' => $unit,
                    'Keterangan' => $m->note ?? '-',
                ];
            });

            $export = new class ($rows) implements FromCollection, WithHeadings {
                public function __construct(private $rows) {}

                public function collection()
                {
                    return $this->rows;
                }

                public function headings(): array
                {
                    return ['Waktu', 'Gudang', 'SKU', 'Nama Produk', 'Tipe', 'Jumlah', 'Satuan', 'Keterangan'];
                }
            };

            return Excel::download($export, 'mutasi_stok_produk_' . now()->format('Ymd_His') . '.xlsx');
        } else {
            $q = RawMaterialStockMovement::query()
                ->with(['warehouse', 'rawMaterial', 'responsible'])
                ->orderBy('created_at', 'desc');

            if ($userWarehouseId) {
                $q->where('warehouse_id', $userWarehouseId);
            } elseif ($request->filled('warehouse_id')) {
                $q->where('warehouse_id', $request->warehouse_id);
            }

            if ($request->filled('type')) {
                $q->where('type', $request->type);
            }

            if ($request->filled('name')) {
                $search = $request->name;
                $q->whereHas('rawMaterial', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });
            }

            if ($request->filled('code')) {
                $code = $request->code;
                $q->whereHas('rawMaterial', function ($sub) use ($code) {
                    $sub->where('code', 'like', "%{$code}%");
                });
            }

            if ($request->filled('note')) {
                $note = $request->note;
                $q->where('note', 'like', "%{$note}%");
            }

            if ($request->filled('date_from')) {
                $q->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $q->whereDate('created_at', '<=', $request->date_to);
            }

            $rows = $q->get()->map(function ($m) {
                $typeLabel = strtolower($m->type) === 'in' ? 'Masuk' : (strtolower($m->type) === 'out' ? 'Keluar' : $m->type);
                $qty = strtolower($m->type) === 'in' ? "+{$m->stock}" : "-{$m->stock}";
                $unit = $m->rawMaterial?->unit ?? '';

                return [
                    'Waktu' => $m->created_at ? $m->created_at->timezone(config('app.timezone', 'Asia/Jakarta'))->format('d/m/Y H:i') : '-',
                    'Gudang' => $m->warehouse?->name ?? '-',
                    'Kode Barang' => $m->rawMaterial?->code ?? '-',
                    'Bahan Baku' => $m->rawMaterial?->name ?? '-',
                    'Tipe' => $typeLabel,
                    'Jumlah' => $qty,
                    'Satuan' => $unit,
                    'Penanggung Jawab' => $m->responsible?->name ?? '-',
                    'Keterangan' => $m->note ?? '-',
                ];
            });

            $export = new class ($rows) implements FromCollection, WithHeadings {
                public function __construct(private $rows) {}

                public function collection()
                {
                    return $this->rows;
                }

                public function headings(): array
                {
                    return ['Waktu', 'Gudang', 'Kode Barang', 'Bahan Baku', 'Tipe', 'Jumlah', 'Satuan', 'Penanggung Jawab', 'Keterangan'];
                }
            };

            return Excel::download($export, 'mutasi_stok_bahan_baku_' . now()->format('Ymd_His') . '.xlsx');
        }
    }
}
