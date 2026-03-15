<?php

namespace App\Http\Controllers;

use App\Models\ProductStock;
use App\Models\ProductVariant;
use App\Models\ProductionBatch;
use App\Models\ProductionHasMaterial;
use App\Models\RawMaterialStock;
use App\Models\RawMaterialStockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use App\Models\Product;

class ProductionController extends Controller
{
    // public function index(Request $request)
    // {
    //     $search = trim((string) $request->get('search'));

    //     $productionBatches = ProductionBatch::with([
    //         'personResponsible',
    //         'productStock.productVariant',
    //         'materials.rawMaterial',
    //     ])
    //         ->when($search, function ($query) use ($search) {
    //             $query->where(function ($q) use ($search) {
    //                 $q->where('province', 'like', "%{$search}%")
    //                     ->orWhereDate('entry_date', $search)
    //                     ->orWhereHas('personResponsible', function ($userQuery) use ($search) {
    //                         $userQuery->where('name', 'like', "%{$search}%");
    //                     })
    //                     ->orWhereHas('productStock.productVariant', function ($variantQuery) use ($search) {
    //                         $variantQuery->where('sku', 'like', "%{$search}%")
    //                             ->orWhere('name', 'like', "%{$search}%");
    //                     });
    //             });
    //         })
    //         ->orderByDesc('entry_date')
    //         ->orderByDesc('id')
    //         ->paginate(10)
    //         ->withQueryString();

    //     return view('admin.gudang-laporan-produksi', compact(
    //         'productionBatches',
    //         'search'
    //     ));
    // }


    public function index()
    {
        $productionBatches = ProductionBatch::with([
            'personResponsible',
            'productStock.productVariant.product',
            'materials.rawMaterial',
        ])
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->paginate(5);

        return view('admin.gudang-laporan-produksi', compact('productionBatches'));
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
        $provinces = $this->getProvinceOptions();

        return view('admin.add-produk', compact(
            'productVariant',
            'personResponsible',
            'provinces'
        ));
    }

