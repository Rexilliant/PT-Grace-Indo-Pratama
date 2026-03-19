<?php

namespace App\Http\Controllers;
use App\Models\ProductStockMovement;
use App\Models\HistorySalePayment;
use App\Models\ProductStock;
use App\Models\Sale;
use Illuminate\Http\Request;
use Carbon\Carbon;
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
        return view('admin.add-laporan-penjualan', [
            'reportDate' => now()->format('Y-m-d'),
            'personResponsibleName' => Auth::user()?->name ?? '-',
            'provinceJsonUrl' => asset('assets/data/provinceAndCity.json'),
            'stocksByProvinceUrl' => route('admin.pemasaran-laporan-penjualan.stocks-by-province'),
        ]);
    }

    public function getStocksByProvince(Request $request)
    {
        $request->validate([
            'province' => ['required', 'string', 'max:255'],
        ]);

        $province = trim((string) $request->province);

        $stocks = ProductStock::query()
            ->with([
                'productVariant:id,product_id,sku,name,price',
                'productVariant.product:id,name',
            ])
            ->whereRaw('LOWER(TRIM(province)) = ?', [strtolower($province)])
            ->where('stock', '>', 0)
            ->orderBy('id')
            ->get()
            ->map(function ($stock) {
                return [
                    'id' => $stock->id,
                    'province' => $stock->province,
                    'stock' => (int) $stock->stock,
                    'sku' => $stock->productVariant?->sku ?? '-',
                    'product_name' => $stock->productVariant?->name ?? ($stock->productVariant?->product?->name ?? '-'),
                    'price' => (int) ($stock->productVariant?->price ?? 0),
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
            $customerProvince = trim((string) $request->customer_province);

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

                if ($stock->province !== $customerProvince) {
                    throw ValidationException::withMessages([
                        "items.$index.product_stock_id" => 'Barang tidak sesuai dengan provinsi yang dipilih.',
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

            $sale = Sale::create([
                'report_date' => now()->toDateString(),
                'sale_date' => $request->sale_date,
                'person_responsible_id' => Auth::id(),
                'sale_type' => $request->sale_type,
                'customer_province' => $customerProvince,
                'customer_city' => $request->customer_city,
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
            'personResponsible',
            'items.productStock.productVariant.product',
            'paymentHistories',
        ])->findOrFail($id);

        $initialItems = $sale->items->map(function ($item) {
            return [
                'product_stock_id' => $item->product_stock_id,
                'quantity' => $item->quantity,
                'discount' => $item->discount,
            ];
        })->values()->all();

        return view('admin.edit-laporan-penjualan', [
            'sale' => $sale,
            'reportDate' => \Carbon\Carbon::parse($sale->report_date)->format('Y-m-d'),
            'personResponsibleName' => $sale->personResponsible?->name ?? '-',
            'provinceJsonUrl' => asset('assets/data/provinceAndCity.json'),
            'stocksByProvinceUrl' => route('admin.pemasaran-laporan-penjualan.stocks-by-province'),
            'initialItems' => $initialItems,
            'currentPaidAmount' => (int) $sale->paymentHistories()->sum('amount'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $sale = Sale::with([
            'items',
            'paymentHistories',
        ])->findOrFail($id);

        $request->validate([
            'sale_date' => ['required', 'date'],
            'sale_type' => ['required', 'in:Perseorangan,Instansi,Pesanan'],
            'customer_province' => ['required', 'string', 'max:255'],
            'customer_city' => ['required', 'string', 'max:255'],
            'customer_address' => ['nullable', 'string'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_contact' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'payment_amount' => ['nullable', 'string'],
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

        return DB::transaction(function () use ($request, $sale, $itemsInput) {
            $newStockIds = $itemsInput
                ->pluck('product_stock_id')
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            $oldStockIds = $sale->items
                ->pluck('product_stock_id')
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            $allStockIds = $newStockIds
                ->merge($oldStockIds)
                ->unique()
                ->values();

            $stocks = ProductStock::query()
                ->with([
                    'productVariant:id,product_id,sku,name,price',
                    'productVariant.product:id,name',
                ])
                ->whereIn('id', $allStockIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $customerProvince = trim((string) $request->customer_province);

            // 1. Balikin stok lama dulu + movement IN rollback
            foreach ($sale->items as $oldItem) {
                $oldStock = $stocks->get((int) $oldItem->product_stock_id);

                if ($oldStock) {
                    $oldStock->increment('stock', (int) $oldItem->quantity);

                    ProductStockMovement::create([
                        'province' => $oldStock->province,
                        'product_stock_id' => $oldStock->id,
                        'type' => 'In',
                        'quantity' => (int) $oldItem->quantity,
                        'ref_type' => Sale::class,
                        'ref_id' => $sale->id,
                        'note' => 'Rollback stok karena sunting sale #' . $sale->id,
                    ]);
                }
            }

            // 2. Hapus item lama
            $sale->items()->delete();

            // 3. Validasi ulang item baru setelah stok dikembalikan
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

                if (trim((string) $stock->province) !== $customerProvince) {
                    throw ValidationException::withMessages([
                        "items.$index.product_stock_id" => 'Barang tidak sesuai dengan provinsi yang dipilih.',
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

            // 4. Hitung pembayaran
            $existingPaid = (int) $sale->paymentHistories()->sum('amount');
            $additionalPayment = $this->parseMoney($request->input('payment_amount', 0));

            if ($existingPaid > $grandTotal) {
                throw ValidationException::withMessages([
                    'items' => 'Total penjualan baru lebih kecil dari total pembayaran yang sudah tercatat. Sesuaikan item atau tangani refund secara terpisah.',
                ]);
            }

            if (($existingPaid + $additionalPayment) > $grandTotal) {
                throw ValidationException::withMessages([
                    'payment_amount' => 'Pembayaran tambahan melebihi sisa tagihan.',
                ]);
            }

            $paidAmount = $existingPaid + $additionalPayment;
            $debtAmount = max(0, $grandTotal - $paidAmount);
            $finalStatus = $debtAmount > 0 ? 'Terhutang' : 'Lunas';

            // 5. Update sale header
            $sale->update([
                'sale_date' => $request->sale_date,
                'sale_type' => $request->sale_type,
                'customer_province' => $customerProvince,
                'customer_city' => $request->customer_city,
                'customer_address' => $request->customer_address,
                'customer_name' => $request->customer_name,
                'customer_contact' => $request->customer_contact,
                'total_amount' => $grandTotal,
                'paid_amount' => $paidAmount,
                'debt_amount' => $debtAmount,
                'notes' => $request->notes,
                'status' => $finalStatus,
            ]);

            // 6. Simpan item baru + kurangi stok + movement OUT
            foreach ($normalizedItems as $item) {
                $sale->items()->create($item);

                $stock = $stocks->get($item['product_stock_id']);
                $stock->decrement('stock', $item['quantity']);

                ProductStockMovement::create([
                    'province' => $stock->province,
                    'product_stock_id' => $stock->id,
                    'type' => 'Out',
                    'quantity' => $item['quantity'],
                    'ref_type' => Sale::class,
                    'ref_id' => $sale->id,
                    'note' => 'Pengeluaran stok karena sunting sale #' . $sale->id,
                ]);
            }

            // 7. Simpan pembayaran tambahan ke history + lampirkan bukti pembayaran
            if ($request->hasFile('invoice') && $additionalPayment <= 0) {
                throw ValidationException::withMessages([
                    'payment_amount' => 'Isi tambahan pembayaran terlebih dahulu jika ingin mengunggah bukti pembayaran.',
                ]);
            }

            if ($additionalPayment > 0) {
                $paymentHistory = HistorySalePayment::create([
                    'sale_id' => $sale->id,
                    'payment_date' => now()->toDateString(),
                    'amount' => $additionalPayment,
                ]);

                if ($request->hasFile('invoice')) {
                    $paymentHistory
                        ->addMedia($request->file('invoice'))
                        ->toMediaCollection('payment_proof');
                }
            }

            return redirect()
                ->route('admin.pemasaran-laporan-penjualan')
                ->with('success', 'Laporan penjualan berhasil diperbarui.');
        });
    }

    public function historyPayment($id)
    {
        $sale = Sale::with([
            'personResponsible',
            'items.productStock.productVariant.product',
            'paymentHistories' => function ($query) {
                $query->orderBy('payment_date')->orderBy('id');
            },
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