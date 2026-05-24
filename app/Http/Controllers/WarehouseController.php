<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
class WarehouseController extends Controller
{
    public function export(Request $request)
    {
        $query = Warehouse::query()
            ->with('employees')
            ->orderBy('created_at', 'desc');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled('province')) {
            $query->where('province', 'like', '%'.$request->province.'%');
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%'.$request->city.'%');
        }

        if ($request->filled('type')) {
            $query->where('type', 'like', '%'.$request->type.'%');
        }

        $rows = collect();
        $mergeRanges = [];
        $currentRow = 2;
        $no = 1;

        $query->get()->each(function ($w) use ($rows, &$mergeRanges, &$currentRow, &$no) {
            $startRow = $currentRow;

            $employees = $w->employees;

            if ($employees->isEmpty()) {
                $employees = collect([null]);
            }

            foreach ($employees as $employee) {
                $rows->push([
                    'No' => $no,
                    'ID Gudang' => $w->id,
                    'Nama Gudang' => $w->name ?? '-',
                    'Provinsi' => $w->province ?? '-',
                    'Kota' => $w->city ?? '-',
                    'Jenis' => $w->type ?? '-',
                    'Dibuat Pada' => $w->created_at
                        ? Carbon::parse($w->created_at)->format('d/m/Y H:i')
                        : '-',

                    'NIP Karyawan' => $employee->nip ?? '-',
                    'Nama Karyawan' => $employee->name ?? '-',
                    'Email Karyawan' => $employee->email ?? '-',
                    'No. HP Karyawan' => $employee->phone ?? '-',
                    'Jabatan Karyawan' => $employee->position ?? '-',
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

            $no++;
        });

        $export = new class($rows, $mergeRanges) implements FromCollection, WithEvents, WithHeadings
        {
            public function __construct(private $rows, private $mergeRanges) {}

            public function collection()
            {
                return $this->rows;
            }

            public function headings(): array
            {
                return [
                    'No',
                    'ID Gudang',
                    'Nama Gudang',
                    'Provinsi',
                    'Kota',
                    'Jenis',
                    'Dibuat Pada',
                    'NIP Karyawan',
                    'Nama Karyawan',
                    'Email Karyawan',
                    'No. HP Karyawan',
                    'Jabatan Karyawan',
                ];
            }

            public function registerEvents(): array
            {
                return [
                    AfterSheet::class => function (AfterSheet $event) {
                        foreach ($this->mergeRanges as $range) {
                            foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $column) {
                                $event->sheet->mergeCells(
                                    $column.$range['start'].':'.$column.$range['end']
                                );
                            }
                        }
                    },
                ];
            }
        };

        return Excel::download(
            $export,
            'warehouses_'.now()->format('Ymd_His').'.xlsx'
        );
    }

    public function index(Request $request)
    {
        $q = Warehouse::query();

        if ($request->filled('name')) {
            $q->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled('province')) {
            $q->where('province', 'like', '%'.$request->province.'%');
        }

        if ($request->filled('city')) {
            $q->where('city', 'like', '%'.$request->city.'%');
        }
        if ($request->filled('type')) {
            $q->where('type', 'like', '%'.$request->type.'%');
        }

        $perPage = (int) ($request->get('per_page', 10));
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;

        $warehouses = $q->paginate($perPage)->withQueryString();

        return view('admin.warehouse.warehouse', compact('warehouses'));
    }

    public function create()
    {
        $path = public_path('assets/data/provinceAndCity.json');

        if (! File::exists($path)) {
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

        if (! File::exists($path)) {
            return response()->json([]);
        }

        $json = File::get($path);
        $data = json_decode($json, true);

        $province = collect($data)->firstWhere('province_id', $provinceId);

        if (! $province) {
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

        if (! File::exists($path)) {
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
        if (! auth()->check()) {
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
