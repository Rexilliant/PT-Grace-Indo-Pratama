<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\RawMaterialStock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;


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
                return [
                    'Kode Barang',
                    'Bahan Baku',
                    'Unit',
                    'Status',
                    'Dibuat Pada',
                ];
            }
        };

        return Excel::download($export, 'bahan_baku_' . now()->format('Ymd_His') . '.xlsx');
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

        // FILTER PROVINCE
        if ($request->filled('status')) {
            $q->where('status', 'like', "%{$request->status}%");
        }

        // ROWS PER PAGE (dropdown 10/25/50)
        $perPage = (int) ($request->get('per_page', 10));
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
        $q = RawMaterialStock::query()->with('rawMaterial');
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
        if ($request->filled('warehouse_id')) {
            $q->where('warehouse_id', $request->warehouse_id);
        }
        $perPage = (int) ($request->get('per_page', 10));
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;
        $stocks = $q->paginate(5)->withQueryString();
        $warehouses = Warehouse::all();

        return view('admin.gudang-stok-bahan-baku', compact('stocks', 'warehouses'));
    }

    /**
     * FORM TAMBAH
     */
    public function create()
    {
        return view('admin.add-bahan-baku');
    }

    /**
     * SIMPAN (4 input)
     * + bikin stok default di raw_material_stocks
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.kode_barang' => 'required|unique:raw_materials,code',
            'items.*.bahan_baku' => 'required',
            'items.*.unit' => 'required',
            'items.*.status' => 'required',
        ]);

        foreach ($request->items as $item) {
            RawMaterial::create([
                'code' => $item['kode_barang'],
                'name' => $item['bahan_baku'],
                'unit' => $item['unit'],
                'status' => $item['status'],
            ]);

            // kalau nanti mau bikin stok default per bahan baku, buka ini lagi
            // RawMaterialStock::create([
            //     'raw_material_id' => $material->id,
            //     'province' => 'Belum diisi',
            //     'stock' => 0,
            // ]);
        }

        return redirect()
            ->route('admin.gudang-bahan-baku')
            ->with('success', 'Bahan baku berhasil ditambahkan!');
    }

    /**
     * FORM EDIT (master barang aja)
     */
    public function edit($id)
    {
        $material = RawMaterial::with('stock')->findOrFail($id);

        return view('admin.edit-bahan-baku', compact('material'));
    }

    /**
     * UPDATE (master barang aja)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_barang' => 'required|unique:raw_materials,code,'.$id,
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

        return redirect()
            ->route('admin.gudang-bahan-baku')
            ->with('success', 'Bahan baku berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $material = RawMaterial::findOrFail($id);
        $material->delete();

        return redirect()
            ->route('admin.gudang-bahan-baku')
            ->with('success', 'Bahan baku berhasil dihapus!');
    }
}
