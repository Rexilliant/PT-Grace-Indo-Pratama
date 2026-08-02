<?php

namespace App\Http\Controllers;

use App\Models\ProductionBatch;
use App\Models\ProductionHasMaterial;
use App\Models\ProductStock;
use App\Models\ProductStockMovement;
use App\Models\ProductVariant;
use App\Models\RawMaterialStock;
use App\Models\RawMaterialStockMovement;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Facades\Excel;

class ProductionController extends Controller
{
    public function export(Request $request)
    {
        $q = ProductionBatch::query()
            ->with(['personResponsible', 'warehouse', 'productStock.productVariant.product', 'materials.rawMaterial'])
            ->orderBy('entry_date', 'desc');

        if ($request->filled('id')) {
            $q->where('id', 'like', '%' . $request->id . '%');
        }

        if ($request->filled('warehouse_id')) {
            $q->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('date_from')) {
            $q->whereDate('entry_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $q->whereDate('entry_date', '<=', $request->date_to);
        }

        $rows = collect();
        $mergeRanges = [];
        $currentRow = 2;

        $q->get()->each(function ($p) use ($rows, &$mergeRanges, &$currentRow) {
            $startRow = $currentRow;

            $variant = $p->productStock?->productVariant;
            $product = $variant?->product;

            foreach ($p->materials as $material) {
                $rawMaterial = $material->rawMaterial;

                $rows->push([
                    'Id Produksi' => $p->id,
                    'Tanggal Produksi' => $p->entry_date ? Carbon::parse($p->entry_date)->format('d/m/Y') : '-',
                    'Nama Penanggung Jawab' => $p->personResponsible->name ?? '-',
                    'Gudang' => $p->warehouse->name ?? '-',
                    'ID Barang Jadi' => $product->code ?? '-',
                    'SKU' => $variant->sku ?? '-',
                    'Produk' => $product->name ?? '-',
                    'Variant' => $variant->name ?? '-',
                    'Jumlah Produksi' => $p->quantity ?? 0,
                    'Catatan' => $p->note ?? '-',

                    'ID Bahan Baku' => $rawMaterial->code ?? '-',
                    'Nama Bahan Baku' => $rawMaterial->name ?? '-',
                    'Stok Sebelum Dipakai' => $material->stock ?? 0,
                    'Stok Digunakan' => $material->quantity_use ?? 0,
                    'Satuan' => $rawMaterial->unit ?? '-',
                ]);

                $currentRow++;
            }

            $endRow = $currentRow - 1;

            if ($endRow > $startRow) {
                $mergeRanges[] = [
                    'start' => $startRow,
                    'end' => $endRow,
                ];
            }
        });

        $export = new class ($rows, $mergeRanges) implements FromCollection, WithEvents, WithHeadings {
            public function __construct(private $rows, private $mergeRanges) {}

            public function collection()
            {
                return $this->rows;
            }

            public function headings(): array
            {
                return ['Id Produksi', 'Tanggal Produksi', 'Nama Penanggung Jawab', 'Gudang', 'ID Barang Jadi', 'SKU', 'Produk', 'Variant', 'Jumlah Produksi', 'Catatan', 'ID Bahan Baku', 'Nama Bahan Baku', 'Stok Sebelum Dipakai', 'Stok Digunakan', 'Satuan'];
            }

            public function registerEvents(): array
            {
                return [
                    AfterSheet::class => function (AfterSheet $event) {
                        foreach ($this->mergeRanges as $range) {
                            foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'] as $column) {
                                $event->sheet->mergeCells($column . $range['start'] . ':' . $column . $range['end']);
                            }
                        }
                    },
                ];
            }
        };

        return Excel::download($export, 'produksi_' . now()->format('YmdHis') . '.xlsx');
    }

