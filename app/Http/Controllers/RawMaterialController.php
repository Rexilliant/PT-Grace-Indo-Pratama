<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\RawMaterialStock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawMaterialController extends Controller
{
    public function export(Request $request)
    {
        $q = RawMaterial::query()->orderBy('created_at', 'desc');

        if ($request->filled('code')) {
            $q->where('code', 'like', "%{$request->code}%");
        }

        if ($request->filled('name')) {
            $q->where('name', 'like', "%{$request->name}%");
        }

        if ($request->filled('status')) {
            $q->where('status', 'like', "%{$request->status}%");
        }

        $rows = $q->get()->map(function ($material) {
            return [
                'Kode Barang' => $material->code,
                'Bahan Baku' => $material->name,
                'Unit' => $material->unit,
                'Status' => $material->status,
                'Dibuat Pada' => $material->created_at ? $material->created_at->format('Y-m-d H:i:s') : '-',
            ];
        });

        $export = new class ($rows) implements FromCollection, WithHeadings {
            public function __construct(private $rows)
            {}

            public function collection()
            {
                return $this->rows;
            }

            public function headings(): array
            {
                return ['Kode Barang', 'Bahan Baku', 'Unit', 'Status', 'Dibuat Pada'];
            }
        };

        return Excel::download($export, 'bahan_baku_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function exportStock(Request $request)
    {
        $warehouseId = auth()->user()->employee?->warehouse_id;
        $q = RawMaterialStock::query()->with(['rawMaterial', 'warehouse']);

        if ($warehouseId) {
            $q->where('warehouse_id', $warehouseId);
        } elseif ($request->filled('warehouse_id')) {
            $q->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('code')) {
            $q->whereHas('rawMaterial', function ($u) use ($request) {
                $u->where('code', 'like', "%{$request->code}%");
            });
        }

        if ($request->filled('name')) {
            $search = $request->name;
            $q->whereHas('rawMaterial', function ($u) use ($search) {
                $u->where('name', 'like', "%{$search}%");
            });
        }

        $rows = $q->get()->map(function ($stock) {
            return [
                'Kode Barang' => $stock->rawMaterial->code ?? '-',
                'Bahan Baku' => $stock->rawMaterial->name ?? '-',
                'Gudang' => $stock->warehouse->name ?? '-',
                'Jumlah Stok' => $stock->stock,
            ];
        });

        $export = new class ($rows) implements FromCollection, WithHeadings {
            public function __construct(private $rows)
            {}

            public function collection()
            {
                return $this->rows;
            }

            public function headings(): array
            {
                return ['Kode Barang', 'Bahan Baku', 'Gudang', 'Jumlah Stok'];
            }
        };

        return Excel::download($export, 'stok_bahan_baku_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function index(Request $request)
    {
        $q = RawMaterial::query()->orderBy('created_at', 'desc');

        if ($request->filled('code')) {
            $q->where('code', 'like', "%{$request->code}%");
        }

        if ($request->filled('name')) {
            $q->where('name', 'like', "%{$request->name}%");
        }

        if ($request->filled('status')) {
            $q->where('status', 'like', "%{$request->status}%");
        }

        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;

        $materials = $q->paginate($perPage)->withQueryString();

        $statuses = RawMaterial::query()
            ->select('status')
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        return view('admin.raw_materials.raw_materials', compact('materials', 'statuses'));
    }

    public function stockIndex(Request $request)
    {
        $warehouseId = auth()->user()->employee?->warehouse_id;
        $q = RawMaterialStock::query()->with('rawMaterial');

        // Mengatur hirarki filter gudang
        if ($warehouseId) {
            $q->where('warehouse_id', $warehouseId);
        } elseif ($request->filled('warehouse_id')) {
            $q->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('code')) {
            $q->whereHas('rawMaterial', function ($u) use ($request) {
                $u->where('code', 'like', "%{$request->code}%");
            });
        }

        if ($request->filled('name')) {
            $search = $request->name;
            $q->whereHas('rawMaterial', function ($u) use ($search) {
                $u->where('name', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;

        // Menggunakan variabel $perPage secara dinamis
        $stocks = $q->paginate($perPage)->withQueryString();

        if ($warehouseId) {
            $warehouses = Warehouse::where('id', $warehouseId)->get();
        } else {
            $warehouses = Warehouse::all();
        }

        return view('admin.raw_materials_inventory.gudang-stok-bahan-baku', compact('stocks', 'warehouses'));
    }

    public function create()
    {
        return view('admin.raw_materials.add-bahan-baku');
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.kode_barang' => 'required|distinct|unique:raw_materials,code',
            'items.*.bahan_baku' => 'required',
            'items.*.unit' => 'required',
            'items.*.status' => 'required',
        ], [
            'items.*.kode_barang.unique' => 'Kode barang ":input" sudah digunakan di database!',
            'items.*.kode_barang.distinct' => 'Ada kode barang yang sama/duplikat dalam form ini.',
        ]);

        foreach ($request->items as $item) {
            RawMaterial::create([
                'code' => $item['kode_barang'],
                'name' => $item['bahan_baku'],
                'unit' => $item['unit'],
                'status' => $item['status'],
            ]);
        }

        return redirect()->route('admin.gudang-bahan-baku')->with('success', 'Bahan baku berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $material = RawMaterial::with('stock')->findOrFail($id);

        return view('admin.raw_materials.edit-bahan-baku', compact('material'));
    }

    public function checkCode(Request $request)
    {
        $code = trim($request->query('code'));
        $exceptId = $request->query('except_id');

        if (empty($code)) {
            return response()->json(['exists' => false]);
        }

        // Pake withTrashed() biar data terhapus (soft delete) tetep kedeteksi
        $query = RawMaterial::withTrashed()
            ->whereRaw('LOWER(code) = ?', [mb_strtolower($code)]);

        if (!empty($exceptId)) {
            $query->where('id', '!=', $exceptId);
        }

        $item = $query->first();

        if ($item) {
            $msg = $item->trashed()
                ? "Kode barang '{$code}' sudah pernah digunakan (status terhapus/soft delete)."
                : "Kode barang '{$code}' sudah terdaftar di sistem.";

            return response()->json(['exists' => true, 'message' => $msg]);
        }

        return response()->json(['exists' => false, 'message' => "Kode barang tersedia."]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_barang' => 'required|unique:raw_materials,code,' . $id,
            'bahan_baku' => 'required',
            'unit' => 'required',
            'status' => 'required',
        ]);

        $material = RawMaterial::findOrFail($id);

        $material->update([
            'code' => $request->kode_barang,
            'name' => $request->bahan_baku,
            'unit' => $request->unit,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.gudang-bahan-baku')->with('success', 'Bahan baku berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $material = RawMaterial::findOrFail($id);
        $material->delete();

        return redirect()->route('admin.gudang-bahan-baku')->with('success', 'Bahan baku berhasil dihapus!');
    }
}