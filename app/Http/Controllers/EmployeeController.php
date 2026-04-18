<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $q = Employee::query();
        if ($request->filled('nip')) {
            $q->where('nip', 'like', '%'.$request->nip.'%');
        }
        if ($request->filled('name')) {
            $q->where('name', 'like', '%'.$request->name.'%');
        }
        if ($request->filled('email')) {
            $q->where('email', 'like', '%'.$request->email.'%');
        }
        if ($request->filled('phone')) {
            $q->where('phone', 'like', '%'.$request->phone.'%');
        }
        if ($request->filled('position')) {
            $q->where('position', 'like', '%'.$request->position.'%');
        }
        $perPage = (int) ($request->get('per_page', 10));
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;
        $employees = $q->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        return view('admin.employee.employees', compact('employees'));
    }

    public function create()
    {
        $response = Http::get('http://api.geonames.org/countryInfoJSON', [
            'username' => 'hier',
        ]);
        $warehouses = Warehouse::all();

        $countries = $response->json('geonames');

        return view('admin.employee.create-emplyee', compact('countries', 'warehouses'));
    }

    public function getProvinces($countryCode)
    {
        $response = Http::get('http://api.geonames.org/searchJSON', [
            'country' => $countryCode,
            'featureCode' => 'ADM1',
            'maxRows' => 1000,
            'username' => 'hier',
        ]);

        return response()->json($response->json()['geonames'] ?? []);
    }

    public function getCities($countryCode)
    {
        $adminCode1 = request('adminCode1');

        $response = Http::get('http://api.geonames.org/searchJSON', [
            'country' => $countryCode,
            'adminCode1' => $adminCode1,
            'username' => 'hier',
        ]);

        return response()->json($response->json()['geonames'] ?? []);
    }

    private function generateNip()
    {
        return DB::transaction(function () {

            $year = date('Y');

            // ambil nip terakhir di tahun ini
            $lastEmployee = Employee::where('nip', 'like', $year.'%')
                ->orderBy('nip', 'desc')
                ->lockForUpdate() // <-- penting untuk mencegah duplicate saat bersamaan
                ->first();

            $nextNumber = 1;

            if ($lastEmployee) {
                $lastSequence = (int) substr($lastEmployee->nip, -4);
                $nextNumber = $lastSequence + 1;
            }

            return $year.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'birthday' => 'required|date',
            'email' => 'required|email|unique:employees,email',
            'position' => 'string|max:255',
            'phone' => 'required|string|max:20|unique:employees,phone',
            'country' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'nullable|string',
            'address' => 'required|string',
            'profile_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5048',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ]);
        try {
            $request->birthday = Carbon::parse($request->birthday)->setTimeZone('Asia/Jakarta')->format('Y-m-d');
            $nip = $this->generateNip();
            $employee = Employee::create([
                'nip' => $nip,
                'name' => $request->name,
                'position' => $request->position,
                'birthday' => $request->birthday,
                'email' => $request->email,
                'phone' => $request->phone,
                'country' => $request->country,
                'province' => $request->province,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'warehouse_id' => $request->warehouse_id,
                'created_at' => now('Asia/Jakarta')->format('Y-m-d H:i:s'),
            ]);

            if ($request->hasFile('profile_image')) {
                $employee->clearMediaCollection('profile_images');

                $employee
                    ->addMediaFromRequest('profile_image')->usingFileName(
                        $nip.'.'.$request->file('profile_image')->getClientOriginalExtension()
                    )
                    ->toMediaCollection('profile_images');
            }

            return redirect()->back()->with('success', 'Data karyawan berhasil disimpan.');
        } catch (\Throwable $th) {
            save_log_error($th);

            return back()->with('error', 'Gagal menyimpan data karyawan. Silakan coba lagi.')->withInput();
        }
    }

    public function edit($id)
    {
        $response = Http::get('http://api.geonames.org/countryInfoJSON', [
            'username' => 'hier',
        ]);
        $countries = $response->json('geonames');
        $employee = Employee::findOrFail($id);
        $profileImage = $employee->getFirstMediaUrl('profile_images') ?: null;
        $warehouses = Warehouse::all();

        return view('admin.employee.edit-employees', compact('countries', 'employee', 'profileImage', 'warehouses'));
    }

    public function update(Request $request, $id)
    {
        if (! $request->hasFile('profile_image')) {
            $request->request->remove('profile_image');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'birthday' => 'required|date',
            'email' => 'required|email|unique:employees,email,'.$id,
            'position' => 'string|max:255',
            'phone' => 'required|string|max:20|unique:employees,phone,'.$id,
            'country' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'address' => 'required|string',
            'profile_image' => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5048',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ]);
        try {
            $request->birthday = Carbon::parse($request->birthday)->setTimeZone('Asia/Jakarta')->format('Y-m-d');
            $employee = Employee::findOrFail($id);
            $employee->update([
                'name' => $request->name,
                'position' => $request->position,
                'birthday' => $request->birthday,
                'email' => $request->email,
                'phone' => $request->phone,
                'country' => $request->country,
                'province' => $request->province,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'warehouse_id' => $request->warehouse_id,
            ]);

            if ($request->hasFile('profile_image')) {
                $employee->clearMediaCollection('profile_images');

                $employee
                    ->addMediaFromRequest('profile_image')->usingFileName(
                        $employee->nip.'.'.$request->file('profile_image')->getClientOriginalExtension()
                    )
                    ->toMediaCollection('profile_images');
            }
            return redirect()->back()->with('success', 'Data karyawan berhasil disimpan.');
        } catch (\Throwable $th) {
            save_log_error($th);

            return back()->with('error', 'Gagal menyimpan data karyawan. Silakan coba lagi.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->delete();

            return redirect()->route('employees')->with('success', 'Data karyawan berhasil dihapus.');
        } catch (\Throwable $th) {
            save_log_error($th);

            return back()->with('error', 'Gagal menghapus data karyawan. Silakan coba lagi.');
        }
    }

    public function restore($id)
    {
        try {
            $employee = Employee::withTrashed()->findOrFail($id);
            $employee->restore();

            return redirect()->route('employees')->with('success', 'Data karyawan berhasil dikembalikan.');
        } catch (\Throwable $th) {
            save_log_error($th);

            return back()->with('error', 'Gagal mengembalikan data karyawan. Silakan coba lagi.');
        }
    }
}