    public function edit(ProductionBatch $productionBatch)
    {
        $productionBatch->load([
            'personResponsible',
            'productStock.productVariant.product',
            'materials.rawMaterial',
        ]);

        $personResponsible = $productionBatch->personResponsible;
        $productVariant = $productionBatch->productStock?->productVariant;
        $provinces = $this->getProvinceOptions();

        if (!$productVariant) {
            return redirect()
                ->route('admin.gudang-laporan-produksi')
                ->with('error', 'Produk pada batch produksi tidak ditemukan.');
        }

        $usedMaterials = $productionBatch->materials->keyBy('raw_material_id');

        $materials = RawMaterialStock::with('rawMaterial')
            ->where('province', $productionBatch->province)
            ->whereHas('rawMaterial', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->orderBy('raw_material_id')
            ->get()
            ->map(function ($stock) use ($usedMaterials) {
                $used = $usedMaterials->get($stock->raw_material_id);

                return [
                    'raw_material_id' => $stock->raw_material_id,
                    'id_barang' => $stock->rawMaterial->code ?? '-',
                    'nama_barang' => $stock->rawMaterial->name ?? '-',
                    'stok_tersedia' => $stock->stock,
                    'unit' => $stock->rawMaterial->unit ?? '',
                    'quantity_use' => $used?->quantity_use,
                ];
            })
            ->values();

        return view('admin.edit-produk', compact(
            'productionBatch',
            'productVariant',
            'personResponsible',
            'provinces',
            'materials'
        ));
    }

    public function destroy(ProductionBatch $productionBatch)
    {
        try {
            DB::transaction(function () use ($productionBatch) {
                $productionBatch->load(['materials', 'productStock']);

                $province = trim($productionBatch->province);
                $quantity = (int) $productionBatch->quantity;

                $productStock = ProductStock::lockForUpdate()->find($productionBatch->product_stock_id);

                if (!$productStock) {
                    throw new \Exception('Stok produk tidak ditemukan.');
                }

                if ($productStock->stock < $quantity) {
                    throw new \Exception('Stok produk jadi tidak cukup untuk rollback penghapusan.');
                }

                $productStock->decrement('stock', $quantity);

                foreach ($productionBatch->materials as $material) {
                    $rawStock = RawMaterialStock::where('raw_material_id', $material->raw_material_id)
                        ->where('province', $province)
                        ->lockForUpdate()
                        ->first();

                    if (!$rawStock) {
                        throw new \Exception("Stok bahan baku tidak ditemukan untuk provinsi {$province}.");
                    }

                    $rawStock->increment('stock', (int) $material->quantity_use);
                }

                ProductionHasMaterial::where('production_batch_id', $productionBatch->id)->delete();

                RawMaterialStockMovement::where('ref_type', 'production_batches')
                    ->where('ref_id', $productionBatch->id)
                    ->delete();

                $productionBatch->delete();
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

    public function getMaterialsByProvince(Request $request)
    {
        $request->validate([
            'province' => ['required', 'string'],
        ]);

        $province = trim($request->province);

        $materials = RawMaterialStock::with('rawMaterial')
            ->where('province', $province)
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
        $validated = $request->validate([
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'province' => ['required', 'string', 'max:255'],
            'entry_date' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.raw_material_id' => ['required', 'exists:raw_materials,id'],
            'items.*.quantity_use' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        $user = Auth::user();

        try {
            DB::transaction(function () use ($validated, $user) {
                $province = trim($validated['province']);
                $productVariantId = (int) $validated['product_variant_id'];
                $quantity = (int) $validated['quantity'];

                /**
                 * Ambil / buat stok produk jadi per product_variant + province
                 * quantity di production_batches = source of truth transaksi
                 * stock di product_stocks = hasil akumulasi
                 */
                $productStock = ProductStock::firstOrCreate(
                    [
                        'product_variant_id' => $productVariantId,
                        'province' => $province,
                    ],
                    [
                        'stock' => 0,
                    ]
                );

                $batch = ProductionBatch::create([
                    'person_responsible_id' => $user->id,
                    'product_stock_id' => $productStock->id,
                    'province' => $province,
                    'entry_date' => $validated['entry_date'],
                    'quantity' => $quantity,
                    'note' => $validated['note'] ?? null,
                    'status' => 'completed',
                ]);

                foreach ($validated['items'] as $item) {
                    $rawMaterialId = (int) $item['raw_material_id'];
                    $quantityUse = (int) $item['quantity_use'];

                    $rawStock = RawMaterialStock::with('rawMaterial')
                        ->where('raw_material_id', $rawMaterialId)
                        ->where('province', $province)
                        ->lockForUpdate()
                        ->first();

                    if ($quantityUse === 0) {
                        continue;
                    }

                    if (!$rawStock) {
                        throw new \Exception("Stok bahan baku tidak ditemukan untuk provinsi {$province}.");
                    }

                    if ($rawStock->stock < $quantityUse) {
                        $materialName = $rawStock->rawMaterial->name ?? 'Unknown Material';
                        throw new \Exception("Stok bahan baku {$materialName} tidak mencukupi.");
                    }

                    /**
                     * Simpan snapshot bahan yang dipakai di transaksi produksi
                     * stock = stok saat itu sebelum dikurangi
                     */
                    ProductionHasMaterial::create([
                        'production_batch_id' => $batch->id,
                        'raw_material_id' => $rawMaterialId,
                        'stock' => $rawStock->stock,
                        'quantity_use' => $quantityUse,
                    ]);

                    /**
                     * Kurangi stok bahan baku
                     */
                    $rawStock->decrement('stock', $quantityUse);

                    /**
                     * Catat movement OUT bahan baku
                     */
                    RawMaterialStockMovement::create([
                        'province' => $province,
                        'raw_material_id' => $rawMaterialId,
                        'type' => 'out',
                        'stock' => $quantityUse,
                        'ref_type' => 'production_batches',
                        'ref_id' => $batch->id,
                        'responsible_id' => $user->id,
                        'note' => 'Pemakaian bahan baku untuk produksi',
                    ]);
                }

                /**
                 * Tambahkan stok produk jadi
                 */
                $productStock->increment('stock', $quantity);
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

    public function update(Request $request, ProductionBatch $productionBatch)
    {
        $validated = $request->validate([
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'province' => ['required', 'string', 'max:255'],
            'entry_date' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.raw_material_id' => ['required', 'exists:raw_materials,id'],
            'items.*.quantity_use' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        $user = Auth::user();

        try {
            DB::transaction(function () use ($validated, $user, $productionBatch) {
                $productionBatch->load([
                    'productStock',
                    'materials',
                ]);

                $oldProvince = trim($productionBatch->province);
                $oldQuantity = (int) $productionBatch->quantity;
                $oldProductStock = ProductStock::lockForUpdate()->find($productionBatch->product_stock_id);

                if (!$oldProductStock) {
                    throw new \Exception('Stok produk lama tidak ditemukan.');
                }

                /**
                 * Rollback efek batch lama
                 */

                if ($oldProductStock->stock < $oldQuantity) {
                    throw new \Exception('Stok produk jadi tidak cukup untuk rollback batch lama.');
                }

                $oldProductStock->decrement('stock', $oldQuantity);

                foreach ($productionBatch->materials as $oldMaterial) {
                    $oldRawStock = RawMaterialStock::where('raw_material_id', $oldMaterial->raw_material_id)
                        ->where('province', $oldProvince)
                        ->lockForUpdate()
                        ->first();

                    if (!$oldRawStock) {
                        throw new \Exception("Stok bahan baku lama tidak ditemukan untuk provinsi {$oldProvince}.");
                    }

                    $oldRawStock->increment('stock', (int) $oldMaterial->quantity_use);
                }

                ProductionHasMaterial::where('production_batch_id', $productionBatch->id)->delete();

                RawMaterialStockMovement::where('ref_type', 'production_batches')
                    ->where('ref_id', $productionBatch->id)
                    ->delete();

                /**
                 * Terapkan data baru
                 */
                $newProvince = trim($validated['province']);
                $newProductVariantId = (int) $validated['product_variant_id'];
                $newQuantity = (int) $validated['quantity'];

                $newProductStock = ProductStock::firstOrCreate(
                    [
                        'product_variant_id' => $newProductVariantId,
                        'province' => $newProvince,
                    ],
                    [
                        'stock' => 0,
                    ]
                );

                $productionBatch->update([
                    'person_responsible_id' => $user->id,
                    'product_stock_id' => $newProductStock->id,
                    'province' => $newProvince,
                    'entry_date' => $validated['entry_date'],
                    'quantity' => $newQuantity,
                    'note' => $validated['note'] ?? null,
                    'status' => 'completed',
                ]);

                foreach ($validated['items'] as $item) {
                    $rawMaterialId = (int) $item['raw_material_id'];
                    $quantityUse = (int) $item['quantity_use'];

                    $rawStock = RawMaterialStock::with('rawMaterial')
                        ->where('raw_material_id', $rawMaterialId)
                        ->where('province', $newProvince)
                        ->lockForUpdate()
                        ->first();

                    if ($quantityUse === 0) {
                        continue;
                    }

                    if (!$rawStock) {
                        throw new \Exception("Stok bahan baku tidak ditemukan untuk provinsi {$newProvince}.");
                    }

                    if ($rawStock->stock < $quantityUse) {
                        $materialName = $rawStock->rawMaterial->name ?? 'Unknown Material';
                        throw new \Exception("Stok bahan baku {$materialName} tidak mencukupi.");
                    }

                    ProductionHasMaterial::create([
                        'production_batch_id' => $productionBatch->id,
                        'raw_material_id' => $rawMaterialId,
                        'stock' => $rawStock->stock,
                        'quantity_use' => $quantityUse,
                    ]);

                    $rawStock->decrement('stock', $quantityUse);

                    RawMaterialStockMovement::create([
                        'province' => $newProvince,
                        'raw_material_id' => $rawMaterialId,
                        'type' => 'out',
                        'stock' => $quantityUse,
                        'ref_type' => 'production_batches',
                        'ref_id' => $productionBatch->id,
                        'responsible_id' => $user->id,
                        'note' => 'Pemakaian bahan baku untuk produksi',
                    ]);
                }

                $newProductStock->increment('stock', $newQuantity);
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