    public function index(Request $request)
    {
        $q = ProductionBatch::query()
            ->with(['personResponsible', 'productStock.productVariant.product', 'materials.rawMaterial', 'warehouse', 'deletedBy'])
            ->latest();
        $warehouseId = auth()->user()->employee?->warehouse_id;
        // Jika user punya warehouse_id, procurement hanya gudang itu
        if ($warehouseId) {
            $q->where('warehouse_id', $warehouseId);
        }
        // Filter warehouse_id dari request hanya berlaku kalau user tidak punya gudang
        if (!$warehouseId && $request->filled('warehouse_id')) {
            $q->where('warehouse_id', $request->warehouse_id);
        }
        if ($request->filled('id')) {
            $q->where('id', 'like', '%' . $request->id . '%');
        }

        if ($request->filled('date_from')) {
            $q->whereDate('entry_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $q->whereDate('entry_date', '<=', $request->date_to);
        }

        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;

        $productionBatches = $q->paginate($perPage)->withQueryString();
        // Jika user punya warehouse_id, dropdown gudang hanya gudang itu
        if ($warehouseId) {
            $warehouses = Warehouse::where('id', $warehouseId)->get();
        } else {
            $warehouses = Warehouse::all();
        }

        return view('admin.production_report.gudang-laporan-produksi', compact('productionBatches', 'warehouses'));
    }

    public function print($id)
    {
        try {
            $productionBatch = ProductionBatch::with(['materials.rawMaterial', 'productStock.productVariant.product', 'personResponsible', 'warehouse'])->findOrFail($id);

            return view('admin.production_report.print-produksi', compact('productionBatch'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal memuat dokumen cetak.');
        }
    }

    public function pilihProduk()
    {
        $products = ProductVariant::with('product')->orderBy('name')->get();

        return view('admin.production_report.add-pilih-produk', compact('products'));
    }

    public function create(ProductVariant $productVariant)
    {
        $productVariant->load('product');
        $personResponsible = Auth::user();
        $warehouseId = optional($personResponsible->employee)->warehouse_id;
        $warehouses = Warehouse::where('type', 'produksi')
            ->when($warehouseId, function ($query) use ($warehouseId) {
                $query->where('id', $warehouseId);
            })
            ->get();

        return view('admin.production_report.add-produk', compact('productVariant', 'personResponsible', 'warehouses'));
    }

    public function edit($id)
    {
        try {
            $productionBatch = ProductionBatch::with(['materials.rawMaterial', 'productStock.productVariant.product', 'personResponsible', 'warehouse', 'deletedBy'])->findOrFail($id);

            $productVariant = $productionBatch->productStock?->productVariant;

            if (!$productVariant) {
                throw new \Exception('Variant produk tidak ditemukan.');
            }

            $personResponsible = $productionBatch->personResponsible;
            $warehouseId = optional($personResponsible->employee)->warehouse_id;
            $warehouses = Warehouse::where('type', 'produksi')
                ->when($warehouseId, function ($query) use ($warehouseId) {
                    $query->where('id', $warehouseId);
                })
                ->get();

            return view('admin.production_report.edit-produk', compact('productionBatch', 'productVariant', 'personResponsible', 'warehouses'));
        } catch (\Throwable $th) {
            if (function_exists('save_log_error')) {
                save_log_error($th);
            }

            return redirect()
                ->route('admin.gudang-laporan-produksi')
                ->with('error', $th->getMessage() ?: 'Gagal memuat halaman edit produksi.');
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $batch = ProductionBatch::with(['materials', 'productStock'])
                    ->lockForUpdate()
                    ->findOrFail($id);

                $warehouseId = (int) $batch->warehouse_id;
                $productQty = (int) $batch->quantity;
                $userId = Auth::id();

                $productStock = ProductStock::lockForUpdate()->findOrFail($batch->product_stock_id);

                foreach ($batch->materials as $material) {
                    $rawStock = RawMaterialStock::where('raw_material_id', $material->raw_material_id)->where('warehouse_id', $warehouseId)->lockForUpdate()->first();

                    if ($rawStock) {
                        $rawStock->increment('stock', (int) $material->quantity_use);
                    }
                }

                if ($productStock->stock < $productQty) {
                    throw new \Exception('Stok produk jadi tidak mencukupi untuk menghapus data produksi.');
                }

                $productStock->decrement('stock', $productQty);

                ProductionHasMaterial::where('production_batch_id', $batch->id)->delete();

                RawMaterialStockMovement::where('ref_type', 'production_batches')->where('ref_id', $batch->id)->delete();

                ProductStockMovement::where('ref_type', 'production_batches')->where('ref_id', $batch->id)->delete();

                $batch->update([
                    'deleted_by' => $userId,
                ]);

                $batch->delete();
            });

            return redirect()->route('admin.gudang-laporan-produksi')->with('success', 'Data produksi berhasil dihapus.');
        } catch (\Throwable $th) {
            if (function_exists('save_log_error')) {
                save_log_error($th);
            }

            return redirect()
                ->back()
                ->with('error', $th->getMessage() ?: 'Terjadi kesalahan saat menghapus data produksi.');
        }
    }

    public function getMaterialsByWarehouse(Request $request)
    {
        $request->validate([
            'warehouse_id' => ['required'],
        ]);

        $warehouse = $request->warehouse_id;

        $materials = RawMaterialStock::with('rawMaterial')
            ->where('warehouse_id', $warehouse)
            ->whereHas('rawMaterial', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->orderBy('raw_material_id')
            ->get()
            ->map(function ($stock) {
                return [
                    'raw_material_id' => $stock->raw_material_id,
                    'id_barang' => $stock->rawMaterial->code ?? '-',
                    'nama_barang' => $stock->rawMaterial->name ?? '-',
                    'stok_tersedia' => $stock->stock,
                    'unit' => $stock->rawMaterial->unit ?? '',
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'materials' => $materials,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'product_variant_id' => ['required', 'exists:product_variants,id'],
                'warehouse_id' => ['required', 'exists:warehouses,id'],
                'entry_date' => ['required', 'date'],
                'quantity' => ['required', 'integer', 'min:1'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.raw_material_id' => ['required', 'exists:raw_materials,id'],
                'items.*.quantity_use' => ['required', 'integer', 'min:1'], // ganti min:0 -> min:1 (lihat catatan di bawah)
                'note' => ['nullable', 'string'],
            ],
            [
                'warehouse_id.required' => 'Silakan pilih gudang.',
                'entry_date.required' => 'Tanggal produksi wajib diisi.',
                'quantity.required' => 'Jumlah produksi wajib diisi.',
                'quantity.min' => 'Jumlah produksi minimal 1.',

                'items.required' => 'Minimal harus ada 1 bahan baku.',
                'items.min' => 'Minimal harus ada 1 bahan baku.',

                'items.*.raw_material_id.required' => 'Bahan baku tidak valid.',
                'items.*.raw_material_id.exists' => 'Bahan baku tidak valid.',

                'items.*.quantity_use.required' => 'Stok digunakan wajib diisi.',
                'items.*.quantity_use.integer' => 'Stok digunakan harus berupa angka.',
                'items.*.quantity_use.min' => 'Stok digunakan minimal 1.',
            ],
        );
        try {
            $userId = Auth::id();
            $warehouseId = (int) $validated['warehouse_id'];
            $productVariantId = (int) $validated['product_variant_id'];
            $productionQty = (int) $validated['quantity'];
            $entryDate = $validated['entry_date'];
            $note = $validated['note'] ?? null;

            $items = collect($validated['items'])
                ->map(function ($item) {
                    return [
                        'raw_material_id' => (int) $item['raw_material_id'],
                        'quantity_use' => (int) $item['quantity_use'],
                    ];
                })
                ->filter(fn($item) => $item['quantity_use'] > 0)
                ->values();

            if ($items->isEmpty()) {
                throw new \Exception('Minimal satu bahan baku harus digunakan.');
            }

            DB::transaction(function () use ($userId, $warehouseId, $productVariantId, $productionQty, $entryDate, $note, $items) {
                $productStock = ProductStock::firstOrCreate(
                    [
                        'product_variant_id' => $productVariantId,
                        'warehouse_id' => $warehouseId,
                    ],
                    [
                        'stock' => 0,
                    ],
                );

                $batch = ProductionBatch::create([
                    'person_responsible_id' => $userId,
                    'product_stock_id' => $productStock->id,
                    'warehouse_id' => $warehouseId,
                    'entry_date' => $entryDate,
                    'quantity' => $productionQty,
                    'note' => $note,
                    'status' => 'completed',
                ]);

                foreach ($items as $item) {
                    $rawMaterialId = $item['raw_material_id'];
                    $quantityUse = $item['quantity_use'];

                    $rawStock = RawMaterialStock::with('rawMaterial')->where('raw_material_id', $rawMaterialId)->where('warehouse_id', $warehouseId)->lockForUpdate()->first();

                    if (!$rawStock) {
                        throw new \Exception("Stok bahan baku tidak ditemukan untuk gudang ID {$warehouseId}.");
                    }

                    if ($rawStock->stock < $quantityUse) {
                        $materialName = $rawStock->rawMaterial->name ?? 'Unknown Material';
                        throw new \Exception("Stok bahan baku {$materialName} tidak mencukupi.");
                    }

                    ProductionHasMaterial::create([
                        'production_batch_id' => $batch->id,
                        'raw_material_id' => $rawMaterialId,
                        'stock' => $rawStock->stock,
                        'quantity_use' => $quantityUse,
                    ]);

                    $rawStock->decrement('stock', $quantityUse);

                    RawMaterialStockMovement::create([
                        'warehouse_id' => $warehouseId,
                        'raw_material_id' => $rawMaterialId,
                        'type' => 'out',
                        'stock' => $quantityUse,
                        'ref_type' => 'production_batches',
                        'ref_id' => $batch->id,
                        'responsible_id' => $userId,
                        'note' => 'Pemakaian bahan baku untuk produksi',
                    ]);
                }

                $productStock->increment('stock', $productionQty);

                ProductStockMovement::create([
                    'warehouse_id' => $warehouseId,
                    'product_stock_id' => $productStock->id,
                    'type' => 'in',
                    'quantity' => $productionQty,
                    'ref_type' => 'production_batches',
                    'ref_id' => $batch->id,
                    'note' => 'Hasil tambah produksi',
                ]);
            });

            return redirect()->route('admin.gudang-laporan-produksi')->with('success', 'Data produksi berhasil disimpan.');
        } catch (\Throwable $th) {
            if (function_exists('save_log_error')) {
                save_log_error($th);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $th->getMessage() ?: 'Terjadi kesalahan saat menyimpan data produksi.');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate(
            [
                'product_variant_id' => ['required', 'exists:product_variants,id'],
                'warehouse_id' => ['required', 'exists:warehouses,id'],
                'entry_date' => ['required', 'date'],
                'quantity' => ['required', 'integer', 'min:1'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.raw_material_id' => ['required', 'exists:raw_materials,id'],
                'items.*.quantity_use' => ['required', 'integer', 'min:1'],
                'note' => ['nullable', 'string'],
            ],
            [
                'warehouse_id.required' => 'Silakan pilih gudang.',
                'entry_date.required' => 'Tanggal produksi wajib diisi.',
                'quantity.required' => 'Jumlah produksi wajib diisi.',
                'quantity.min' => 'Jumlah produksi minimal 1.',

                'items.required' => 'Minimal harus ada 1 bahan baku.',
                'items.min' => 'Minimal harus ada 1 bahan baku.',

                'items.*.raw_material_id.required' => 'Bahan baku tidak valid.',
                'items.*.raw_material_id.exists' => 'Bahan baku tidak valid.',

                'items.*.quantity_use.required' => 'Stok digunakan wajib diisi.',
                'items.*.quantity_use.integer' => 'Stok digunakan harus berupa angka.',
                'items.*.quantity_use.min' => 'Stok digunakan minimal 1.',
            ],
        );
        try {
            $userId = Auth::id();
            $warehouseId = (int) $validated['warehouse_id'];
            $productVariantId = (int) $validated['product_variant_id'];
            $productionQty = (int) $validated['quantity'];
            $entryDate = $validated['entry_date'];
            $note = $validated['note'] ?? null;

            $items = collect($validated['items'])
                ->map(
                    fn($item) => [
                        'raw_material_id' => (int) $item['raw_material_id'],
                        'quantity_use' => (int) $item['quantity_use'],
                    ],
                )
                ->filter(fn($item) => $item['quantity_use'] > 0)
                ->values();

            if ($items->isEmpty()) {
                throw new \Exception('Minimal satu bahan baku harus digunakan.');
            }

            DB::transaction(function () use ($id, $userId, $warehouseId, $productVariantId, $productionQty, $entryDate, $note, $items) {
                $batch = ProductionBatch::with(['materials', 'productStock'])
                    ->lockForUpdate()
                    ->findOrFail($id);

                $oldWarehouseId = (int) $batch->warehouse_id;
                $oldProductStock = ProductStock::lockForUpdate()->findOrFail($batch->product_stock_id);
                $oldProductionQty = (int) $batch->quantity;

                foreach ($batch->materials as $oldMaterial) {
                    $oldRawStock = RawMaterialStock::where('raw_material_id', $oldMaterial->raw_material_id)->where('warehouse_id', $oldWarehouseId)->lockForUpdate()->first();

                    if ($oldRawStock) {
                        $oldRawStock->increment('stock', (int) $oldMaterial->quantity_use);
                    }
                }

                if ($oldProductStock->stock < $oldProductionQty) {
                    throw new \Exception('Stok produk jadi lama tidak mencukupi untuk proses update.');
                }

                $oldProductStock->decrement('stock', $oldProductionQty);

                ProductionHasMaterial::where('production_batch_id', $batch->id)->delete();

                RawMaterialStockMovement::where('ref_type', 'production_batches')->where('ref_id', $batch->id)->delete();

                ProductStockMovement::where('ref_type', 'production_batches')->where('ref_id', $batch->id)->delete();

                $productStock = ProductStock::firstOrCreate(
                    [
                        'product_variant_id' => $productVariantId,
                        'warehouse_id' => $warehouseId,
                    ],
                    [
                        'stock' => 0,
                    ],
                );

                foreach ($items as $item) {
                    $rawMaterialId = $item['raw_material_id'];
                    $quantityUse = $item['quantity_use'];

                    $rawStock = RawMaterialStock::with('rawMaterial')->where('raw_material_id', $rawMaterialId)->where('warehouse_id', $warehouseId)->lockForUpdate()->first();

                    if (!$rawStock) {
                        throw new \Exception("Stok bahan baku tidak ditemukan untuk gudang ID {$warehouseId}.");
                    }

                    if ($rawStock->stock < $quantityUse) {
                        $materialName = $rawStock->rawMaterial->name ?? 'Unknown Material';
                        throw new \Exception("Stok bahan baku {$materialName} tidak mencukupi.");
                    }

                    ProductionHasMaterial::create([
                        'production_batch_id' => $batch->id,
                        'raw_material_id' => $rawMaterialId,
                        'stock' => $rawStock->stock,
                        'quantity_use' => $quantityUse,
                    ]);

                    $rawStock->decrement('stock', $quantityUse);

                    RawMaterialStockMovement::create([
                        'warehouse_id' => $warehouseId,
                        'raw_material_id' => $rawMaterialId,
                        'type' => 'out',
                        'stock' => $quantityUse,
                        'ref_type' => 'production_batches',
                        'ref_id' => $batch->id,
                        'responsible_id' => $userId,
                        'note' => 'Pemakaian bahan baku untuk produksi',
                    ]);
                }

                $batch->update([
                    'product_stock_id' => $productStock->id,
                    'warehouse_id' => $warehouseId,
                    'entry_date' => $entryDate,
                    'quantity' => $productionQty,
                    'note' => $note,
                ]);

                $productStock->increment('stock', $productionQty);

                ProductStockMovement::create([
                    'warehouse_id' => $warehouseId,
                    'product_stock_id' => $productStock->id,
                    'type' => 'in',
                    'quantity' => $productionQty,
                    'ref_type' => 'production_batches',
                    'ref_id' => $batch->id,
                    'note' => 'Hasil produksi barang jadi',
                ]);
            });

            return redirect()->route('admin.gudang-laporan-produksi')->with('success', 'Data produksi berhasil diperbarui.');
        } catch (\Throwable $th) {
            if (function_exists('save_log_error')) {
                save_log_error($th);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $th->getMessage() ?: 'Terjadi kesalahan saat memperbarui data produksi.');
        }
    }

    /**
     * Ambil daftar provinsi Indonesia dari GeoNames
     */
    private function getProvinceOptions()
    {
        try {
            /** @var Response $response */
            $response = Http::timeout(15)->get('http://api.geonames.org/childrenJSON', [
                'geonameId' => 1643084,
                'username' => 'hier',
            ]);

            $geonames = $response->json('geonames');

            return collect(is_array($geonames) ? $geonames : [])
                ->map(function ($province) {
                    return [
                        'id' => $province['geonameId'] ?? null,
                        'name' => $province['name'] ?? null,
                    ];
                })
                ->filter(function ($province) {
                    return !empty($province['name']);
                })
                ->values();
        } catch (\Throwable $th) {
            if (function_exists('save_log_error')) {
                save_log_error($th);
            }

            return collect([]);
        }
    }
}
