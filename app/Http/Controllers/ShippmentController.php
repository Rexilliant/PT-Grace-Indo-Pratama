<?php

namespace App\Http\Controllers;

use App\Models\ProductStock;
use App\Models\ProductStockMovement;
use App\Models\Shippment;
use App\Models\ShippmentItem;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ShippmentController extends Controller
{
    public function index(Request $request)
    {
        $q = Shippment::query()->with('personResponsible');

        if ($request->filled('name')) {
            $q->whereHas('personResponsible', function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->name.'%');
            });
        }
        if ($request->filled('code')) {
            $q->where('shippment_code', 'like', '%'.$request->code.'%');
        }
        // ROWS PER PAGE (dropdown 10/25/50)
        $perPage = (int) ($request->get('per_page', 10));
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;

        $shippments = $q->paginate($perPage)->withQueryString();
        $statuses = Shippment::query()
            ->select('status')
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');
        $warehouses = Warehouse::all();

        return view('admin.shippments.shippment', compact('shippments', 'statuses', 'warehouses'));
    }

    public function create()
    {
        $users = User::all();
        $warehouses = Warehouse::all();
        $productStocks = ProductStock::with('productVariant')
            ->where('stock', '>', 0)
            ->get();

        return view('admin.shippments.create-shippments', compact(
            'users',
            'warehouses',
            'productStocks'
        ));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'shippment_request_at' => ['required', 'date'],
                'shippment_type' => ['required', 'string', 'max:255'],
                'warehouse_id' => ['required', 'exists:warehouses,id'],
                'shipping_fleet' => ['required', 'string', 'max:255'],
                'received_by_id' => ['nullable', 'exists:users,id'],
                'contact' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string'],
                'notes' => ['nullable', 'string'],
                'shippment_at' => ['nullable', 'date'],
                'shippment_services' => ['nullable', 'string', 'max:255'],
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
                ->map(fn ($item) => [
                    'product_stock_id' => (int) $item['product_stock_id'],
                    'quantity' => (int) $item['quantity'],
                ])
                ->groupBy('product_stock_id')
                ->map(fn ($rows, $productStockId) => [
                    'product_stock_id' => (int) $productStockId,
                    'quantity' => $rows->sum('quantity'),
                ])
                ->values();

            DB::transaction(function () use (
                $validated,
                $userId,
                $sourceWarehouseId,
                $targetWarehouseId,
                $groupedItems
            ) {
                $stockIds = $groupedItems->pluck('product_stock_id')->all();

                $sourceStocks = ProductStock::with('productVariant')
                    ->whereIn('id', $stockIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($groupedItems as $item) {
                    $sourceStock = $sourceStocks->get($item['product_stock_id']);

                    if (! $sourceStock) {
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

                $shippment = Shippment::create([
                    'shippment_code' => 'SHP-'.now()->format('YmdHis'),
                    'shippment_type' => $validated['shippment_type'],
                    'person_responsible_id' => $userId,
                    'status' => 'Menunggu',
                    'warehouse_id' => $targetWarehouseId,
                    'address' => $validated['address'],
                    'shippment_request_at' => $validated['shippment_request_at'],
                    'created_by_id' => $userId,
                    'received_by_id' => $validated['received_by_id'] ?? null,
                    'shippment_at' => $validated['shippment_at'] ?? null,
                    'shippment_services' => $validated['shippment_services'] ?? null,
                    'contact' => $validated['contact'],
                    'shipping_fleet' => $validated['shipping_fleet'],
                    'notes' => $validated['notes'] ?? null,
                ]);

                foreach ($groupedItems as $item) {
                    $sourceStock = $sourceStocks->get($item['product_stock_id']);

                    ShippmentItem::create([
                        'shippment_id' => $shippment->id,
                        'product_stock_id' => $sourceStock->id,
                        'quantity' => $item['quantity'],
                    ]);
                }
            });

            return redirect()
                ->route('shippments')
                ->with('success', 'Pengiriman berhasil dibuat.');
        } catch (\Throwable $th) {
            if (function_exists('save_log_error')) {
                save_log_error($th);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pengiriman: '.$th->getMessage());
        }
    }

    public function edit($id)
    {
        $shippment = Shippment::with([
            'personResponsible',
            'shippmentItems.productStock.productVariant',
        ])->findOrFail($id);
        $invoices = $shippment->media
            ->where('collection_name', 'invoices_shippment')
            ->values();

        return view('admin.shippments.edit-shippmetns', compact('shippment', 'invoices'));
    }

    public function update(Request $request, $id)
    {
        try {
            $shippment = Shippment::with([
                'shippmentItems.productStock.productVariant',
            ])->findOrFail($id);

            $validated = $request->validate([
                'status' => ['required', Rule::in(['Menunggu', 'Disetujui', 'Ditolak', 'Dikirim', 'Selesai'])],
                'reason' => ['nullable', 'string', 'required_if:status,Ditolak'],
                'shippment_at' => ['nullable', 'date', 'required_if:status,Dikirim'],
                'invoices' => ['nullable', 'array'],
                'invoices.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'],
            ], [
                'status.required' => 'Status wajib dipilih.',
                'status.in' => 'Status tidak valid.',
                'reason.required_if' => 'Alasan wajib diisi jika status ditolak.',
                'shippment_at.required_if' => 'Tanggal pengiriman wajib diisi jika status dikirim.',
                'invoices.*.mimes' => 'Invoice harus berupa file JPG, JPEG, PNG, atau PDF.',
                'invoices.*.max' => 'Ukuran file invoice maksimal 3MB.',
            ]);

            $currentStatus = $shippment->status;
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
                ! in_array($newStatus, $allowedTransitions[$currentStatus] ?? [])
            ) {
                throw ValidationException::withMessages([
                    'status' => 'Perubahan status tidak valid.',
                ]);
            }

            DB::transaction(function () use ($request, $validated, $shippment, $currentStatus, $newStatus) {
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
                    $alreadyOut = ProductStockMovement::where('ref_type', Shippment::class)
                        ->where('ref_id', $shippment->id)
                        ->where('type', 'Out')
                        ->exists();

                    if ($alreadyOut) {
                        throw ValidationException::withMessages([
                            'status' => 'Stok pengiriman sudah pernah diproses.',
                        ]);
                    }

                    foreach ($shippment->shippmentItems as $item) {
                        $sourceStock = ProductStock::with('productVariant')
                            ->lockForUpdate()
                            ->find($item->product_stock_id);

                        if (! $sourceStock) {
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
                            'ref_type' => Shippment::class,
                            'ref_id' => $shippment->id,
                            'note' => 'Pengeluaran stok untuk pengiriman '.$shippment->shippment_code,
                        ]);
                    }

                    $updateData['shippment_at'] = $validated['shippment_at'] ?? now()->toDateString();
                }

                if ($newStatus === 'Selesai' && $currentStatus !== 'Selesai') {
                    if ($currentStatus !== 'Dikirim') {
                        throw ValidationException::withMessages([
                            'status' => 'Pengiriman hanya dapat diselesaikan setelah status Dikirim.',
                        ]);
                    }
                }

                $shippment->update($updateData);

                if ($request->hasFile('invoices')) {
                    foreach ($request->file('invoices') as $file) {
                        $filename = now()->format('YmdHis').'_'.uniqid().'.'.$file->getClientOriginalExtension();

                        $shippment
                            ->addMedia($file)
                            ->usingFileName($filename)
                            ->toMediaCollection('invoices_shippment');
                    }
                }
            });

            return redirect()
                ->route('edit-shippment', $shippment->id)
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
                ->with('error', 'Gagal memperbarui status pengiriman: '.$th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $shippment = Shippment::with('shippmentItems')->findOrFail($id);

            // hanya boleh hapus jika status Menunggu
            if ($shippment->status !== 'Menunggu') {
                return redirect()
                    ->back()
                    ->with('error', 'Pengiriman hanya bisa dihapus saat status masih Menunggu.');
            }

            DB::transaction(function () use ($shippment) {

                // hapus media (jika pakai spatie media)
                if (method_exists($shippment, 'clearMediaCollection')) {
                    $shippment->clearMediaCollection('invoices_shippment');
                }

                // hapus item
                $shippment->shippmentItems()->delete();

                // hapus data utama
                $shippment->delete();
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
                ->with('error', 'Gagal menghapus pengiriman: '.$th->getMessage());
        }
    }
}
