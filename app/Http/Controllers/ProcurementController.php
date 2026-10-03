<?php

namespace App\Http\Controllers;

use App\Models\Procurement;
use App\Models\ProcurementItem;
use App\Models\RawMaterial;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Facades\Excel;

class ProcurementController extends Controller
{
    public function export(Request $request)
    {
        $q = Procurement::query()
            ->with(['userRequest', 'warehouse', 'procurement_items.raw_material.stock', 'userApproved', 'userRejected'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('name')) {
            $q->whereHas('userRequest', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
            });
        }

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        if ($request->filled('warehouse_id')) {
            $q->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('date_from')) {
            $q->whereDate('purchase_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $q->whereDate('purchase_at', '<=', $request->date_to);
        }

        $rows = collect();
        $mergeRanges = [];
        $currentRow = 2;

        $q->get()->each(function ($p) use ($rows, &$mergeRanges, &$currentRow) {
            $startRow = $currentRow;

            foreach ($p->procurement_items as $item) {
                $rawMaterial = $item->raw_material;

                $rows->push([
                    'ID Pengadaan' => $p->id,
                    'Tanggal Pemesanan' => $p->purchase_at ? Carbon::parse($p->purchase_at)->format('d/m/Y') : '-',
                    'Nama Pemesan' => $p->userRequest->name ?? '-',
                    'Gudang' => $p->warehouse->name ?? '-',
                    'Status' => $p->status ?? '-',
                    'Catatan' => $p->note ?? '-',
                    'Alasan Penolakan' => $p->reason ?? '-',

                    'Kode Raw Material' => $rawMaterial->code ?? 'RM-' . ($rawMaterial->id ?? '-'),
                    'Nama Raw Material' => $rawMaterial->name ?? '-',
                    'Stok' => $rawMaterial->stock->stock ?? 0,
                    'Jumlah Diminta' => $item->quantity_requested ?? 0,
                    'Satuan' => $rawMaterial->unit ?? '-',

                    'Approved By' => $p->userApproved->name ?? '-',
                    'Approved At' => $p->approved_at ? Carbon::parse($p->approved_at)->format('d/m/Y H:i') : '-',
                    'Rejected By' => $p->userRejected->name ?? '-',
                    'Rejected At' => $p->rejected_at ? Carbon::parse($p->rejected_at)->format('d/m/Y H:i') : '-',
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
                return ['ID Pengadaan', 'Tanggal Pemesanan', 'Nama Pemesan', 'Gudang', 'Status', 'Catatan', 'Alasan Penolakan', 'Kode Raw Material', 'Nama Raw Material', 'Stok', 'Jumlah Diminta', 'Satuan', 'Approved By', 'Approved At', 'Rejected By', 'Rejected At'];
            }

            public function registerEvents(): array
            {
                return [
                    AfterSheet::class => function (AfterSheet $event) {
                        foreach ($this->mergeRanges as $range) {
                            foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'M', 'N', 'O', 'P'] as $column) {
                                $event->sheet->mergeCells($column . $range['start'] . ':' . $column . $range['end']);
                            }
                        }
                    },
                ];
            }
        };

        return Excel::download($export, 'procurements_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function index(Request $request)
    {
        $warehouseId = auth()->user()->employee?->warehouse_id;

        $q = Procurement::query()
            ->with(['procurement_items', 'userRequest', 'warehouse'])
            ->withCount('purchaseReceipts')
            ->orderBy('created_at', 'desc');

        // Jika user punya warehouse_id, procurement hanya gudang itu
        if ($warehouseId) {
            $q->where('warehouse_id', $warehouseId);
        }

        if ($request->filled('name')) {
            $q->whereHas('userRequest', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
            });
        }

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        // Filter warehouse_id dari request hanya berlaku kalau user tidak punya gudang
        if (!$warehouseId && $request->filled('warehouse_id')) {
            $q->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('date_from')) {
            $q->whereDate('purchase_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $q->whereDate('purchase_at', '<=', $request->date_to);
        }

        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;

        $procurements = $q->paginate($perPage)->withQueryString();

        // Jika user punya warehouse_id, dropdown gudang hanya gudang itu
        if ($warehouseId) {
            $warehouses = Warehouse::where('id', $warehouseId)->get();
        } else {
            $warehouses = Warehouse::all();
        }

        $statuses = Procurement::query()->select('status')->whereNotNull('status')->distinct()->orderBy('status')->pluck('status');

        return view('admin.procurement.procurements', compact('procurements', 'warehouses', 'statuses'));
    }

    public function print($id)
    {
        try {
            $procurement = Procurement::with(['procurement_items.raw_material', 'userRequest', 'warehouse', 'userApproved', 'userRejected'])->findOrFail($id);

            return view('admin.procurement.print-procurement', compact('procurement'));
        } catch (\Throwable $th) {
            save_log_error($th);

            return redirect()->back()->with('error', 'Gagal memuat data cetak.');
        }
    }

    public function create()
    {
        $user = Auth::user();

        $warehouseId = optional($user->employee)->warehouse_id;

        $warehouses = Warehouse::where('type', 'produksi')
            ->when($warehouseId, function ($query) use ($warehouseId) {
                $query->where('id', $warehouseId);
            })
            ->get();
        $rawMaterials = RawMaterial::select('id', 'code', 'name', 'unit')->orderBy('name')->get();

        return view('admin.procurement.create-procurement', compact('warehouses', 'rawMaterials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'warehouse_id' => 'required|exists:warehouses,id',
                'note' => 'nullable|string',
                'purchase_at' => 'required|date',

                'items' => 'required|array|min:1',

                'items.*.raw_material_id' => 'required|exists:raw_materials,id',
                'items.*.quantity_requested' => 'required|integer|min:1',
            ],
            [
                'items.*.raw_material_id.required' => 'Silakan pilih bahan baku.',
                'items.*.raw_material_id.exists' => 'Bahan baku tidak valid.',

                'items.*.quantity_requested.required' => 'Jumlah pesanan wajib diisi.',
                'items.*.quantity_requested.integer' => 'Jumlah harus berupa angka.',
                'items.*.quantity_requested.min' => 'Jumlah minimal 1.',
            ],
        );
        try {
            $procurement = Procurement::create([
                'request_by' => auth()->user()->id,
                'warehouse_id' => $validated['warehouse_id'],
                'note' => $validated['note'] ?? null,
                'status' => 'Menunggu',
                'purchase_at' => $validated['purchase_at'],
            ]);

            foreach ($validated['items'] as $item) {
                ProcurementItem::create([
                    'procurement_id' => $procurement->id,
                    'raw_material_id' => $item['raw_material_id'],
                    'quantity_requested' => $item['quantity_requested'],
                ]);
            }

            return redirect()->route('procurements')->with('success', 'Pengadaan berhasil dibuat.');
        } catch (\Throwable $th) {
            save_log_error($th);

            return redirect()->back()->withInput($request->all())->with('error', 'Terjadi kesalahan saat menyimpan pengadaan.');
        }
    }

    public function edit(Request $request, $id)
    {
        $user = Auth::user();

        $warehouseId = optional($user->employee)->warehouse_id;

        $warehouses = Warehouse::where('type', 'produksi')
            ->when($warehouseId, function ($query) use ($warehouseId) {
                $query->where('id', $warehouseId);
            })
            ->get();

        $rawMaterials = RawMaterial::select('id', 'code', 'name', 'unit')->orderBy('name')->get();
        $procurement = Procurement::with(['procurement_items.raw_material', 'userRequest', 'warehouse'])->findOrFail($id);

        return view('admin.procurement.edit-procurement', compact('warehouses', 'rawMaterials', 'procurement'));
    }

    public function update(Request $request, $id)
    {
        $procurement = Procurement::findOrFail($id);

        $user = auth()->user();
        $isMenunggu = $procurement->status === 'Menunggu';

        $canEditData = $user->can('edit pengadaan bahan baku') && $isMenunggu;
        $canEditStatus = $user->can('edit status pengadaan bahan baku') && $isMenunggu;

        if (!$canEditData && !$canEditStatus) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengubah data ini atau status pengadaan sudah tidak dapat diubah.');
        }

        $rules = [
            'status' => 'required|string|in:Menunggu,Disetujui,Ditolak',
            'reason' => 'nullable|string',
        ];

        if ($canEditData) {
            $rules['warehouse_id'] = 'required|exists:warehouses,id';
            $rules['purchase_at'] = 'required|date';
            $rules['note'] = 'nullable|string';
            $rules['items'] = 'required|array|min:1';
            $rules['items.*.raw_material_id'] = 'required|exists:raw_materials,id';
            $rules['items.*.quantity_requested'] = 'required|integer|min:1';
        }

        $messages = [
            'status.in' => 'Status tidak valid.',
            'warehouse_id.required' => 'Gudang wajib diisi.',
            'warehouse_id.exists' => 'Gudang tidak valid.',
            'purchase_at.required' => 'Tanggal pemesanan wajib diisi.',
            'purchase_at.date' => 'Tanggal pemesanan tidak valid.',
            'items.required' => 'Minimal 1 item pesanan wajib ada.',
            'items.min' => 'Minimal 1 item pesanan wajib ada.',
            'items.*.raw_material_id.required' => 'Silakan pilih bahan baku.',
            'items.*.raw_material_id.exists' => 'Bahan baku tidak valid.',
            'items.*.quantity_requested.required' => 'Jumlah pesanan wajib diisi.',
            'items.*.quantity_requested.integer' => 'Jumlah pesanan harus berupa angka.',
            'items.*.quantity_requested.min' => 'Jumlah pesanan minimal 1.',
        ];

        $validated = $request->validate($rules, $messages);

        if ($canEditStatus && $validated['status'] === 'Ditolak' && empty(trim($validated['reason'] ?? ''))) {
            return redirect()->back()->withInput($request->all())->with('error', 'Alasan penolakan wajib diisi.');
        }

        try {
            if ($canEditData) {
                $procurement->warehouse_id = $validated['warehouse_id'];
                $procurement->purchase_at = $validated['purchase_at'];
                $procurement->note = $validated['note'] ?? null;

                ProcurementItem::where('procurement_id', $procurement->id)->forceDelete();

                foreach ($validated['items'] as $itemData) {
                    ProcurementItem::create([
                        'procurement_id' => $procurement->id,
                        'raw_material_id' => $itemData['raw_material_id'],
                        'quantity_requested' => $itemData['quantity_requested'],
                    ]);
                }
            }

            if ($canEditStatus) {
                $newStatus = $validated['status'];
                if ($newStatus === 'Disetujui') {
                    $procurement->status = 'Disetujui';
                    $procurement->approved_at = now();
                    $procurement->approved_by = auth()->id();
                } elseif ($newStatus === 'Ditolak') {
                    $procurement->status = 'Ditolak';
                    $procurement->reason = $validated['reason'] ?? null;
                    $procurement->rejected_at = now();
                    $procurement->rejected_by = auth()->id();
                } elseif ($newStatus === 'Menunggu') {
                    $procurement->status = 'Menunggu';
                }
            }

            $procurement->save();

            return redirect()->route('procurements')->with('success', 'Pengadaan berhasil diperbarui.');
        } catch (\Throwable $th) {
            save_log_error($th);

            return redirect()->back()->withInput($request->all())->with('error', 'Terjadi kesalahan saat memperbarui pengadaan.');
        }
    }

    public function destroy($id)
    {
        try {
            $procurement = Procurement::withCount('purchaseReceipts')->findOrFail($id);

            if (!$procurement->canBeDeleted()) {
                return redirect()->route('procurements')->with('error', 'Data pengadaan tidak dapat dihapus karena sudah ada di penerimaan barang (purchase receipt).');
            }

            $procurement->update([
                'deleted_by' => auth()->id(),
            ]);

            $procurement->delete();

            return redirect()->route('procurements')->with('success', 'Data Pengadaan berhasil dihapus');
        } catch (\Throwable $th) {
            save_log_error($th);

            return redirect()->route('procurements')->with('error', 'Terjadi kesalahan saat menghapus data pengadaan');
        }
    }
}
