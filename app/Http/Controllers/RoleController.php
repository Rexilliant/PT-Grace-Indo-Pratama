<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function create()
    {
        return view('admin.roles.roles-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        try {

            Role::create([
                'name' => $request->name,
                'guard_name' => 'web',
            ]);

            return redirect()->back()->with('success', 'Role berhasil ditambahkan');
        } catch (\Throwable $e) {
            save_log_error($e);

            return back()->with('error', 'Gagal simpan data');
        }
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);

        return view('admin.roles.roles-edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required', Rule::unique('roles', 'name')->ignore($id),
        ]);
        try {
            $role = Role::findOrFail($id);
            $role->update([
                'name' => $request->name,
            ]);

            return redirect()->back()->with('success', "Berhasil mengupdate data");
        } catch (\Throwable $th) {
            save_log_error($th);

            return redirect()->back()->with('error', 'Gagal mengupdate data')->withInput();
        }
    }
}
