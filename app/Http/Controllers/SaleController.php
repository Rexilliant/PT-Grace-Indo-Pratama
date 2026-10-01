<?php

namespace App\Http\Controllers;

use App\Models\HistorySalePayment;
use App\Models\ProductStock;
use App\Models\ProductStockMovement;
use App\Models\Sale;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Facades\Excel;

class SaleController extends Controller
{
    public function export(Request $request)
    {
        $q = Sale::query()
            ->with(['warehouse', 'personResponsible', 'items.productStock.productVariant.product', 'paymentHistories.createdBy', 'media'])
            ->latest();

        if ($request->filled('name')) {
            $q->whereHas('personResponsible', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
            });
        }

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        if ($request->filled('province')) {
            $q->where('customer_province', 'like', '%' . $request->province . '%');
        }

        if ($request->filled('date_from')) {
            $q->whereDate('sale_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $q->whereDate('sale_date', '<=', $request->date_to);
        }

        $rows = collect();
        $mergeRanges = [];
        $currentRow = 2;

        $q->get()->each(function ($s) use ($rows, &$mergeRanges, &$currentRow) {
            $startRow = $currentRow;

            $paymentHistories = $s->paymentHistories
                ->map(function ($history) {
                    return ($history->payment_date ? Carbon::parse($history->payment_date)->format('d/m/Y') : '-') . ' - Rp ' . number_format((int) $history->amount, 0, ',', '.') . ' (' . ($history->createdBy->name ?? '-') . ')';
                })
                ->implode(' | ');

            $deliveryProof = $s->getFirstMedia('delivery_proof');

            foreach ($s->items as $item) {
                $stock = $item->productStock;
                $variant = $stock?->productVariant;
                $product = $variant?->product;

                $rows->push([
                    'ID Penjualan' => $s->id,
                    'Tanggal Penjualan' => $s->sale_date ? Carbon::parse($s->sale_date)->format('d/m/Y') : '-',
                    'Penanggung Jawab' => $s->personResponsible->name ?? '-',
                    'Gudang' => $s->warehouse?->name ?? '-',
                    'Jenis Penjualan' => $s->sale_type ?? '-',
                    'Tipe Stok' => $s->stock_type === 'po' ? 'Pre-Order (PO)' : 'Ready Stock',
                    'Nama Pembeli' => $s->customer_name ?? '-',
                    'Kontak Pembeli' => $s->customer_contact ?? '-',
                    'Provinsi Pembeli' => $s->customer_province ?? '-',
                    'Kota Pembeli' => $s->customer_city ?? '-',
                    'Alamat Pembeli' => $s->customer_address ?? '-',
                    'Total Amount' => $s->total_amount ?? 0,
                    'Paid Amount' => $s->paid_amount ?? 0,
                    'Debt Amount' => $s->debt_amount ?? 0,
                    'Status' => $s->status ?? '-',
                    'Catatan' => $s->notes ?? '-',
                    'Riwayat Pembayaran' => $paymentHistories ?: '-',
                    'BST / Bukti Serah Terima' => $deliveryProof?->file_name ?? '-',
                    'SKU' => $variant->sku ?? '-',
                    'Produk' => $product->name ?? ($variant->name ?? '-'),
                    'Variant' => $variant->name ?? '-',
                    'Qty Ordered' => $item->quantity ?? 0,
                    'Qty Fulfilled' => $item->fulfilled_quantity ?? 0,
                    'Harga Satuan' => $item->price ?? 0,
                    'Diskon' => $item->discount ?? 0,
                    'Subtotal' => $item->subtotal ?? 0,
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
            public function __construct(private $rows, private $mergeRanges)
            {
            }

            public function collection()
            {
                return $this->rows;
            }

            public function headings(): array
            {
                return [
                'ID Penjualan',
                'Tanggal Penjualan',
                'Penanggung Jawab',
                'Gudang',
                'Jenis Penjualan',
                'Tipe Stok',
                'Nama Pembeli',
                'Kontak Pembeli',
                'Provinsi Pembeli',
                'Kota Pembeli',
                'Alamat Pembeli',
                'Total Amount',
                'Paid Amount',
                'Debt Amount',
                'Status',
                'Catatan',
                'Riwayat Pembayaran',
                'BST / Bukti Serah Terima',
                'SKU',
                'Produk',
                'Variant',
                'Qty Ordered',
                'Qty Fulfilled',
                'Harga Satuan',
                'Diskon',
                'Subtotal',
                ];
            }

            public function registerEvents(): array
            {
                return [
                    AfterSheet::class => function (AfterSheet $event) {
                        foreach ($this->mergeRanges as $range) {
                            foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R'] as $column) {
                                $event->sheet->mergeCells($column . $range['start'] . ':' . $column . $range['end']);
                            }
                        }
                    },
                ];
            }
        };

        return Excel::download($export, 'Penjualan_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function index(Request $request)
    {
        $q = Sale::query()
            ->with(['personResponsible', 'items.productStock.productVariant.product'])
            ->latest();

        if ($request->filled('name')) {
            $q->whereHas('personResponsible', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
            });
        }

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        if ($request->filled('province')) {
            $q->where('customer_province', 'like', '%' . $request->province . '%');
        }

        if ($request->filled('date_from')) {
            $q->whereDate('sale_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $q->whereDate('sale_date', '<=', $request->date_to);
        }

        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;

        $sales = $q->orderBy('sale_date', 'asc')->paginate($perPage)->withQueryString();

        $statuses = Sale::query()->select('status')->whereNotNull('status')->distinct()->orderBy('status')->pluck('status');

        return view('admin.sales.pemasaran-laporan-penjualan', compact('sales', 'statuses'));
    }

    public function create()
    {
        $warehouseId = auth()->user()->employee?->warehouse_id;

        if ($warehouseId) {
            $warehouses = Warehouse::where('id', $warehouseId)->where('type', 'pemasaran')->get();
        } else {
            $warehouses = Warehouse::where('type', 'pemasaran')->get();
        }

        return view('admin.sales.add-laporan-penjualan', [
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
            ->with(['warehouse:id,name,province,city', 'productVariant:id,product_id,sku,name,price,unit', 'productVariant.product:id,name'])
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
        $dpValue = $this->parseMoney($request->input('down_payment', 0));

        $request->validate(
            [
                'sale_date' => ['required', 'date'],
                'sale_type' => ['required', 'in:Perseorangan,Instansi,Pesanan'],
                'stock_type' => ['required', 'in:ready,po'],
                'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
                'customer_province' => ['required', 'string', 'max:255'],
                'customer_city' => ['required', 'string', 'max:255'],
                'customer_address' => ['required', 'string'],
                'customer_name' => ['required', 'string', 'max:255'],
                'customer_contact' => ['required', 'string', 'max:255'],
                'status' => ['required', 'in:Lunas,Terhutang'],
                'down_payment' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request, $dpValue) {
                        if ($request->status === 'Terhutang' && $dpValue <= 0) {
                            $fail('Down Payment (DP) wajib diisi lebih dari 0 jika status Terhutang.');
                        }
                    },
                ],
                'notes' => ['nullable', 'string'],
                'invoice' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:3072'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.product_stock_id' => ['required', 'integer', 'exists:product_stocks,id'],
                'items.*.quantity' => ['required', 'integer', 'min:1'],
                'items.*.discount' => ['nullable', 'string'],
            ],
            [
                'sale_date.required' => 'Tanggal penjualan wajib dipilih.',
                'stock_type.required' => 'Tipe stok (Ready / PO) wajib dipilih.',
                'customer_name.required' => 'Nama pembeli wajib diisi.',
                'customer_contact.required' => 'Nomor kontak pembeli wajib diisi.',
                'customer_address.required' => 'Alamat lengkap wajib diisi.',
                'invoice.required' => 'Bukti pembayaran wajib diunggah.',
                'invoice.mimes' => 'Format bukti bayar harus PNG, JPG, JPEG, atau PDF.',
                'items.required' => 'Minimal harus ada 1 barang yang terjual.',
            ],
        );

        $itemsInput = collect($request->input('items', []))->values();
        $isPo = $request->stock_type === 'po';

        return DB::transaction(function () use ($request, $itemsInput, $dpValue, $isPo) {
            $stockIds = $itemsInput->pluck('product_stock_id')->map(fn($id) => (int) $id)->unique()->values();
            $warehouseId = (int) $request->warehouse_id;

            $stocks = ProductStock::query()
                ->with(['productVariant:id,product_id,sku,name,price'])
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

                if (!$stock || (int) $stock->warehouse_id !== $warehouseId) {
                    throw ValidationException::withMessages([
                        "items.$index.product_stock_id" => 'Barang tidak valid atau tidak ada di gudang ini.',
                    ]);
                }

                // Hanya validasi stok kalau Ready Stock
                if (!$isPo && $stock->stock < $quantity) {
                    throw ValidationException::withMessages([
                        "items.$index.quantity" => "Stok {$stock->productVariant?->name} tidak cukup (Tersedia: {$stock->stock}).",
                    ]);
                }

                $price = (int) ($stock->productVariant?->price ?? 0);
                $finalUnitPrice = max(0, $price - $discount);
                $subtotal = $finalUnitPrice * $quantity;

                $normalizedItems[] = [
                    'product_stock_id' => $stock->id,
                    'quantity' => $quantity,
                    'fulfilled_quantity' => $isPo ? 0 : $quantity,
                    'price' => $price,
                    'discount' => $discount,
                    'subtotal' => $subtotal,
                ];

                $grandTotal += $subtotal;
            }

            if ($request->status === 'Lunas') {
                $paidAmount = $grandTotal;
            } else {
                $paidAmount = min($dpValue, $grandTotal);
            }

            $debtAmount = max(0, $grandTotal - $paidAmount);
            $finalStatus = $debtAmount > 0 ? 'Terhutang' : 'Lunas';

            $sale = Sale::create([
                'report_date' => now()->toDateString(),
                'sale_date' => $request->sale_date,
                'person_responsible_id' => Auth::id(),
                'updated_by' => Auth::id(),
                'warehouse_id' => $warehouseId,
                'sale_type' => $request->sale_type,
                'stock_type' => $request->stock_type,
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

                // Hanya potong stok kalau Ready Stock
                if (!$isPo) {
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
                        'note' => 'Penjualan Ready #' . $sale->id,
                    ]);
                }
            }

            if ($paidAmount > 0) {
                $paymentHistory = HistorySalePayment::create([
                    'sale_id' => $sale->id,
                    'created_by' => Auth::id(),
                    'payment_date' => now()->toDateString(),
                    'amount' => $paidAmount,
                ]);

                if ($request->hasFile('invoice')) {
                    $paymentHistory->addMedia($request->file('invoice'))->toMediaCollection('payment_proof');
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

        return view('admin.sales.edit-laporan-penjualan', [
            'sale' => $sale,
            'reportDate' => Carbon::parse($sale->report_date)->format('Y-m-d'),
            'personResponsibleName' => $sale->personResponsible?->name ?? '-',
            'currentPaidAmount' => (int) $sale->paymentHistories()->sum('amount'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $sale = Sale::with('items')->findOrFail($id);
        $isPaidOff = $sale->status === 'Lunas' || (int) $sale->debt_amount <= 0;

        // Jika sudah lunas, hanya boleh update catatan
        if ($isPaidOff) {
            $request->validate([
                'notes' => ['nullable', 'string', 'max:1000'],
            ]);

            $sale->update([
                'notes' => $request->input('notes'),
                'updated_by' => Auth::id(),
            ]);

            return redirect()
                ->route('admin.pemasaran-laporan-penjualan.edit', $sale->id)
                ->with('success', 'Catatan laporan berhasil diperbarui.');
        }

        // Validasi untuk yang masih terhutang (payment & invoice sekarang optional)
        $rules = [
            'payment_date' => ['nullable', 'date'],
            'payment_amount' => ['nullable', 'string'],
            'invoice' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:3072'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        if ($sale->stock_type === 'po') {
            $rules['fulfill'] = ['nullable', 'array'];
            $rules['fulfill.*'] = ['nullable', 'integer', 'min:0'];
        }

        $request->validate($rules, [
            'invoice.mimes' => 'Bukti pembayaran harus berupa PNG, JPG, JPEG, atau PDF.',
            'invoice.max' => 'Ukuran bukti pembayaran maksimal 3 MB.',
        ]);

        return DB::transaction(function () use ($request, $sale) {

            // === 1. Proses Fulfill PO (jika ada) ===
            if ($sale->stock_type === 'po' && $request->has('fulfill')) {
                $fulfillInput = $request->input('fulfill', []);
                $fulfillmentDate = $request->input('fulfillment_date') ?: now()->toDateString();

                $stockIds = $sale->items->pluck('product_stock_id')->unique()->values();
                $stocks = ProductStock::query()
                    ->whereIn('id', $stockIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($sale->items as $item) {
                    $addQty = (int) ($fulfillInput[$item->id] ?? 0);
                    if ($addQty <= 0)
                        continue;

                    $remaining = max(0, $item->quantity - $item->fulfilled_quantity);

                    if ($addQty > $remaining) {
                        throw ValidationException::withMessages([
                            "fulfill.{$item->id}" => "Qty fulfill melebihi sisa PO (sisa: {$remaining}).",
                        ]);
                    }

                    $stock = $stocks->get($item->product_stock_id);

                    if (!$stock || $stock->stock < $addQty) {
                        throw ValidationException::withMessages([
                            "fulfill.{$item->id}" => 'Stok tidak cukup untuk item ini (tersedia: ' . ($stock->stock ?? 0) . ').',
                        ]);
                    }

                    // Potong stok
                    $stock->decrement('stock', $addQty);

                    ProductStockMovement::create([
                        'warehouse_id' => $stock->warehouse_id,
                        'province' => $stock->province,
                        'product_stock_id' => $stock->id,
                        'type' => 'Out',
                        'quantity' => $addQty,
                        'ref_type' => Sale::class,
                        'ref_id' => $sale->id,
                        'note' => 'Fulfill PO #' . $sale->id . ' item #' . $item->id,
                    ]);

                    // Update fulfilled_quantity
                    $item->increment('fulfilled_quantity', $addQty);

                    // === CATAT HISTORY FULFILL ===
                    \App\Models\SaleItemFulfillment::create([
                        'sale_id' => $sale->id,
                        'sale_item_id' => $item->id,
                        'product_stock_id' => $item->product_stock_id,
                        'quantity' => $addQty,
                        'fulfillment_date' => $fulfillmentDate,
                        'created_by' => Auth::id(),
                        'note' => 'Pemenuhan PO',
                    ]);
                }
            }

            // === 2. Proses Pembayaran Cicilan (sekarang optional) ===
            $additionalPayment = $this->parseMoney($request->input('payment_amount'));

            if ($additionalPayment > 0) {
                $existingPaid = (int) $sale->paymentHistories()->sum('amount');
                $newPaid = $existingPaid + $additionalPayment;

                if ($newPaid > (int) $sale->total_amount) {
                    $remainingDebt = max(0, (int) $sale->total_amount - $existingPaid);
                    throw ValidationException::withMessages([
                        'payment_amount' => 'Nominal cicilan melebihi sisa tagihan (Sisa: Rp ' . number_format($remainingDebt, 0, ',', '.') . ').',
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
                    'payment_date' => $request->input('payment_date') ?: now()->toDateString(),
                    'amount' => $additionalPayment,
                ]);

                // Upload hanya kalau ada file
                if ($request->hasFile('invoice')) {
                    $paymentHistory->addMedia($request->file('invoice'))->toMediaCollection('payment_proof');
                }
            } else {
                // Tidak ada pembayaran, cukup update notes saja
                $sale->update([
                    'notes' => $request->input('notes'),
                    'updated_by' => Auth::id(),
                ]);
            }

            return redirect()
                ->route('admin.pemasaran-laporan-penjualan.edit', $sale->id)
                ->with('success', 'Perubahan berhasil disimpan.');
        });
    }

    public function destroy($id)
    {
        $sale = Sale::with(['items.productStock.productVariant.product', 'paymentHistories'])->findOrFail($id);

        DB::transaction(function () use ($sale) {
            $stockIds = $sale->items->pluck('product_stock_id')->filter()->map(fn($id) => (int) $id)->unique()->values();

            $stocks = ProductStock::query()
                ->whereIn('id', $stockIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($sale->items as $item) {
                // Hanya kembalikan stok yang sudah di-fulfill
                $qtyToReturn = (int) $item->fulfilled_quantity;

                if ($qtyToReturn <= 0) {
                    continue;
                }

                $stock = $stocks->get((int) $item->product_stock_id);
                if (!$stock) {
                    continue;
                }

                $stock->increment('stock', $qtyToReturn);

                ProductStockMovement::create([
                    'warehouse_id' => $stock->warehouse_id,
                    'province' => $stock->province,
                    'product_stock_id' => $stock->id,
                    'type' => 'In',
                    'quantity' => $qtyToReturn,
                    'ref_type' => Sale::class,
                    'ref_id' => $sale->id,
                    'note' => 'Pengembalian stok karena hapus sale #' . $sale->id,
                ]);
            }

            $sale->update([
                'deleted_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $sale->paymentHistories()->delete();
            $sale->items()->delete();
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
            'fulfillments.saleItem.productStock.productVariant',
            'fulfillments.createdBy',
        ])->findOrFail($id);

        return view('admin.sales.history-pembayaran-penjualan', compact('sale'));
    }

    public function uploadDeliveryProof(Request $request, $id)
    {
        $sale = Sale::findOrFail($id);

        if ($sale->status !== 'Lunas' && (int) $sale->debt_amount > 0) {
            return back()->withErrors(['delivery_proof' => 'Gagal: Bukti Serah Terima hanya bisa diunggah setelah status Lunas.']);
        }

        $request->validate(
            [
                'delivery_proof' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:3072'],
            ],
            [
                'delivery_proof.required' => 'File bukti serah terima wajib dipilih.',
                'delivery_proof.mimes' => 'Format file harus PNG, JPG, JPEG, atau PDF.',
                'delivery_proof.max' => 'Ukuran file maksimal adalah 3 MB.',
            ],
        );

        try {
            if ($request->hasFile('delivery_proof')) {
                $sale->addMediaFromRequest('delivery_proof')->toMediaCollection('delivery_proof');
            }

            return back()->with('success', 'Bukti serah terima barang (BST) berhasil diunggah.');
        } catch (\Exception $e) {
            return back()->withErrors(['delivery_proof' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
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

        return view('admin.sales.invoice-penjualan', compact('sale'));
    }

    public function pemenuhanPo($id)
    {
        $sale = Sale::with([
            'personResponsible',
            'warehouse',
            'items.productStock.productVariant.product',
            'fulfillments.saleItem.productStock.productVariant',
            'fulfillments.createdBy',
        ])->findOrFail($id);

        // Hanya boleh dibuka kalau tipenya PO
        if ($sale->stock_type !== 'po') {
            return redirect()
                ->route('admin.pemasaran-laporan-penjualan.history-pembayaran', $sale->id)
                ->with('error', 'Halaman ini hanya tersedia untuk transaksi Pre-Order (PO).');
        }

        return view('admin.sales.pemenuhan-po', compact('sale'));
    }
}