<?php

namespace App\Http\Controllers;

use App\Models\ProductStock;
use App\Models\ProductStockMovement;
use App\Models\ProductVariant;
use App\Models\Shippment;
use App\Models\ShippmentItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
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
        if ($request->filled('province')) {
            $q->where('province', 'like', '%'.$request->province.'%');
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
        $provinces = Shippment::query()
            ->select('province')
            ->whereNotNull('province')
            ->distinct()
            ->orderBy('province')
            ->pluck('province');

        return view('admin.shippments.shippment', compact('shippments', 'statuses', 'provinces'));
    }

    public function create()
    {
        $users = User::all();
        $productVariants = ProductVariant::all();
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

        $provinceProducts = ProductStock::select('province')
            ->distinct()
            ->orderBy('province')
            ->pluck('province');

        $productStocks = ProductStock::with('productVariant')
            ->where('stock', '>', 0)
            ->get();

        return view('admin.shippments.create-shippments', compact(
            'users',
            'productVariants',
            'provinceProducts',
            'provinces',
            'productStocks'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shippment_request_at' => ['required', 'date'],

            'shippment_type' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'shipping_fleet' => ['required', 'string', 'max:255'],
            'received_by_id' => ['nullable', 'exists:users,id'],
            'contact' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'shippment_at' => ['nullable', 'date'],
            'shippment_services' => ['nullable', 'string', 'max:255'],

            'stock_province' => ['required', 'string', 'max:255'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_stock_id' => ['required', 'exists:product_stocks,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ], [
            'items.required' => 'Item pengiriman wajib diisi.',
            'items.min' => 'Minimal harus ada 1 item pengiriman.',
            'stock_province.required' => 'Provinsi stok wajib dipilih.',
        ]);

        try {
            DB::beginTransaction();

            $groupedItems = collect($validated['items'])
                ->groupBy('product_stock_id')
                ->map(function ($rows, $productStockId) {
                    return [
                        'product_stock_id' => (int) $productStockId,
                        'quantity' => collect($rows)->sum('quantity'),
                    ];
                })
                ->values();

            $stockIds = $groupedItems->pluck('product_stock_id')->all();

            $sourceStocks = ProductStock::with('productVariant')
                ->whereIn('id', $stockIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($groupedItems as $item) {
                $sourceStock = $sourceStocks->get($item['product_stock_id']);

                if (! $sourceStock) {
                    DB::rollBack();

                    return back()
                        ->withInput()
                        ->withErrors([
                            'items' => 'Produk stok tidak ditemukan.',
                        ]);
                }

                if ($sourceStock->province !== $validated['stock_province']) {
                    DB::rollBack();

                    return back()
                        ->withInput()
                        ->withErrors([
                            'items' => "Produk {$sourceStock->productVariant->name} tidak berasal dari provinsi stok yang dipilih.",
                        ]);
                }

                if ($item['quantity'] > $sourceStock->stock) {
                    DB::rollBack();

                    return back()
                        ->withInput()
                        ->withErrors([
                            'items' => "Stok {$sourceStock->productVariant->name} tidak cukup. Tersedia {$sourceStock->stock}, diminta {$item['quantity']}.",
                        ]);
                }
            }

            $shippmentCode = 'SHP-'.now()->format('YmdHis');

            $shippment = Shippment::create([
                'shippment_code' => $shippmentCode,
                'shippment_type' => $validated['shippment_type'],
                'person_responsible_id' => Auth::id(),
                'status' => 'Menunggu',
                'province' => $validated['province'],
                'address' => $validated['address'],
                'shippment_request_at' => $validated['shippment_request_at'],
                'created_by_id' => Auth::id(),
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

            DB::commit();

            return redirect()
                ->route('shippments ')
                ->with('success', 'Pengiriman berhasil dibuat.');
        } catch (\Throwable $th) {
            DB::rollBack();
            save_log_error($th);

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
            $validated = $request->validate([
                'status' => ['required', Rule::in(['Menunggu', 'Ditolak', 'disetujui', 'Selesai'])],
                'shippment_at' => ['required_if:status,Selesai', 'nullable', 'date'],
                'reason' => [
                    'nullable',
                    'string',
                    'required_if:status,Ditolak',
                ],
                'invoices' => [
                    'nullable',
                    'array',
                    'required_if:status,Selesai',
                ],
                'invoices.*' => [
                    'file',
                    'mimes:jpg,jpeg,png,pdf',
                    'max:3072',
                ],
            ], [
                'status.required' => 'Status wajib dipilih.',
                'status.in' => 'Status tidak valid.',
                'shippment_at.required_if' => 'Tanggal pengiriman wajib diisi jika status disetujui.',
                'reason.required_if' => 'Alasan wajib diisi jika status ditolak.',
                'invoices.required_if' => 'Invoice wajib diupload jika status Selesai.',
                'invoices.array' => 'Format invoice tidak valid.',
                'invoices.*.mimes' => 'Invoice harus berupa file JPG, JPEG, PNG, atau PDF.',
                'invoices.*.max' => 'Ukuran file invoice maksimal 3MB.',
            ]);

            $shippment = Shippment::with([
                'shippmentItems.productStock.productVariant',
            ])->findOrFail($id);

            DB::beginTransaction();

            $oldStatus = $shippment->status;
            $newStatus = $validated['status'];

            $shippment->status = $newStatus;
            $shippment->reason = $newStatus === 'Ditolak' ? $validated['reason'] : null;

            if ($newStatus === 'Ditolak') {
                $shippment->rejected_by_id = auth()->id();
                $shippment->rejected_at = now();
                $shippment->approved_at = null;
                $shippment->approved_by_id = null;
                $shippment->received_at = null;
                $shippment->save();
            }

            if ($newStatus === 'disetujui') {
                if ($oldStatus === 'Selesai') {
                    throw ValidationException::withMessages([
                        'status' => 'Pengiriman yang sudah selesai tidak dapat diubah ke disetujui.',
                    ]);
                }

                foreach ($request->file('invoices', []) as $file) {
                    $filename = now()->format('YmdHis').'_'.uniqid().'.'.$file->getClientOriginalExtension();

                    $shippment
                        ->addMedia($file)
                        ->usingFileName($filename)
                        ->toMediaCollection('invoices_shippment');
                }

                $shippment->approved_at = now();
                $shippment->approved_by_id = auth()->id();
                $shippment->shippment_at = $validated['shippment_at'];
                $shippment->rejected_by_id = null;
                $shippment->rejected_at = null;
                $shippment->reason = null;
                $shippment->save();
            }

            if ($newStatus === 'Selesai') {
                if (! in_array($oldStatus, ['disetujui', 'Selesai'])) {
                    throw ValidationException::withMessages([
                        'status' => 'Pengiriman hanya dapat diselesaikan setelah status disetujui.',
                    ]);
                }

                // cegah stok diproses dua kali
                $alreadyMoved = ProductStockMovement::where('ref_type', Shippment::class)
                    ->where('ref_id', $shippment->id)
                    ->exists();

                if (! $alreadyMoved) {
                    foreach ($shippment->shippmentItems as $item) {
                        $sourceStock = ProductStock::lockForUpdate()->find($item->product_stock_id);

                        if (! $sourceStock) {
                            throw ValidationException::withMessages([
                                'status' => "Product stock untuk item ID {$item->id} tidak ditemukan.",
                            ]);
                        }

                        if ((int) $sourceStock->stock < (int) $item->quantity) {
                            $productName = $item->productStock?->productVariant?->name ?? 'Produk';
                            throw ValidationException::withMessages([
                                'status' => "Stok {$productName} di provinsi asal tidak mencukupi.",
                            ]);
                        }

                        $destinationStock = ProductStock::lockForUpdate()->firstOrCreate(
                            [
                                'product_variant_id' => $sourceStock->product_variant_id,
                                'province' => $shippment->province,
                            ],
                            [
                                'stock' => 0,
                            ]
                        );

                        $sourceStock->decrement('stock', $item->quantity);
                        $destinationStock->increment('stock', $item->quantity);

                        ProductStockMovement::create([
                            'province' => $sourceStock->province,
                            'product_stock_id' => $sourceStock->id,
                            'type' => 'Out',
                            'quantity' => $item->quantity,
                            'ref_type' => Shippment::class,
                            'ref_id' => $shippment->id,
                            'note' => 'Pengeluaran stok untuk pengiriman '.$shippment->shippment_code,
                        ]);

                        ProductStockMovement::create([
                            'province' => $destinationStock->province,
                            'product_stock_id' => $destinationStock->id,
                            'type' => 'In',
                            'quantity' => $item->quantity,
                            'ref_type' => Shippment::class,
                            'ref_id' => $shippment->id,
                            'note' => 'Penambahan stok dari pengiriman '.$shippment->shippment_code,
                        ]);
                    }
                }

                $shippment->received_at = now();
                $shippment->save();
            }

            DB::commit();

            return redirect()
                ->route('edit-shippment', $shippment->id)
                ->with('success', 'Status pengiriman berhasil diperbarui.');
        } catch (ValidationException $th) {
            DB::rollBack();
            save_log_error($th);

            return back()
                ->withErrors($th->errors())
                ->withInput();
        } catch (\Throwable $th) {
            DB::rollBack();
            save_log_error($th);

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui status pengiriman: '.$th->getMessage());
        }
    }
}
