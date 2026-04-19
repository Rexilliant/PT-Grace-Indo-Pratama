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
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        $q = ProductionBatch::query()->with([
            'personResponsible',
            'productStock.productVariant.product',
            'materials.rawMaterial',
            'warehouse',
            'deletedBy',
        ])->latest();

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

        $perPage = (int) ($request->get('per_page', 10));
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;

        $productionBatches = $q->paginate($perPage)->withQueryString();
        $warehouses = Warehouse::all();

        return view('admin.gudang-laporan-produksi', compact('productionBatches', 'warehouses'));
    }

    public function pilihProduk()
    {
        $products = ProductVariant::with('product')
            ->orderBy('name')
            ->get();

        return view('admin.add-pilih-produk', compact('products'));
    }

    public function create(ProductVariant $productVariant)
    {
        $productVariant->load('product');

        $personResponsible = Auth::user();
        $warehouses = Warehouse::all();

        return view('admin.add-produk', compact(
            'productVariant',
            'personResponsible',
            'warehouses'
        ));
    }

    public function edit($id)
    {
        try {
            $productionBatch = ProductionBatch::with([
                'materials.rawMaterial',
                'productStock.productVariant.product',
                'personResponsible',
                'warehouse',
                'deletedBy',
            ])->findOrFail($id);

            $productVariant = $productionBatch->productStock?->productVariant;

            if (!$productVariant) {
                throw new \Exception('Variant produk tidak ditemukan.');
            }

            $personResponsible = $productionBatch->personResponsible;
            $warehouses = Warehouse::orderBy('name')->get();

            return view('admin.edit-produk', compact(
                'productionBatch',
                'productVariant',
                'personResponsible',
                'warehouses'
            ));
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
                $batch = ProductionBatch::with([
                    'materials',
                    'productStock',
                ])->lockForUpdate()->findOrFail($id);

                $warehouseId = (int) $batch->warehouse_id;
                $productQty = (int) $batch->quantity;
                $userId = Auth::id();

                $productStock = ProductStock::lockForUpdate()->findOrFail($batch->product_stock_id);

                foreach ($batch->materials as $material) {
                    $rawStock = RawMaterialStock::where('raw_material_id', $material->raw_material_id)
                        ->where('warehouse_id', $warehouseId)
                        ->lockForUpdate()
                        ->first();

                    if ($rawStock) {
                        $rawStock->increment('stock', (int) $material->quantity_use);
                    }
                }

                if ($productStock->stock < $productQty) {
                    throw new \Exception('Stok produk jadi tidak mencukupi untuk menghapus data produksi.');
                }

                $productStock->decrement('stock', $productQty);

                ProductionHasMaterial::where('production_batch_id', $batch->id)->delete();

                RawMaterialStockMovement::where('ref_type', 'production_batches')
                    ->where('ref_id', $batch->id)
                    ->delete();

                ProductStockMovement::where('ref_type', 'production_batches')
                    ->where('ref_id', $batch->id)
                    ->delete();

                $batch->update([
                    'deleted_by' => $userId,
                ]);

                $batch->delete();
            });

            return redirect()
                ->route('admin.gudang-laporan-produksi')
                ->with('success', 'Data produksi berhasil dihapus.');
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
        try {
            $validated = $request->validate([
                'product_variant_id' => ['required', 'exists:product_variants,id'],
                'warehouse_id' => ['required', 'exists:warehouses,id'],
                'entry_date' => ['required', 'date'],
                'quantity' => ['required', 'integer', 'min:1'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.raw_material_id' => ['required', 'exists:raw_materials,id'],
                'items.*.quantity_use' => ['required', 'integer', 'min:0'],
                'note' => ['nullable', 'string'],
            ]);

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
                    ]
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

                    $rawStock = RawMaterialStock::with('rawMaterial')
                        ->where('raw_material_id', $rawMaterialId)
                        ->where('warehouse_id', $warehouseId)
                        ->lockForUpdate()
                        ->first();

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

            return redirect()
                ->route('admin.gudang-laporan-produksi')
                ->with('success', 'Data produksi berhasil disimpan.');
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
        try {
            $validated = $request->validate([
                'product_variant_id' => ['required', 'exists:product_variants,id'],
                'warehouse_id' => ['required', 'exists:warehouses,id'],
                'entry_date' => ['required', 'date'],
                'quantity' => ['required', 'integer', 'min:1'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.raw_material_id' => ['required', 'exists:raw_materials,id'],
                'items.*.quantity_use' => ['required', 'integer', 'min:0'],
                'note' => ['nullable', 'string'],
            ]);

            $userId = Auth::id();
            $warehouseId = (int) $validated['warehouse_id'];
            $productVariantId = (int) $validated['product_variant_id'];
            $productionQty = (int) $validated['quantity'];
            $entryDate = $validated['entry_date'];
            $note = $validated['note'] ?? null;

            $items = collect($validated['items'])
                ->map(fn($item) => [
                    'raw_material_id' => (int) $item['raw_material_id'],
                    'quantity_use' => (int) $item['quantity_use'],
                ])
                ->filter(fn($item) => $item['quantity_use'] > 0)
                ->values();

            if ($items->isEmpty()) {
                throw new \Exception('Minimal satu bahan baku harus digunakan.');
            }

            DB::transaction(function () use ($id, $userId, $warehouseId, $productVariantId, $productionQty, $entryDate, $note, $items) {
                $batch = ProductionBatch::with([
                    'materials',
                    'productStock',
                ])->lockForUpdate()->findOrFail($id);

                $oldWarehouseId = (int) $batch->warehouse_id;
                $oldProductStock = ProductStock::lockForUpdate()->findOrFail($batch->product_stock_id);
                $oldProductionQty = (int) $batch->quantity;

                foreach ($batch->materials as $oldMaterial) {
                    $oldRawStock = RawMaterialStock::where('raw_material_id', $oldMaterial->raw_material_id)
                        ->where('warehouse_id', $oldWarehouseId)
                        ->lockForUpdate()
                        ->first();

                    if ($oldRawStock) {
                        $oldRawStock->increment('stock', (int) $oldMaterial->quantity_use);
                    }
                }

                if ($oldProductStock->stock < $oldProductionQty) {
                    throw new \Exception('Stok produk jadi lama tidak mencukupi untuk proses update.');
                }

                $oldProductStock->decrement('stock', $oldProductionQty);

                ProductionHasMaterial::where('production_batch_id', $batch->id)->delete();

                RawMaterialStockMovement::where('ref_type', 'production_batches')
                    ->where('ref_id', $batch->id)
                    ->delete();

                ProductStockMovement::where('ref_type', 'production_batches')
                    ->where('ref_id', $batch->id)
                    ->delete();

                $productStock = ProductStock::firstOrCreate(
                    [
                        'product_variant_id' => $productVariantId,
                        'warehouse_id' => $warehouseId,
                    ],
                    [
                        'stock' => 0,
                    ]
                );

                foreach ($items as $item) {
                    $rawMaterialId = $item['raw_material_id'];
                    $quantityUse = $item['quantity_use'];

                    $rawStock = RawMaterialStock::with('rawMaterial')
                        ->where('raw_material_id', $rawMaterialId)
                        ->where('warehouse_id', $warehouseId)
                        ->lockForUpdate()
                        ->first();

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

            return redirect()
                ->route('admin.gudang-laporan-produksi')
                ->with('success', 'Data produksi berhasil diperbarui.');
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