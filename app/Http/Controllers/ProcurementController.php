<?php

namespace App\Http\Controllers;

use App\Models\Procurement;
use App\Models\ProcurementItem;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProcurementController extends Controller
{
    public function index()
    {
        $procurements = Procurement::with('procurement_items')->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.procurement.procurements', compact('procurements'));
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
        ]);

        try {
            // Simpan data procurement
            $procurement = Procurement::create([
                'request_by' => auth()->user()->id,
                'province' => $validated['province'],
                'note' => $validated['note'] ?? null,
                'status' => 'Menunggu',
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

        $rawMaterials = RawMaterial::select('id', 'code', 'name', 'unit')
            ->orderBy('name')
            ->get();

        $procurement = Procurement::with('procurement_items.raw_material')
            ->findOrFail($id);

        return view('admin.procurement.edit-procurement', compact('provinces', 'rawMaterials', 'procurement'));
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
