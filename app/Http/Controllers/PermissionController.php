<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::latest()->paginate(5);

        return view('admin.permissions.permissions', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.permissions-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);
        try {
            Permission::create([
                'name' => $request->name,
                'guard_name' => 'web',
            ]);

            return redirect()->back()->with('success', 'Berhasil Menyimpan Data');
        } catch (\Throwable $th) {
            save_log_error($th);

            return redirect()->back()->with('error', 'Gagal Menyimpan Data');
        }
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id);

        return view('admin.permissions.permissions-edit', compact('permission'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,'.$id,
        ]);
        try {
            $permission = Permission::findOrFail($id);
            $permission->update([
                'name' => $request->name,
                'guard_name' => 'web',
            ]);

            return redirect()->back()->with('success', 'Berhasil Menyimpan Data');
        } catch (\Throwable $th) {
            save_log_error($th);

            return redirect()->back()->with('error', 'Gagal Menyimpan Data');
        }
    }

    public function destroy($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->delete();

            return redirect()->back()->with('success', 'Berhasil Menghapus Data');
        } catch (\Throwable $th) {
            save_log_error($th);

            return redirect()->back()->with('error', 'Gagal Menghapus Data');
        }
    }
}
