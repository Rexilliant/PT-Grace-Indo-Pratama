<?php

namespace App\Http\Controllers;
use App\Models\ProductStockMovement;
use App\Models\HistorySalePayment;
use App\Models\ProductStock;
use App\Models\Sale;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with([
            'personResponsible',
            'items.productStock.productVariant.product',
        ])
            ->latest()
            ->paginate(10);

        return view('admin.pemasaran-laporan-penjualan', compact('sales'));
    }

    public function create()
    {
        $warehouses = Warehouse::query()
            ->orderBy('name')
            ->get(['id', 'name', 'province', 'city']);

        return view('admin.add-laporan-penjualan', [
            'reportDate' => now()->format('Y-m-d'),
            'personResponsibleName' => Auth::user()?->name ?? '-',
            'provinceJsonUrl' => asset('assets/data/provinceAndCity.json'),
            'stocksByWarehouseUrl' => route('admin.pemasaran-laporan-penjualan.stocks-by-warehouse'),
            'warehouses' => $warehouses,
        ]);
    }

    public function getStocksByWarehouse(Request $request)
    {
        $request->validate([
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
        ]);

        $warehouseId = (int) $request->warehouse_id;

        $stocks = ProductStock::query()
            ->with([
                'warehouse:id,name,province,city',
                'productVariant:id,product_id,sku,name,price,unit',
                'productVariant.product:id,name',
            ])
            ->where('warehouse_id', $warehouseId)
            ->where('stock', '>', 0)
            ->orderBy('id')
            ->get()
            ->map(function ($stock) {
                return [
                    'id' => $stock->id,
                    'warehouse_id' => $stock->warehouse_id,
                    'warehouse_name' => $stock->warehouse?->name ?? '-',
                    'warehouse_province' => $stock->warehouse?->province ?? '-',
                    'warehouse_city' => $stock->warehouse?->city ?? '-',
                    'stock' => (int) $stock->stock,
                    'sku' => $stock->productVariant?->sku ?? '-',
                    'product_name' => $stock->productVariant?->name ?? ($stock->productVariant?->product?->name ?? '-'),
                    'price' => (int) ($stock->productVariant?->price ?? 0),
                    'unit' => $stock->productVariant?->unit ?? '-',
                ];
            })
            ->values();

        return response()->json([
            'data' => $stocks,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_date' => ['required', 'date'],
            'sale_type' => ['required', 'in:Perseorangan,Instansi,Pesanan'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'customer_province' => ['required', 'string', 'max:255'],
            'customer_city' => ['required', 'string', 'max:255'],
            'customer_address' => ['nullable', 'string'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_contact' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:Lunas,Terhutang'],
            'down_payment' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'invoice' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:3072'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_stock_id' => ['required', 'integer', 'exists:product_stocks,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.discount' => ['nullable', 'string'],
        ]);

        $itemsInput = collect($request->input('items', []))->values();

        if ($itemsInput->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Minimal harus ada 1 barang.',
            ]);
        }

        return DB::transaction(function () use ($request, $itemsInput) {
            $stockIds = $itemsInput
                ->pluck('product_stock_id')
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            $warehouseId = (int) $request->warehouse_id;

            $stocks = ProductStock::query()
                ->with([
                    'productVariant:id,product_id,sku,name,price',
                    'productVariant.product:id,name',
                ])
                ->whereIn('id', $stockIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $normalizedItems = [];
            $grandTotal = 0;

            foreach ($itemsInput as $index => $item) {
                $productStockId = (int) ($item['product_stock_id'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 0);
                $discount = $this->parseMoney($item['discount'] ?? 0);

                $stock = $stocks->get($productStockId);

                if (!$stock) {
                    throw ValidationException::withMessages([
                        "items.$index.product_stock_id" => 'Barang tidak ditemukan.',
                    ]);
                }

                if ((int) $stock->warehouse_id !== $warehouseId) {
                    throw ValidationException::withMessages([
                        "items.$index.product_stock_id" => 'Barang tidak sesuai dengan gudang yang dipilih.',
                    ]);
                }

                if ($quantity < 1) {
                    throw ValidationException::withMessages([
                        "items.$index.quantity" => 'Jumlah terjual minimal 1.',
                    ]);
                }

                if ($stock->stock < $quantity) {
                    throw ValidationException::withMessages([
                        "items.$index.quantity" => "Stok untuk {$stock->productVariant?->name} tidak cukup. Stok tersedia: {$stock->stock}.",
                    ]);
                }

                $price = (int) ($stock->productVariant?->price ?? 0);
                $finalUnitPrice = max(0, $price - $discount);
                $subtotal = $finalUnitPrice * $quantity;

                $normalizedItems[] = [
                    'product_stock_id' => $stock->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'discount' => $discount,
                    'subtotal' => $subtotal,
                ];

                $grandTotal += $subtotal;
            }

            $inputStatus = $request->input('status');
            $downPayment = $this->parseMoney($request->input('down_payment', 0));

            if ($inputStatus === 'Lunas') {
                $paidAmount = $grandTotal;
            } else {
                $paidAmount = min($downPayment, $grandTotal);
            }

            $debtAmount = max(0, $grandTotal - $paidAmount);
            $finalStatus = $debtAmount > 0 ? 'Terhutang' : 'Lunas';

            $warehouseId = (int) $request->input('warehouse_id');
            if (!$warehouseId) {
                throw ValidationException::withMessages([
                    'warehouse_id' => 'Gudang wajib dipilih.',
                ]);
            }

            $sale = Sale::create([
                'report_date' => now()->toDateString(),
                'sale_date' => $request->sale_date,
                'person_responsible_id' => Auth::id(),
                'updated_by' => Auth::id(),
                'warehouse_id' => $warehouseId,
                'sale_type' => $request->sale_type,
                'customer_province' => trim((string) $request->customer_province),
                'customer_city' => trim((string) $request->customer_city),
                'customer_address' => $request->customer_address,
                'customer_name' => $request->customer_name,
                'customer_contact' => $request->customer_contact,
                'total_amount' => $grandTotal,
                'paid_amount' => $paidAmount,
                'debt_amount' => $debtAmount,
                'notes' => $request->notes,
                'status' => $finalStatus,
            ]);

            foreach ($normalizedItems as $item) {
                $sale->items()->create($item);

                $stock = $stocks->get($item['product_stock_id']);
                $stock->decrement('stock', $item['quantity']);

                ProductStockMovement::create([
                    'warehouse_id' => $stock->warehouse_id,
                    'province' => $stock->province,
                    'product_stock_id' => $stock->id,
                    'type' => 'Out',
                    'quantity' => $item['quantity'],
                    'ref_type' => Sale::class,
                    'ref_id' => $sale->id,
                    'note' => 'Pengeluaran stok karena penjualan sale #' . $sale->id,
                ]);
            }

            if ($paidAmount > 0) {
                $paymentHistory = HistorySalePayment::create([
                    'sale_id' => $sale->id,
                    'created_by' => Auth::id(),
                    'payment_date' => now()->toDateString(),
                    'amount' => $paidAmount,
                ]);

                if ($request->hasFile('invoice')) {
                    $paymentHistory
                        ->addMedia($request->file('invoice'))
                        ->toMediaCollection('payment_proof');
                }
            }

            return redirect()
                ->route('admin.pemasaran-laporan-penjualan')
                ->with('success', 'Laporan penjualan berhasil disimpan.');
        });
    }

    public function edit($id)
    {
        $sale = Sale::with([
            'warehouse',
            'personResponsible',
            'items.productStock.productVariant.product',
            'paymentHistories',
        ])->findOrFail($id);

        return view('admin.edit-laporan-penjualan', [
            'sale' => $sale,
            'reportDate' => Carbon::parse($sale->report_date)->format('Y-m-d'),
            'personResponsibleName' => $sale->personResponsible?->name ?? '-',
            'currentPaidAmount' => (int) $sale->paymentHistories()->sum('amount'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $sale = Sale::with([
            'paymentHistories',
        ])->findOrFail($id);

        $request->validate([
            'payment_amount' => ['required', 'string'],
            'invoice' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:3072'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'payment_amount.required' => 'Nominal cicilan wajib diisi.',
            'invoice.required' => 'Bukti pembayaran wajib diupload.',
            'invoice.file' => 'Bukti pembayaran tidak valid.',
            'invoice.mimes' => 'Bukti pembayaran harus berupa PNG, JPG, JPEG, atau PDF.',
            'invoice.max' => 'Ukuran bukti pembayaran maksimal 3 MB.',
        ]);

        return DB::transaction(function () use ($request, $sale) {
            if ($sale->status === 'Lunas' || (int) $sale->debt_amount <= 0) {
                throw ValidationException::withMessages([
                    'payment_amount' => 'Transaksi ini sudah lunas, tidak bisa menambah pembayaran lagi.',
                ]);
            }

            $additionalPayment = $this->parseMoney($request->input('payment_amount'));

            if ($additionalPayment <= 0) {
                throw ValidationException::withMessages([
                    'payment_amount' => 'Nominal cicilan harus lebih dari 0.',
                ]);
            }

            if (!$request->hasFile('invoice')) {
                throw ValidationException::withMessages([
                    'invoice' => 'Bukti pembayaran wajib diupload.',
                ]);
            }

            $existingPaid = (int) $sale->paymentHistories()->sum('amount');
            $newPaid = $existingPaid + $additionalPayment;

            if ($newPaid > (int) $sale->total_amount) {
                $remainingDebt = max(0, (int) $sale->total_amount - $existingPaid);

                throw ValidationException::withMessages([
                    'payment_amount' => 'Nominal cicilan melebihi sisa tagihan. Maksimal pembayaran yang bisa ditambahkan adalah Rp ' . number_format($remainingDebt, 0, ',', '.') . '.',
                ]);
            }

            $debtAmount = max(0, (int) $sale->total_amount - $newPaid);
            $finalStatus = $debtAmount <= 0 ? 'Lunas' : 'Terhutang';

            $sale->update([
                'paid_amount' => $newPaid,
                'debt_amount' => $debtAmount,
                'status' => $finalStatus,
                'notes' => $request->input('notes'),
                'updated_by' => Auth::id(),
            ]);

            $paymentHistory = HistorySalePayment::create([
                'sale_id' => $sale->id,
                'created_by' => Auth::id(),
                'payment_date' => now()->toDateString(),
                'amount' => $additionalPayment,
            ]);

            $paymentHistory
                ->addMedia($request->file('invoice'))
                ->toMediaCollection('payment_proof');

            return redirect()
                ->route('admin.pemasaran-laporan-penjualan.edit', $sale->id)
                ->with('success', $finalStatus === 'Lunas'
                    ? 'Pembayaran berhasil ditambahkan. Transaksi sekarang sudah lunas.'
                    : 'Pembayaran berhasil ditambahkan.');
        });
    }

    public function destroy($id)
    {
        $sale = Sale::with([
            'items.productStock.productVariant.product',
            'paymentHistories',
        ])->findOrFail($id);

        DB::transaction(function () use ($sale) {
            $stockIds = $sale->items
                ->pluck('product_stock_id')
                ->filter()
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            $stocks = ProductStock::query()
                ->whereIn('id', $stockIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($sale->items as $item) {
                $stock = $stocks->get((int) $item->product_stock_id);

                if (!$stock) {
                    continue;
                }

                $stock->increment('stock', (int) $item->quantity);

                ProductStockMovement::create([
                    'warehouse_id' => $stock->warehouse_id,
                    'province' => $stock->province,
                    'product_stock_id' => $stock->id,
                    'type' => 'In',
                    'quantity' => (int) $item->quantity,
                    'ref_type' => Sale::class,
                    'ref_id' => $sale->id,
                    'note' => 'Pengembalian stok karena hapus sale #' . $sale->id,
                ]);
            }

            // soft delete history pembayaran dulu
            $sale->paymentHistories()->delete();

            // soft delete item penjualan
            $sale->items()->delete();

            // soft delete sale utama
            $sale->delete();
        });

        return redirect()
            ->route('admin.pemasaran-laporan-penjualan')
            ->with('success', 'Laporan penjualan berhasil dihapus dan stok dikembalikan.');
    }

    public function historyPayment($id)
    {
        $sale = Sale::with([
            'warehouse',
            'personResponsible',
            'updatedBy',
            'items.productStock.productVariant.product',
            'paymentHistories.createdBy',
        ])->findOrFail($id);

        return view('admin.history-pembayaran-penjualan', compact('sale'));
    }

    private function parseMoney($value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        return (int) preg_replace('/[^\d]/', '', (string) $value);
    }

    public function invoice($id)
    {
        $sale = Sale::with([
            'personResponsible',
            'items.productStock.productVariant.product',
            'paymentHistories' => function ($query) {
                $query->orderBy('payment_date')->orderBy('id');
            },
        ])->findOrFail($id);

        return view('admin.invoice-penjualan', compact('sale'));
    }
}