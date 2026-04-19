<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $q = Warehouse::query();

        if ($request->filled('name')) {
            $q->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('province')) {
            $q->where('province', 'like', '%' . $request->province . '%');
        }

        if ($request->filled('city')) {
            $q->where('city', 'like', '%' . $request->city . '%');
        }

        $perPage = (int) ($request->get('per_page', 10));
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;

        $warehouses = $q->paginate($perPage)->withQueryString();

        return view('admin.warehouse.warehouse', compact('warehouses'));
    }

    public function create()
    {
        $path = public_path('assets/data/provinceAndCity.json');

        if (!File::exists($path)) {
            abort(404, 'File provinceAndCity.json tidak ditemukan');
        }

        $json = File::get($path);
        $data = json_decode($json, true);

        $provinces = collect($data)->map(function ($prov) {
            return [
                'province_id' => $prov['province_id'],
                'province_name' => $prov['province_name'],
            ];
        })->values();

        $users = User::all();

        return view('admin.warehouse.create-warehouse', compact('provinces', 'users'));
    }

    public function getCities($provinceId)
    {
        $path = public_path('assets/data/provinceAndCity.json');

        if (!File::exists($path)) {
            return response()->json([]);
        }

        $json = File::get($path);
        $data = json_decode($json, true);

        $province = collect($data)->firstWhere('province_id', $provinceId);

        if (!$province) {
            return response()->json([]);
        }

        $cities = collect($province['cities'])->map(function ($city) use ($province) {
            return [
                'id' => $city['id'] ?? null,
                'name' => $city['name'] ?? null,
                'province_id' => $province['province_id'] ?? null,
                'province_name' => $province['province_name'] ?? null,
            ];
        })->values();

        return response()->json($cities);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:warehouses,name',
            'province' => 'required|string',
            'city' => 'required|string',
            'type' => 'required|string',
        ]);

        Warehouse::create($validated);
        return redirect()
            ->route('warehouses')
            ->with('success', 'Data gudang berhasil ditambahkan');
    }

    public function edit($id)
    {
        $warehouse = Warehouse::findOrFail($id);

        $path = public_path('assets/data/provinceAndCity.json');

        if (!File::exists($path)) {
            abort(404, 'File provinceAndCity.json tidak ditemukan');
        }

        $json = File::get($path);
        $data = json_decode($json, true);

        $provinces = collect($data)->map(function ($prov) {
            return [
                'province_id' => $prov['province_id'],
                'province_name' => $prov['province_name'],
            ];
        })->values();

        $selectedProvince = collect($data)->firstWhere('province_name', $warehouse->province);
        $selectedProvinceId = $selectedProvince['province_id'] ?? '';

        $users = User::all();
        $employees = Employee::where('warehouse_id', $warehouse->id)->get();

        return view('admin.warehouse.edit-warehouse', compact(
            'warehouse',
            'users',
            'provinces',
            'selectedProvinceId',
            'employees'
        ));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'province_id' => 'required|string|max:10',
            'city' => 'required|string|max:255',
        ]);

        $warehouse = Warehouse::findOrFail($id);

        $warehouse->update([
            'name' => $validated['name'],
            'province' => $validated['province'],
            'city' => $validated['city'],
        ]);

        return redirect()
            ->route('warehouses')
            ->with('success', 'Data gudang berhasil diperbarui');
    }

    public function destroy($id)
    {
        if (!auth()->check()) {
            abort(403, 'User belum login');
        }

        $warehouse = Warehouse::findOrFail($id);

        $warehouse->update([
            'deleted_by' => auth()->id(),
        ]);

        $warehouse->delete();

        return redirect()
            ->route('warehouses')
            ->with('success', 'Data gudang berhasil dihapus');
    }
}