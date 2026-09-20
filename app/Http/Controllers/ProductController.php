<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function export(Request $request)
    {
        $q = Product::query()->orderBy('created_at', 'desc');

        if ($request->filled('code')) {
            $q->where('code', 'like', '%' . $request->code . '%');
        }

        if ($request->filled('name')) {
            $q->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        $rows = $q->get()->map(function ($p) {
            return [
                'ID Produk (Code)' => $p->code,
                'Nama Produk' => $p->name,
                'Status' => $p->status,
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
                return ['ID Produk (Code)', 'Nama Produk', 'Status'];
            }
        };

        return Excel::download($export, 'produk.xlsx');
    }

    // CREATE (FORM TAMBAH)
    public function createExecutive()
    {
        return view('admin.ex_product.add-executive-produk-baru');
    }

    // STORE (SIMPAN)
    public function storeExecutive(Request $request)
    {
        $validated = $request->validate([
            'products' => ['required', 'array', 'min:1'],
            'products.*.code' => ['required', 'string', 'max:255', 'unique:products,code'],
            'products.*.name' => ['required', 'string', 'max:255'],
            'products.*.status' => ['required', 'in:aktif,nonaktif'],
        ], [
            'products.*.code.required' => 'Kode produk wajib diisi.',
            'products.*.code.unique' => 'Kode produk sudah digunakan.',
            'products.*.name.required' => 'Nama produk wajib diisi.',
            'products.*.status.required' => 'Status wajib dipilih.',
        ]);

        foreach ($validated['products'] as $item) {
            Product::create([
                'code' => $item['code'],
                'name' => $item['name'],
                'status' => $item['status'],
            ]);
        }

        return redirect()
            ->route('admin.executive-produk')
            ->with('success', count($validated['products']) . ' Produk berhasil ditambahkan.');
    }

    // EDIT (FORM EDIT) - AMBIL DARI DB
    public function editExecutive($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.ex_product.edit-executive-produk', compact('product'));
    }

    // UPDATE (SIMPAN PERUBAHAN)
    public function updateExecutive(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:products,code,' . $product->id],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        $product->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.executive-produk')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function indexExecutive(Request $request)
    {
        $query = Product::query()->latest();

        if ($request->filled('code')) {
            $query->where('code', 'like', '%' . $request->code . '%');
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;

        $products = $query->paginate($perPage)->withQueryString();

        return view('admin.ex_product.executive-produk', compact('products'));
    }

    public function destroyExecutive($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->forceDelete();

        return redirect()
            ->route('admin.executive-produk')
            ->with('success', 'Produk berhasil dihapus!!!.');
    }

    public function productStock(Request $request)
    {
        $query = ProductStock::query()
            ->with('productVariant')
            ->latest();

        if ($request->filled('sku')) {
            $query->whereHas('productVariant', function ($q) use ($request) {
                $q->where('sku', 'like', '%' . $request->sku . '%');
            });
        }

        if ($request->filled('name_product')) {
            $query->whereHas('productVariant', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name_product . '%');
            });
        }
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        $warehouses = Warehouse::all();

        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;

        $productStocks = $query->paginate($perPage)->withQueryString();

        return view('admin.product.product-stocks', compact('productStocks', 'warehouses'));
    }
    public function exportProductStock(Request $request)
    {
        $query = ProductStock::query()
            ->with('productVariant')
            ->latest();

        if ($request->filled('sku')) {
            $query->whereHas('productVariant', function ($q) use ($request) {
                $q->where('sku', 'like', '%' . $request->sku . '%');
            });
        }

        if ($request->filled('name_product')) {
            $query->whereHas('productVariant', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name_product . '%');
            });
        }
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $rows = $query->get()->map(function ($ps) {
            return [
                'SKU' => $ps->productVariant->sku,
                'Nama Produk' => $ps->productVariant->name,
                'Gudang' => $ps->warehouse->name,
                'Stock' => $ps->stock,
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
                return ['SKU', 'Nama Produk', 'Gudang', 'Stock'];
            }
        };

        return Excel::download($export, 'product_stocks.xlsx');
    }
}
