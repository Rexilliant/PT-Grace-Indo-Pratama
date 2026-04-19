<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductStock;
use App\Models\ProductStockMovement;
use App\Models\Shipment;
use App\Models\ShipmentReceipt;
use App\Models\ShipmentReceiptItem;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PHPUnit\Event\Code\Throwable;

class ShipmentReceiptController extends Controller
{
    public function index(Request $request)
    {
        $q = ShipmentReceipt::query()->with([
            'shipment.warehouse',
            'receivedBy',
        ]);

        if ($request->filled('name')) {
            $q->whereHas('receivedBy', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
            });
        }

        if ($request->filled('code')) {
            $q->whereHas('shipment', function ($query) use ($request) {
                $query->where('shipment_code', 'like', '%' . $request->code . '%');
            });
        }

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        if ($request->filled('warehouse_id')) {
            $q->whereHas('shipment', function ($query) use ($request) {
                $query->where('warehouse_id', $request->warehouse_id);
            });
        }

        $perPage = (int) ($request->get('per_page', 10));
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;

        $shipmentReceipts = $q->latest()->paginate($perPage)->withQueryString();

        $statuses = ShipmentReceipt::query()
            ->select('status')
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        $warehouses = Warehouse::all();

        return view('admin.shipment-receipts.shipment-receipt', compact(
            'shipmentReceipts',
            'statuses',
            'warehouses'
        ));
    }

    public function create()
    {
        $shipments = Shipment::with([
            'warehouse',
            'receivedBy',
        ])->where('status', '=', 'dikirim')->get();

        return view('admin.shipment-receipts.create-shipment-receipt', compact('shipments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipment_id' => 'required|exists:shipments,id',
            'received_at' => 'required|date',
            'notes' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.shipment_item_id' => 'required|exists:shipment_items,id',
            'items.*.qty_received' => 'required|integer|min:0',

            // ✅ TAMBAHAN (UPLOAD)
            'damage_proofs' => 'nullable|array',
            'damage_proofs.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072',
        ], [
            'shipment_id.required' => 'Shipment wajib dipilih.',
            'received_at.required' => 'Tanggal diterima wajib diisi.',
            'items.required' => 'Daftar item receipt wajib diisi.',
            'items.min' => 'Minimal harus ada satu item receipt.',
            'items.*.shipment_item_id.required' => 'Item shipment wajib dipilih.',
            'items.*.qty_received.required' => 'Qty diterima wajib diisi.',
        ]);

        DB::beginTransaction();

        try {
            $shipment = Shipment::with('shipmentItems')->findOrFail($validated['shipment_id']);

            $shipmentItems = $shipment->shipmentItems->keyBy('id');

            foreach ($validated['items'] as $index => $item) {
                $shipmentItem = $shipmentItems->get((int) $item['shipment_item_id']);

                if (!$shipmentItem) {
                    throw ValidationException::withMessages([
                        "items.$index.shipment_item_id" => 'Item shipment tidak sesuai dengan shipment yang dipilih.',
                    ]);
                }

                if ((int) $item['qty_received'] > (int) $shipmentItem->quantity) {
                    throw ValidationException::withMessages([
                        "items.$index.qty_received" => 'Qty diterima tidak boleh melebihi qty dikirim.',
                    ]);
                }
            }

            $shipmentReceipt = ShipmentReceipt::create([
                'shipment_id' => $validated['shipment_id'],
                'status' => 'received',
                'received_by_id' => Auth::id(),
                'received_at' => $validated['received_at'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                ShipmentReceiptItem::create([
                    'shipment_receipt_id' => $shipmentReceipt->id,
                    'shipment_item_id' => $item['shipment_item_id'],
                    'qty_received' => $item['qty_received'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            // ✅ TAMBAHAN (UPLOAD FILE)
            if ($request->hasFile('damage_proofs')) {
                foreach ($request->file('damage_proofs') as $file) {
                    if (!$file->isValid())
                        continue;

                    $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $ext = $file->getClientOriginalExtension();

                    $safeFileName = now()->format('YmdHis')
                        . '-' . \Illuminate\Support\Str::slug($baseName)
                        . '.' . $ext;

                    $shipmentReceipt->addMedia($file)
                        ->usingFileName($safeFileName)
                        ->toMediaCollection('damage_proofs');
                }
            }

            DB::commit();

            return redirect()
                ->route('shipment-receipts')
                ->with('success', 'Shipment receipt berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            save_log_error($th);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Shipment receipt gagal disimpan. ');
        }
    }

    public function edit($id)
    {
        $shipmentReceipt = ShipmentReceipt::with([
            'shipment.warehouse',
            'shipment.receivedBy',
            'items.shipmentItem.productStock.productVariant',
        ])->findOrFail($id);

        $damageProofs = $shipmentReceipt->getMedia('damage_proofs');

        return view('admin.shipment-receipts.edit-shipment-receipt', compact(
            'shipmentReceipt',
            'damageProofs'
        ));
    }

    public function update(Request $request, $id)
    {
        $shipmentReceipt = ShipmentReceipt::with([
            'shipment.shipmentItems.productStock.productVariant',
            'shipment.warehouse',
            'items.shipmentItem.productStock.productVariant',
        ])->findOrFail($id);

        $oldStatus = $shipmentReceipt->status;
        $isLocked = $oldStatus !== 'received';

        $rules = [
            'status' => 'required|in:received,approved,rejected',
            'reject_reason' => 'nullable|string|required_if:status,rejected',
        ];

        if (!$isLocked) {
            $rules = array_merge($rules, [
                'received_at' => 'required|date',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.shipment_receipt_item_id' => 'required|exists:shipment_receipt_items,id',
                'items.*.shipment_item_id' => 'required|exists:shipment_items,id',
                'items.*.qty_received' => 'required|integer|min:0',
                'items.*.notes' => 'nullable|string',
            ]);
        }

        $validated = $request->validate($rules, [
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status tidak valid.',
            'reject_reason.required_if' => 'Alasan penolakan wajib diisi saat status rejected.',
            'received_at.required' => 'Tanggal diterima wajib diisi.',
            'items.required' => 'Daftar item receipt wajib diisi.',
            'items.min' => 'Minimal harus ada satu item receipt.',
        ]);

        DB::beginTransaction();

        try {
            $newStatus = $validated['status'];

            if (!$isLocked) {
                $shipmentItems = $shipmentReceipt->shipment->shipmentItems->keyBy('id');
                $receiptItems = $shipmentReceipt->items->keyBy('id');

                foreach ($validated['items'] as $index => $item) {
                    $receiptItem = $receiptItems->get((int) $item['shipment_receipt_item_id']);

                    if (!$receiptItem) {
                        throw ValidationException::withMessages([
                            "items.$index.shipment_receipt_item_id" => 'Item receipt tidak valid.',
                        ]);
                    }

                    if ((int) $receiptItem->shipment_item_id !== (int) $item['shipment_item_id']) {
                        throw ValidationException::withMessages([
                            "items.$index.shipment_item_id" => 'Item shipment tidak sesuai.',
                        ]);
                    }

                    $shipmentItem = $shipmentItems->get((int) $item['shipment_item_id']);

                    if (!$shipmentItem) {
                        throw ValidationException::withMessages([
                            "items.$index.shipment_item_id" => 'Item shipment tidak ditemukan pada shipment ini.',
                        ]);
                    }

                    if ((int) $item['qty_received'] > (int) $shipmentItem->quantity) {
                        throw ValidationException::withMessages([
                            "items.$index.qty_received" => 'Qty diterima tidak boleh melebihi qty dikirim.',
                        ]);
                    }
                }

                $shipmentReceipt->received_at = $validated['received_at'];
                $shipmentReceipt->notes = $validated['notes'] ?? null;

                foreach ($validated['items'] as $item) {
                    $receiptItem = $shipmentReceipt->items->firstWhere('id', (int) $item['shipment_receipt_item_id']);

                    if ($receiptItem) {
                        $receiptItem->update([
                            'qty_received' => $item['qty_received'],
                            'notes' => $item['notes'] ?? null,
                        ]);
                    }
                }
            }

            $shipmentReceipt->status = $newStatus;

            /*
            |--------------------------------------------------------------------------
            | APPROVED
            |--------------------------------------------------------------------------
            | Saat status berubah menjadi approved, masukkan stok ke gudang tujuan
            | dan catat pergerakan stok sebagai type = in.
            */
            if ($newStatus === 'approved') {
                $shipmentReceipt->approved_by_id = Auth::id();
                $shipmentReceipt->approved_at = now();
                $shipmentReceipt->rejected_by_id = null;
                $shipmentReceipt->rejected_at = null;
                $shipmentReceipt->reject_reason = null;
                $shipmentReceipt->shipment->status = 'Selesai';
                $shipmentReceipt->shipment->save();

                // Jalankan stock in hanya saat transisi ke approved pertama kali
                if ($oldStatus !== 'approved') {
                    $destinationWarehouseId = $shipmentReceipt->shipment->warehouse_id;

                    foreach ($shipmentReceipt->items as $receiptItem) {
                        $shipmentItem = $receiptItem->shipmentItem;

                        if (!$shipmentItem) {
                            continue;
                        }

                        $sourceProductStock = $shipmentItem->productStock;

                        if (!$sourceProductStock) {
                            throw ValidationException::withMessages([
                                'items' => 'Product stock asal tidak ditemukan pada shipment item.',
                            ]);
                        }

                        $productVariantId = $sourceProductStock->product_variant_id;
                        $qtyIn = (int) $receiptItem->qty_received;

                        if ($qtyIn <= 0) {
                            continue;
                        }

                        $destinationProductStock = ProductStock::withTrashed()->firstOrNew([
                            'product_variant_id' => $productVariantId,
                            'warehouse_id' => $destinationWarehouseId,
                        ]);

                        if ($destinationProductStock->exists && $destinationProductStock->trashed()) {
                            $destinationProductStock->restore();
                        }

                        if (!$destinationProductStock->exists) {
                            $destinationProductStock->stock = 0;
                        }

                        $destinationProductStock->stock = (int) $destinationProductStock->stock + $qtyIn;
                        $destinationProductStock->save();

                        ProductStockMovement::create([
                            'warehouse_id' => $destinationWarehouseId,
                            'product_stock_id' => $destinationProductStock->id,
                            'type' => 'in',
                            'quantity' => $qtyIn,
                            'ref_type' => 'shipment_receipt',
                            'ref_id' => $shipmentReceipt->id,
                            'note' => 'Stock masuk dari approval shipment receipt #' . $shipmentReceipt->id,
                        ]);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | REJECTED
            |--------------------------------------------------------------------------
            */
            if ($newStatus === 'rejected') {
                $shipmentReceipt->rejected_by_id = Auth::id();
                $shipmentReceipt->rejected_at = now();
                $shipmentReceipt->approved_by_id = null;
                $shipmentReceipt->approved_at = null;
                $shipmentReceipt->reject_reason = $validated['reject_reason'];

            }

            /*
            |--------------------------------------------------------------------------
            | RECEIVED
            |--------------------------------------------------------------------------
            */
            if ($newStatus === 'received') {
                $shipmentReceipt->approved_by_id = null;
                $shipmentReceipt->approved_at = null;
                $shipmentReceipt->rejected_by_id = null;
                $shipmentReceipt->rejected_at = null;
                $shipmentReceipt->reject_reason = null;
            }

            $shipmentReceipt->save();

            DB::commit();

            return redirect()
                ->route('shipment-receipts')
                ->with('success', 'Shipment receipt berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $th) {
            DB::rollBack();
            save_log_error($th);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Shipment receipt gagal diperbarui');
        }
    }

    public function destroy($id)
    {
        $shipmentReceipt = ShipmentReceipt::with([
            'shipment',
            'items',
        ])->findOrFail($id);

        if ($shipmentReceipt->status !== 'received') {
            return redirect()
                ->back()
                ->with('error', 'Shipment receipt tidak bisa dihapus karena sudah diproses.');
        }

        DB::beginTransaction();

        try {
            // SIMPAN USER YANG MENGHAPUS
            $shipmentReceipt->deleted_by = Auth::id();
            $shipmentReceipt->save();

            // hapus item
            foreach ($shipmentReceipt->items as $item) {
                $item->delete();
            }

            // soft delete receipt
            $shipmentReceipt->delete();

            DB::commit();

            return redirect()
                ->route('shipment-receipts')
                ->with('success', 'Shipment receipt berhasil dihapus.');
        } catch (\Throwable $th) {
            DB::rollBack();
            save_log_error($th);

            return redirect()
                ->back()
                ->with('error', 'Shipment receipt gagal dihapus.');
        }
    }
}