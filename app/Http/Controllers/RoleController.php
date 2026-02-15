<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->paginate(10);

        return view('admin.roles.roles', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();

        return view('admin.roles.roles-create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        try {

            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web',
            ]);
            $role->syncPermissions($request->permissions ?? []);

            return redirect()->back()->with('success', 'Role berhasil ditambahkan');
        } catch (\Throwable $e) {
            save_log_error($e);

            return back()->with('error', 'Gagal simpan data');
        }
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();

        return view('admin.roles.roles-edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required', Rule::unique('roles', 'name')->ignore($id),
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        try {
            $role = Role::findOrFail($id);
            $role->update([
                'name' => $request->name,
            ]);
            $role->syncPermissions($request->permissions ?? []);

            return redirect()->back()->with('success', 'Berhasil mengupdate data');
        } catch (\Throwable $th) {
            save_log_error($th);

            return redirect()->back()->with('error', 'Gagal mengupdate data')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();

            return redirect()->back()->with('success', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            save_log_error($th);

            return redirect()->back()->with('error', 'Gagal menghapus data');
        }
    }
}
