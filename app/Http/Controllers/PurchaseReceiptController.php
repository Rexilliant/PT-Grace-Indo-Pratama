<?php

namespace App\Http\Controllers;

use App\Models\Procurement;
use App\Models\PurchaseReceipt;
use App\Models\PurchaseReceiptItem;
use App\Models\RawMaterial;
use App\Models\RawMaterialStock;
use App\Models\RawMaterialStockMovement;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class PurchaseReceiptController extends Controller
{
    public function index(Request $request)
    {
        $q = PurchaseReceipt::query()->with('receivedBy')
            ->orderBy('created_at', 'desc');
        // FILTER TANGGAL (purchase_at)
        if ($request->filled('date_from')) {
            $q->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $q->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('name')) {
            $search = $request->name;
            $q->whereHas('receivedBy', function ($u) use ($search) {
                $u->where('name', 'like', "%{$search}%");
            });
        }
        // FILTER PROVINCE
        if ($request->filled('province')) {
            $q->where('province', 'like', "%{$request->province}%");
        }
        // ROWS PER PAGE (dropdown 10/25/50)
        $perPage = (int) ($request->get('per_page', 10));
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;
        $receipts = $q->paginate($perPage)->withQueryString();

        return view('admin.purchase.purchases', compact('receipts'));
    }

    public function create()
    {

        // $response = Http::get('http://api.geonames.org/childrenJSON', [
        //     'geonameId' => 1643084,
        //     'username' => 'hier',
        // ]);
        $path = public_path('assets/data/provinceAndCity.json');

        $json = File::get($path);

        $data = json_decode($json, true);

        $provinces = collect($data['geonames'] ?? [])
            ->map(fn ($p) => [
                'id' => $p['geonameId'] ?? null,
                'name' => $p['name'] ?? null,
            ])
            ->filter(fn ($p) => ! empty($p['name']))
            ->values();
        $rawMaterials = RawMaterial::select('id', 'code', 'name', 'unit')
            ->orderBy('name')
            ->get();
        $procurements = Procurement::where('status', 'diterima')->get();

        return view('admin.purchase.purchase-create', compact('provinces', 'rawMaterials', 'procurements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'procurement_id' => ['required', 'integer', 'exists:procurements,id'],
            'province' => ['required'],

            'received_at' => ['required', 'date'],
            'total_price' => ['nullable', 'integer', 'min:0'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.raw_material_id' => ['required', 'integer', 'exists:raw_materials,id'],
            'items.*.quantity_received' => ['required', 'integer', 'min:1'],

            'invoices' => ['required', 'array'],
            'invoices.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'], // 3MB
        ]);
        $rawIds = collect($validated['items'])->pluck('raw_material_id')->unique()->values();
        $count = RawMaterial::whereIn('id', $rawIds)->count();
        if ($count !== $rawIds->count()) {
            return back()->withInput()->withErrors([
                'items' => 'Ada item raw material yang tidak valid.',
            ]);
        }
        try {
            $receipt = DB::transaction(function () use ($request, $validated) {
                $userId = auth()->id();

                // Receipt number (silakan sesuaikan format)
                $receiptNumber = 'RCPT-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));

                $receipt = PurchaseReceipt::create([
                    'receipt_number' => $receiptNumber,
                    'procurement_id' => (int) $validated['procurement_id'],
                    'province' => (string) $validated['province'], // simpan apa adanya dari form
                    'received_at' => $validated['received_at'],
                    'received_by' => $userId,
                    'status' => 'received',
                    'note' => null,
                    'total_price' => (int) ($validated['total_price'] ?? 0),
                ]);

                // Simpan items
                foreach ($validated['items'] as $row) {
                    PurchaseReceiptItem::create([
                        'purchase_receipt_id' => $receipt->id,
                        'raw_material_id' => (int) $row['raw_material_id'],
                        'quantity_received' => (int) $row['quantity_received'],
                    ]);
                    RawMaterialStockMovement::create([
                        'province' => $receipt->province,
                        'raw_material_id' => (int) $row['raw_material_id'],
                        'type' => 'in',
                        'stock' => (int) $row['quantity_received'],
                        'ref_type' => PurchaseReceipt::class,
                        'ref_id' => $receipt->id,
                        'responsible_id' => $userId,
                        'note' => "Penerimaan dari procurement ID {$receipt->procurement_id}",
                    ]);
                    RawMaterialStock::updateOrCreate(
                        [
                            'raw_material_id' => (int) $row['raw_material_id'],
                            'province' => $receipt->province,
                        ],
                        []
                    );

                    RawMaterialStock::where([
                        'raw_material_id' => (int) $row['raw_material_id'],
                        'province' => $receipt->province,
                    ])->increment('stock', (int) $row['quantity_received']);
                }
                if ($request->hasFile('invoices')) {
                    foreach ($request->file('invoices') as $file) {
                        if (! $file->isValid()) {
                            continue;
                        }

                        // file name rapi
                        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $ext = $file->getClientOriginalExtension();

                        $safeFileName = now()->format('YmdHis')
                            .'-'.Str::slug($baseName)
                            .'.'.$ext;

                        $receipt->addMedia($file)
                            ->usingFileName($safeFileName)
                            ->toMediaCollection('invoices');
                    }
                }

                return $receipt;
            });

            return redirect()
                ->back()
                ->with('success', "Barang masuk berhasil disimpan. No: {$receipt->receipt_number}");

        } catch (Throwable $e) {
            save_log_error($e);

            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Gagal menyimpan barang masuk. Silakan coba lagi.',
                ]);
        }
    }

    public function addMedia(Request $request, $id)
    {
        $request->validate([
            'invoices' => ['required'], // jangan paksa array
            'invoices.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'],
        ]);
        try {
            $receipt = PurchaseReceipt::findOrFail($id);

            if (! $request->hasFile('invoices')) {
                return back()->withErrors(['invoices' => 'File invoice tidak ditemukan.']);
            }

            $files = $request->file('invoices');
            // kalau single file, jadikan array
            $files = is_array($files) ? $files : [$files];

            foreach ($files as $file) {
                if (! $file || ! $file->isValid()) {
                    continue;
                }

                $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $ext = strtolower($file->getClientOriginalExtension());

                $safeFileName = now()->format('YmdHis')
                    .'-'.Str::slug($baseName)
                    .'.'.$ext;

                $receipt->addMedia($file)
                    ->usingFileName($safeFileName)
                    ->toMediaCollection('invoices');
            }

            return back()->with('success', 'Invoice berhasil ditambahkan.');
        } catch (\Throwable $th) {
            save_log_error($th);

            return back()->withErrors(['error' => 'Gagal menyimpan invoice. Silakan coba lagi.']);
        }
    }

    public function edit($id)
    {
        $path = public_path('assets/data/provinceAndCity.json');

        $json = File::get($path);

        $data = json_decode($json, true);

        $provinces = collect($data['geonames'] ?? [])
            ->map(fn ($p) => [
                'id' => $p['geonameId'] ?? null,
                'name' => $p['name'] ?? null,
            ])
            ->filter(fn ($p) => ! empty($p['name']))
            ->values();

        $receipt = PurchaseReceipt::with([
            'items.rawMaterial',
            'media',
        ])->findOrFail($id);

        $rawMaterials = RawMaterial::select('id', 'code', 'name', 'unit')
            ->orderBy('name')
            ->get();

        $procurements = Procurement::where('status', 'diterima')
            ->orWhere('id', $receipt->procurement_id)
            ->get();

        // filter collection invoices saja (tidak query lagi)
        $invoices = $receipt->media
            ->where('collection_name', 'invoices')
            ->values();

        return view('admin.purchase.purchase-edit', compact(
            'receipt',
            'provinces',
            'rawMaterials',
            'procurements',
            'invoices'
        ));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'procurement_id' => ['required', 'integer', 'exists:procurements,id'],
            'province' => ['required'],
            'received_at' => ['required', 'date'],
            'total_price' => ['nullable', 'integer', 'min:0'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.raw_material_id' => ['required', 'integer', 'exists:raw_materials,id'],
            'items.*.quantity_received' => ['required', 'integer', 'min:1'],
        ]);

        try {
            DB::transaction(function () use ($validated, $id) {
                $userId = auth()->id();

                $receipt = PurchaseReceipt::with(['items'])->findOrFail($id);

                /**
                 * 1) BALIKIN STOK DARI ITEM LAMA (reverse)
                 * - kurangi stock berdasarkan item lama
                 * - hapus movement lama untuk receipt ini
                 */
                foreach ($receipt->items as $oldItem) {
                    RawMaterialStock::where([
                        'raw_material_id' => (int) $oldItem->raw_material_id,
                        'province' => $receipt->province, // province lama
                    ])->decrement('stock', (int) $oldItem->quantity_received);
                }

                RawMaterialStockMovement::where([
                    'ref_type' => PurchaseReceipt::class,
                    'ref_id' => $receipt->id,
                ])->delete();

                /**
                 * 2) UPDATE HEADER RECEIPT
                 */
                $receipt->update([
                    'procurement_id' => (int) $validated['procurement_id'],
                    'province' => (string) $validated['province'],
                    'received_at' => $validated['received_at'],
                    'total_price' => (int) ($validated['total_price'] ?? 0),
                ]);

                /**
                 * 3) HAPUS ITEM LAMA, INSERT ITEM BARU
                 */
                PurchaseReceiptItem::where('purchase_receipt_id', $receipt->id)->delete();

                foreach ($validated['items'] as $row) {
                    PurchaseReceiptItem::create([
                        'purchase_receipt_id' => $receipt->id,
                        'raw_material_id' => (int) $row['raw_material_id'],
                        'quantity_received' => (int) $row['quantity_received'],
                    ]);

                    // movement baru
                    RawMaterialStockMovement::create([
                        'province' => $receipt->province, // province baru
                        'raw_material_id' => (int) $row['raw_material_id'],
                        'type' => 'in',
                        'stock' => (int) $row['quantity_received'],
                        'ref_type' => PurchaseReceipt::class,
                        'ref_id' => $receipt->id,
                        'responsible_id' => $userId,
                        'note' => "Update penerimaan dari procurement ID {$receipt->procurement_id}",
                    ]);

                    // pastikan stock row ada
                    RawMaterialStock::updateOrCreate(
                        [
                            'raw_material_id' => (int) $row['raw_material_id'],
                            'province' => $receipt->province,
                        ],
                        []
                    );

                    // tambah stock baru
                    RawMaterialStock::where([
                        'raw_material_id' => (int) $row['raw_material_id'],
                        'province' => $receipt->province,
                    ])->increment('stock', (int) $row['quantity_received']);
                }
            });

            return redirect()
                ->route('edit-barang-masuk', $id)
                ->with('success', 'Data barang masuk berhasil diupdate.');
        } catch (\Throwable $th) {
            save_log_error($th);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal update barang masuk. Silakan coba lagi.']);
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {

                $receipt = PurchaseReceipt::with(['items'])
                    ->findOrFail($id);

                // CEK: jangan sampai stok jadi minus setelah reverse
                foreach ($receipt->items as $item) {
                    $current = RawMaterialStock::where([
                        'raw_material_id' => (int) $item->raw_material_id,
                        'province' => $receipt->province,
                    ])->value('stock') ?? 0;

                    if ($current < (int) $item->quantity_received) {
                        throw new \Exception('Stok sudah terpakai. Tidak bisa hapus barang masuk ini.');
                    }
                }

                // REVERSE STOK
                foreach ($receipt->items as $item) {
                    RawMaterialStock::where([
                        'raw_material_id' => (int) $item->raw_material_id,
                        'province' => $receipt->province,
                    ])->decrement('stock', (int) $item->quantity_received);
                }

                // HAPUS MOVEMENT untuk receipt ini
                RawMaterialStockMovement::where([
                    'ref_type' => PurchaseReceipt::class,
                    'ref_id' => $receipt->id,
                ])->delete();

                // OPTIONAL: kalau mau bikin jejak bahwa ini di-cancel, bikin movement 'out' juga
                // (kalau kamu mau audit trail lengkap, bilang nanti aku bikinin)

                // SOFT DELETE RECEIPT (record tetap ada)
                $receipt->delete();
            });

            return redirect()
                ->route('purchase-receipts')
                ->with('success', 'Barang masuk berhasil dihapus (soft delete).');
        } catch (\Throwable $th) {
            save_log_error($th);

            return back()->withErrors([
                'error' => $th->getMessage() ?: 'Gagal menghapus barang masuk.',
            ]);
        }
    }
}
