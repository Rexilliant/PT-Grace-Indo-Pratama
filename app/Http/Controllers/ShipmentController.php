<?php

namespace App\Http\Controllers;

use App\Models\ProductStock;
use App\Models\ProductStockMovement;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $q = Shipment::query()->with('personResponsible');

        if ($request->filled('name')) {
            $q->whereHas('personResponsible', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
            });
        }
        if ($request->filled('code')) {
            $q->where('shipment_code', 'like', '%' . $request->code . '%');
        }
        // ROWS PER PAGE (dropdown 10/25/50)
        $perPage = (int) ($request->get('per_page', 10));
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;

        $shipments = $q->latest()->paginate($perPage)->withQueryString();
        $statuses = Shipment::query()
            ->select('status')
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');
        $warehouses = Warehouse::all();

        return view('admin.shipments.shipments', compact('shipments', 'statuses', 'warehouses'));
    }

    public function create()
    {
        $users = User::all();
        $warehouses = Warehouse::all();
        $productStocks = ProductStock::with('productVariant')
            ->where('stock', '>', 0)
            ->get();

        return view('admin.shipments.create-shipments', compact(
            'users',
            'warehouses',
            'productStocks'
        ));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'shipment_request_at' => ['required', 'date'],
                'shipment_type' => ['required', 'string', 'max:255'],
                'warehouse_id' => ['required', 'exists:warehouses,id'],
                'shipping_fleet' => ['required', 'string', 'max:255'],
                'received_by_id' => ['nullable', 'exists:users,id'],
                'contact' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string'],
                'notes' => ['nullable', 'string'],
                'shipment_at' => ['nullable', 'date'],
                'shipment_services' => ['nullable', 'string', 'max:255'],
                'stock_warehouse' => ['required', 'exists:warehouses,id'],

                'items' => ['required', 'array', 'min:1'],
                'items.*.product_stock_id' => ['required', 'exists:product_stocks,id'],
                'items.*.quantity' => ['required', 'integer', 'min:1'],
            ], [
                'items.required' => 'Item pengiriman wajib diisi.',
                'items.min' => 'Minimal harus ada 1 item pengiriman.',
                'stock_warehouse.required' => 'Gudang stok wajib dipilih.',
                'stock_warehouse.exists' => 'Gudang stok tidak valid.',
            ]);

            $userId = Auth::id();

            $sourceWarehouseId = (int) $validated['stock_warehouse'];
            $targetWarehouseId = (int) $validated['warehouse_id'];

            $groupedItems = collect($validated['items'])
                ->map(fn($item) => [
                    'product_stock_id' => (int) $item['product_stock_id'],
                    'quantity' => (int) $item['quantity'],
                ])
                ->groupBy('product_stock_id')
                ->map(fn($rows, $productStockId) => [
                    'product_stock_id' => (int) $productStockId,
                    'quantity' => $rows->sum('quantity'),
                ])
                ->values();

            DB::transaction(function () use ($validated, $userId, $sourceWarehouseId, $targetWarehouseId, $groupedItems) {
                $stockIds = $groupedItems->pluck('product_stock_id')->all();

                $sourceStocks = ProductStock::with('productVariant')
                    ->whereIn('id', $stockIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($groupedItems as $item) {
                    $sourceStock = $sourceStocks->get($item['product_stock_id']);

                    if (!$sourceStock) {
                        throw new \Exception('Produk stok tidak ditemukan.');
                    }

                    if ((int) $sourceStock->warehouse_id !== $sourceWarehouseId) {
                        $productName = $sourceStock->productVariant->name ?? 'Produk';
                        throw new \Exception("{$productName} tidak berasal dari gudang stok yang dipilih.");
                    }

                    if ((int) $item['quantity'] > (int) $sourceStock->stock) {
                        $productName = $sourceStock->productVariant->name ?? 'Produk';
                        throw new \Exception("Stok {$productName} tidak cukup. Tersedia {$sourceStock->stock}, diminta {$item['quantity']}.");
                    }
                }

                $shipment = Shipment::create([
                    'shipment_code' => 'SHP-' . now()->format('YmdHis'),
                    'shipment_type' => $validated['shipment_type'],
                    'person_responsible_id' => $userId,
                    'status' => 'Menunggu',
                    'warehouse_id' => $targetWarehouseId,
                    'address' => $validated['address'],
                    'shipment_request_at' => $validated['shipment_request_at'],
                    'created_by_id' => $userId,
                    'received_by_id' => $validated['received_by_id'] ?? null,
                    'shipment_at' => $validated['shipment_at'] ?? null,
                    'shipment_services' => $validated['shipment_services'] ?? null,
                    'contact' => $validated['contact'],
                    'shipping_fleet' => $validated['shipping_fleet'],
                    'notes' => $validated['notes'] ?? null,
                ]);

                foreach ($groupedItems as $item) {
                    $sourceStock = $sourceStocks->get($item['product_stock_id']);

                    ShipmentItem::create([
                        'shipment_id' => $shipment->id,
                        'product_stock_id' => $sourceStock->id,
                        'quantity' => $item['quantity'],
                    ]);
                }
            });

            return redirect()
                ->route('shipments')
                ->with('success', 'Pengiriman berhasil dibuat.');
        } catch (\Throwable $th) {
            if (function_exists('save_log_error')) {
                save_log_error($th);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pengiriman: ' . $th->getMessage());
        }
    }

    public function edit($id)
    {
        $shipment = Shipment::with([
            'personResponsible',
            'shipmentItems.productStock.productVariant',
        ])->findOrFail($id);
        $invoices = $shipment->media
            ->where('collection_name', 'invoices_shipment')
            ->values();

        return view('admin.shipments.edit-shipments', compact('shipment', 'invoices'));
    }

    public function update(Request $request, $id)
    {
        try {
            $shipment = Shipment::with([
                'shipmentItems.productStock.productVariant',
            ])->findOrFail($id);

            $validated = $request->validate([
                'status' => ['required', Rule::in(['Menunggu', 'Disetujui', 'Ditolak', 'Dikirim', 'Selesai'])],
                'reason' => ['nullable', 'string', 'required_if:status,Ditolak'],
                'shipment_at' => ['nullable', 'date', 'required_if:status,Dikirim'],
                'invoices' => ['nullable', 'array'],
                'invoices.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'],
            ], [
                'status.required' => 'Status wajib dipilih.',
                'status.in' => 'Status tidak valid.',
                'reason.required_if' => 'Alasan wajib diisi jika status ditolak.',
                'shipment_at.required_if' => 'Tanggal pengiriman wajib diisi jika status dikirim.',
                'invoices.*.mimes' => 'Invoice harus berupa file JPG, JPEG, PNG, atau PDF.',
                'invoices.*.max' => 'Ukuran file invoice maksimal 3MB.',
            ]);

            $currentStatus = $shipment->status;
            $newStatus = $validated['status'];

            $allowedTransitions = [
                'Menunggu' => ['Disetujui', 'Ditolak'],
                'Disetujui' => ['Dikirim'],
                'Dikirim' => ['Selesai'],
                'Ditolak' => [],
                'Selesai' => [],
            ];

            if (
                $newStatus !== $currentStatus &&
                !in_array($newStatus, $allowedTransitions[$currentStatus] ?? [])
            ) {
                throw ValidationException::withMessages([
                    'status' => 'Perubahan status tidak valid.',
                ]);
            }

            DB::transaction(function () use ($request, $validated, $shipment, $currentStatus, $newStatus) {
                $updateData = [
                    'status' => $newStatus,
                ];

                if ($newStatus === 'Ditolak' && $currentStatus !== 'Ditolak') {
                    $updateData['reason'] = $validated['reason'] ?? null;
                    $updateData['rejected_at'] = now();
                    $updateData['rejected_by_id'] = auth()->id();
                    $updateData['approved_at'] = null;
                    $updateData['approved_by_id'] = null;
                }

                if ($newStatus === 'Disetujui' && $currentStatus !== 'Disetujui') {
                    $updateData['approved_at'] = now();
                    $updateData['approved_by_id'] = auth()->id();
                    $updateData['rejected_at'] = null;
                    $updateData['rejected_by_id'] = null;
                    $updateData['reason'] = null;
                }

                if ($newStatus === 'Dikirim' && $currentStatus !== 'Dikirim') {
                    $alreadyOut = ProductStockMovement::where('ref_type', Shipment::class)
                        ->where('ref_id', $shipment->id)
                        ->where('type', 'Out')
                        ->exists();

                    if ($alreadyOut) {
                        throw ValidationException::withMessages([
                            'status' => 'Stok pengiriman sudah pernah diproses.',
                        ]);
                    }

                    foreach ($shipment->shipmentItems as $item) {
                        $sourceStock = ProductStock::with('productVariant')
                            ->lockForUpdate()
                            ->find($item->product_stock_id);

                        if (!$sourceStock) {
                            throw ValidationException::withMessages([
                                'status' => 'Stok produk tidak ditemukan.',
                            ]);
                        }

                        if ((int) $sourceStock->stock < (int) $item->quantity) {
                            $productName = $sourceStock->productVariant->name ?? 'Produk';
                            throw ValidationException::withMessages([
                                'status' => "Stok {$productName} tidak mencukupi untuk dikirim.",
                            ]);
                        }

                        $sourceStock->decrement('stock', (int) $item->quantity);

                        ProductStockMovement::create([
                            'warehouse_id' => $sourceStock->warehouse_id,
                            'product_stock_id' => $sourceStock->id,
                            'type' => 'Out',
                            'quantity' => (int) $item->quantity,
                            'ref_type' => Shipment::class,
                            'ref_id' => $shipment->id,
                            'note' => 'Pengeluaran stok untuk pengiriman ' . $shipment->shipment_code,
                        ]);
                    }

                    $updateData['shipment_at'] = $validated['shipment_at'] ?? now()->toDateString();
                }

                if ($newStatus === 'Selesai' && $currentStatus !== 'Selesai') {
                    if ($currentStatus !== 'Dikirim') {
                        throw ValidationException::withMessages([
                            'status' => 'Pengiriman hanya dapat diselesaikan setelah status Dikirim.',
                        ]);
                    }
                }

                $shipment->update($updateData);

                if ($request->hasFile('invoices')) {
                    foreach ($request->file('invoices') as $file) {
                        $filename = now()->format('YmdHis') . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                        $shipment
                            ->addMedia($file)
                            ->usingFileName($filename)
                            ->toMediaCollection('invoices_shipment');
                    }
                }
            });

            return redirect()
                ->route('edit-shipment', $shipment->id)
                ->with('success', 'Status pengiriman berhasil diperbarui.');
        } catch (ValidationException $th) {
            if (function_exists('save_log_error')) {
                save_log_error($th);
            }

            return back()
                ->withErrors($th->errors())
                ->withInput();
        } catch (\Throwable $th) {
            if (function_exists('save_log_error')) {
                save_log_error($th);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui status pengiriman: ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $shipment = Shipment::with('shipmentItems')->findOrFail($id);

            // hanya boleh hapus jika status Menunggu
            if ($shipment->status !== 'Menunggu') {
                return redirect()
                    ->back()
                    ->with('error', 'Pengiriman hanya bisa dihapus saat status masih Menunggu.');
            }

            DB::transaction(function () use ($shipment) {

                // ✅ simpan siapa yang delete (TAMBAHAN INTI)
                $shipment->deleted_by = auth()->id();
                $shipment->save();

                // hapus media
                if (method_exists($shipment, 'clearMediaCollection')) {
                    $shipment->clearMediaCollection('invoices_shipment');
                }

                // hapus item
                $shipment->shipmentItems()->delete();

                // soft delete
                $shipment->delete();
            });

            return redirect()
                ->back()
                ->with('success', 'Pengiriman berhasil dihapus.');
        } catch (\Throwable $th) {
            if (function_exists('save_log_error')) {
                save_log_error($th);
            }

            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus pengiriman: ' . $th->getMessage());
        }
    }

    public function getShipmentItems($id)
    {
        $shipment = Shipment::with([
            'shipmentItems.productStock.productVariant',
        ])->findOrFail($id);


        return response()->json($shipment->shipmentItems);
    }

}
