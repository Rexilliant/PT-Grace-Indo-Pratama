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

                    'Qty' => $item->quantity ?? 0,

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
            public function __construct(private $rows, private $mergeRanges) {}

            public function collection()
            {
                return $this->rows;
            }

            public function headings(): array
            {
                return ['ID Penjualan', 'Tanggal Penjualan', 'Penanggung Jawab', 'Gudang', 'Jenis Penjualan', 'Nama Pembeli', 'Kontak Pembeli', 'Provinsi Pembeli', 'Kota Pembeli', 'Alamat Pembeli', 'Total Amount', 'Paid Amount', 'Debt Amount', 'Status', 'Catatan', 'Riwayat Pembayaran', 'BST / Bukti Serah Terima', 'SKU', 'Produk', 'Variant', 'Qty', 'Harga Satuan', 'Diskon', 'Subtotal'];
            }

            public function registerEvents(): array
            {
                return [
                    AfterSheet::class => function (AfterSheet $event) {
                        foreach ($this->mergeRanges as $range) {
                            foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'] as $column) {
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
        // 1. Bersihkan nilai Down Payment dari format mata uang menjadi integer murni
        $dpValue = $this->parseMoney($request->input('down_payment', 0));

        // 2. Validasi Input
        $request->validate(
            [
                'sale_date' => ['required', 'date'],
                'sale_type' => ['required', 'in:Perseorangan,Instansi,Pesanan'],
                'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
                'customer_province' => ['required', 'string', 'max:255'],
                'customer_city' => ['required', 'string', 'max:255'],
                'customer_address' => ['required', 'string'], // Wajib diisi
                'customer_name' => ['required', 'string', 'max:255'],
                'customer_contact' => ['required', 'string', 'max:255'],
                'status' => ['required', 'in:Lunas,Terhutang'],
                'down_payment' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request, $dpValue) {
                        // Validasi: Jika Terhutang, DP harus lebih dari 0
                        if ($request->status === 'Terhutang' && $dpValue <= 0) {
                            $fail('Down Payment (DP) wajib diisi lebih dari 0 jika status Terhutang.');
                        }
                    },
                ],
                'notes' => ['nullable', 'string'],
                'invoice' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:3072'], // Bukti Bayar Wajib

                'items' => ['required', 'array', 'min:1'],
                'items.*.product_stock_id' => ['required', 'integer', 'exists:product_stocks,id'],
                'items.*.quantity' => ['required', 'integer', 'min:1'],
                'items.*.discount' => ['nullable', 'string'],
            ],
            [
                // Pesan Error Kustom (Muncul di peringatan kolom)
                'sale_date.required' => 'Tanggal penjualan wajib dipilih.',
                'customer_name.required' => 'Nama pembeli wajib diisi.',
                'customer_contact.required' => 'Nomor kontak pembeli wajib diisi.',
                'customer_address.required' => 'Alamat lengkap wajib diisi.',
                'invoice.required' => 'Bukti pembayaran wajib diunggah.',
                'invoice.mimes' => 'Format bukti bayar harus PNG, JPG, JPEG, atau PDF.',
                'items.required' => 'Minimal harus ada 1 barang yang terjual.',
            ],
        );

        $itemsInput = collect($request->input('items', []))->values();

        // 3. Eksekusi Database Transaction
        return DB::transaction(function () use ($request, $itemsInput, $dpValue) {
            $stockIds = $itemsInput->pluck('product_stock_id')->map(fn($id) => (int) $id)->unique()->values();
            $warehouseId = (int) $request->warehouse_id;

            // Ambil data stok dan kunci untuk update (Pencegahan Race Condition)
            $stocks = ProductStock::query()
                ->with(['productVariant:id,product_id,sku,name,price'])
                ->whereIn('id', $stockIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $normalizedItems = [];
            $grandTotal = 0;

            // Validasi item dan hitung total
            foreach ($itemsInput as $index => $item) {
                $productStockId = (int) ($item['product_stock_id'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 0);
                $discount = $this->parseMoney($item['discount'] ?? 0);

                $stock = $stocks->get($productStockId);

                if (!$stock || (int) $stock->warehouse_id !== $warehouseId) {
                    throw ValidationException::withMessages(["items.$index.product_stock_id" => 'Barang tidak valid atau tidak ada di gudang ini.']);
                }

                if ($stock->stock < $quantity) {
                    throw ValidationException::withMessages(["items.$index.quantity" => "Stok {$stock->productVariant?->name} tidak cukup (Tersedia: {$stock->stock})."]);
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

            // Hitung Nominal Pembayaran dan Hutang
            if ($request->status === 'Lunas') {
                $paidAmount = $grandTotal;
            } else {
                // Pastikan dibayar tidak melebihi total pesanan
                $paidAmount = min($dpValue, $grandTotal);
            }

            $debtAmount = max(0, $grandTotal - $paidAmount);
            $finalStatus = $debtAmount > 0 ? 'Terhutang' : 'Lunas';

            // 4. Simpan Data Penjualan Utama
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

            // 5. Simpan Items & Kurangi Stok
            foreach ($normalizedItems as $item) {
                $sale->items()->create($item);

                $stock = $stocks->get($item['product_stock_id']);
                $stock->decrement('stock', $item['quantity']);

                // Catat mutasi stok
                ProductStockMovement::create([
                    'warehouse_id' => $stock->warehouse_id,
                    'province' => $stock->province,
                    'product_stock_id' => $stock->id,
                    'type' => 'Out',
                    'quantity' => $item['quantity'],
                    'ref_type' => Sale::class,
                    'ref_id' => $sale->id,
                    'note' => 'Penjualan #' . $sale->id,
                ]);
            }

            // 6. Simpan History Pembayaran Pertama & Upload Bukti
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

            return redirect()->route('admin.pemasaran-laporan-penjualan')->with('success', 'Laporan penjualan berhasil disimpan.');
        });
    }

    public function edit($id)
    {
        $sale = Sale::with(['warehouse', 'personResponsible', 'items.productStock.productVariant.product', 'paymentHistories'])->findOrFail($id);

        return view('admin.sales.edit-laporan-penjualan', [
            'sale' => $sale,
            'reportDate' => Carbon::parse($sale->report_date)->format('Y-m-d'),
            'personResponsibleName' => $sale->personResponsible?->name ?? '-',
            'currentPaidAmount' => (int) $sale->paymentHistories()->sum('amount'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $sale = Sale::findOrFail($id);

        // Cek status lunas
        $isPaidOff = $sale->status === 'Lunas' || (int) $sale->debt_amount <= 0;

        // 1. Jika sudah lunas, kita hanya izinkan update Catatan (Notes)
        if ($isPaidOff) {
            $request->validate([
                'notes' => ['nullable', 'string', 'max:1000'],
            ]);

            $sale->update([
                'notes' => $request->input('notes'),
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('admin.pemasaran-laporan-penjualan.edit', $sale->id)->with('success', 'Catatan laporan berhasil diperbarui.');
        }

        // 2. Jika BELUM lunas, jalankan validasi pembayaran cicilan seperti biasa
        $request->validate(
            [
                'payment_amount' => ['required', 'string'],
                'invoice' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:3072'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ],
            [
                'payment_amount.required' => 'Nominal cicilan wajib diisi.',
                'invoice.required' => 'Bukti pembayaran wajib diupload.',
                'invoice.mimes' => 'Bukti pembayaran harus berupa PNG, JPG, JPEG, atau PDF.',
                'invoice.max' => 'Ukuran bukti pembayaran maksimal 3 MB.',
            ],
        );

        return DB::transaction(function () use ($request, $sale) {
            $additionalPayment = $this->parseMoney($request->input('payment_amount'));

            if ($additionalPayment <= 0) {
                throw ValidationException::withMessages([
                    'payment_amount' => 'Nominal cicilan harus lebih dari 0.',
                ]);
            }

            $existingPaid = (int) $sale->paymentHistories()->sum('amount');
            $newPaid = $existingPaid + $additionalPayment;

            // Validasi agar tidak overpayment
            if ($newPaid > (int) $sale->total_amount) {
                $remainingDebt = max(0, (int) $sale->total_amount - $existingPaid);
                throw ValidationException::withMessages([
                    'payment_amount' => 'Nominal cicilan melebihi sisa tagihan (Sisa: Rp ' . number_format($remainingDebt, 0, ',', '.') . ').',
                ]);
            }

            $debtAmount = max(0, (int) $sale->total_amount - $newPaid);
            $finalStatus = $debtAmount <= 0 ? 'Lunas' : 'Terhutang';

            // Update data utama
            $sale->update([
                'paid_amount' => $newPaid,
                'debt_amount' => $debtAmount,
                'status' => $finalStatus,
                'notes' => $request->input('notes'),
                'updated_by' => Auth::id(),
            ]);

            // Simpan history cicilan
            $paymentHistory = HistorySalePayment::create([
                'sale_id' => $sale->id,
                'created_by' => Auth::id(),
                'payment_date' => now()->toDateString(),
                'amount' => $additionalPayment,
            ]);

            $paymentHistory->addMedia($request->file('invoice'))->toMediaCollection('payment_proof');

            return redirect()->route('admin.pemasaran-laporan-penjualan.edit', $sale->id)->with('success', 'Pembayaran berhasil ditambahkan.');
        });
    }

    public function destroy($id)
    {
        $sale = Sale::with(['items.productStock.productVariant.product', 'paymentHistories'])->findOrFail($id);

        DB::transaction(function () use ($sale) {
            $stockIds = $sale->items->pluck('product_stock_id')->filter()->map(fn($id) => (int) $id)->unique()->values();

            $stocks = ProductStock::query()->whereIn('id', $stockIds)->lockForUpdate()->get()->keyBy('id');

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

            // simpan siapa yang menghapus
            $sale->update([
                'deleted_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // soft delete history pembayaran dulu
            $sale->paymentHistories()->delete();

            // soft delete item penjualan
            $sale->items()->delete();

            // soft delete sale utama
            $sale->delete();
        });

        return redirect()->route('admin.pemasaran-laporan-penjualan')->with('success', 'Laporan penjualan berhasil dihapus dan stok dikembalikan.');
    }

    public function historyPayment($id)
    {
        $sale = Sale::with(['warehouse', 'personResponsible', 'updatedBy', 'items.productStock.productVariant.product', 'paymentHistories.createdBy'])->findOrFail($id);

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
}
