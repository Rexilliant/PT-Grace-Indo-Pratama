<?php

namespace App\Http\Controllers;

use App\Models\Procurement;
use App\Models\ProcurementItem;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;

class ProcurementController extends Controller
{
    public function export(Request $request)
    {
        $query = Procurement::query()
            ->with('userRequest')
            ->orderBy('created_at', 'desc');

        // FILTER sama seperti index kamu
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($sub) use ($search) {
                $sub->where('province', 'like', "%{$search}%")
                    ->orWhereHas('userRequest', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('purchase_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('purchase_at', '<=', $request->date_to);
        }

        $rows = $query->get()->map(function ($p) {
            return [
                'id Pengadaan' => $p->id,
                'Tanggal Pemesanan' => $p->purchase_at,
                'Nama Pemesan' => $p->userRequest->name ?? '-',
                'Provinsi' => $p->province ?? '-',
                'Status' => $p->status ?? '-',
            ];
        });

        $export = new class($rows) implements FromCollection, WithHeadings
        {
            public function __construct(private $rows) {}

            public function collection()
            {
                return $this->rows;
            }

            public function headings(): array
            {
                return ['id Pengadaan', 'Tanggal Pemesanan', 'Nama Pemesan', 'Provinsi', 'Status'];
            }
        };

        return Excel::download($export, 'procurements_'.now()->format('Ymd_His').'.xlsx');
    }

    public function index(Request $request)
    {
        $q = Procurement::query()
            ->with(['procurement_items', 'userRequest'])
            ->orderBy('created_at', 'desc');

        // SEARCH (contoh: cari nama pemesan / provinsi)
        if ($request->filled('search')) {
            $search = $request->search;

            $q->where(function ($sub) use ($search) {
                $sub->where('province', 'like', "%{$search}%")
                    ->orWhereHas('userRequest', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // FILTER STATUS
        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        // FILTER PROVINCE
        if ($request->filled('province')) {
            $q->where('province', $request->province);
        }

        // FILTER TANGGAL (purchase_at)
        if ($request->filled('date_from')) {
            $q->whereDate('purchase_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $q->whereDate('purchase_at', '<=', $request->date_to);
        }

        // ROWS PER PAGE (dropdown 10/25/50)
        $perPage = (int) ($request->get('per_page', 10));
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;

        $procurements = $q->paginate($perPage)->withQueryString();

        // buat dropdown province (opsional)
        $provinces = Procurement::query()
            ->select('province')
            ->whereNotNull('province')
            ->distinct()
            ->orderBy('province')
            ->pluck('province');

        $statuses = Procurement::query()
            ->select('status')
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        return view('admin.procurement.procurements', compact('procurements', 'provinces', 'statuses'));
    }

    public function create()
    {
        /**
         * GeoNames: ambil provinsi Indonesia
         * Indonesia geonameId = 1643084
         */
        $response = Http::get('http://api.geonames.org/childrenJSON', [
            'geonameId' => 1643084,
            'username' => 'hier',
        ]);

        $provinces = collect($response->json('geonames') ?? [])
            ->map(fn ($p) => [
                'id' => $p['geonameId'] ?? null,
                'name' => $p['name'] ?? null,
            ])
            ->filter(fn ($p) => ! empty($p['name']))
            ->values();

        $rawMaterials = RawMaterial::select('id', 'code', 'name', 'unit')
            ->orderBy('name')
            ->get();

        return view('admin.procurement.create-procurement', compact('provinces', 'rawMaterials'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'province' => 'required|string|max:255',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.quantity_requested' => 'required|integer|min:1',
            'purchase_at' => 'required|date',
        ]);

        try {
            // Simpan data procurement
            $procurement = Procurement::create([
                'request_by' => auth()->user()->id,
                'province' => $validated['province'],
                'note' => $validated['note'] ?? null,
                'status' => 'Menunggu',
                'purchase_at' => $validated['purchase_at'],
            ]);

            // Simpan data procurement items
            foreach ($validated['items'] as $item) {
                ProcurementItem::create([
                    'procurement_id' => $procurement->id,
                    'raw_material_id' => $item['raw_material_id'],
                    'quantity_requested' => $item['quantity_requested'],
                ]);
            }

            return redirect()->back()->with('success', 'Pengadaan berhasil dibuat.');
        } catch (\Throwable $th) {
            save_log_error($th);

            return redirect()->back()->withInput($request->all())->with('error', 'Terjadi kesalahan saat menyimpan pengadaan.');
        }
    }

    public function edit(Request $request, $id)
    {
        $response = Http::get('http://api.geonames.org/childrenJSON', [
            'geonameId' => 1643084,
            'username' => 'hier',
        ]);

        $provinces = collect($response->json('geonames') ?? [])
            ->map(fn ($p) => [
                'id' => $p['geonameId'] ?? null,
                'name' => $p['name'] ?? null,
            ])
            ->filter(fn ($p) => ! empty($p['name']))
            ->values();
        $procurement = Procurement::with('procurement_items.raw_material')
            ->findOrFail($id);

        return view('admin.procurement.edit-procurement', compact('provinces', 'procurement'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'reason' => 'nullable|string',
        ]);

        try {
            $procurement = Procurement::findOrFail($id);
            if ($validated['status'] === 'Diterima') {
                $procurement->update([
                    'status' => $validated['status'],
                    'approved_at' => now(),
                    'approved_by' => auth()->user()->id,
                ]);

                return redirect()->back()->with('success', 'Pengadaan berhasil diperbarui.');
            } elseif ($validated['status'] === 'Ditolak') {
                $procurement->update([
                    'status' => $validated['status'],
                    'reason' => $validated['reason'] ?? null,
                    'rejected_at' => now(),
                    'rejected_by' => auth()->user()->id,
                ]);

                return redirect()->back()->with('success', 'Pengadaan berhasil diperbarui.');
            } else {
                return redirect()->back()->with('error', 'Status tidak valid.');
            }

        } catch (\Throwable $th) {
            save_log_error($th);

            return redirect()->back()->withInput($request->all())->with('error', 'Terjadi kesalahan saat memperbarui pengadaan.');
        }
    }
}
