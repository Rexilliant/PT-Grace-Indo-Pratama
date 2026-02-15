<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('employee', 'roles')->withTrashed()->latest()->paginate(10);

        return view('admin.users.users', compact('users'));
    }

    public function create()
    {
        $employees = Employee::all();
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('admin.users.user-create', compact('employees', 'roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'employee_id' => 'nullable|exists:employees,id',
            'password' => 'required|string|min:6|confirmed',

            'role' => 'nullable|exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'employee_id' => $request->employee_id,
                'password' => bcrypt($request->password),
            ]);
            if ($request->filled('role')) {
                $user->assignRole($request->role);
            }

            if ($request->filled('permissions')) {
                $user->givePermissionTo($request->permissions);
            }

            // Redirect atau tampilkan pesan sukses
            return redirect()->back()->with('success', 'User berhasil dibuat!');

        } catch (\Throwable $th) {
            save_log_error($th);

            return back()->with('error', 'Gagal membuat user');
        }
    }

    public function edit($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $employees = Employee::all();
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('admin.users.user-edit', compact('user', 'employees', 'roles', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:6|confirmed',

            'role' => 'nullable|exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        try {
            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            if ($request->filled('role')) {
                $user->syncRoles($request->role);
            } else {
                $user->syncRoles([]);
            }

            if ($request->filled('permissions')) {
                $user->syncPermissions($request->permissions);
            } else {
                $user->syncPermissions([]);
            }

            return redirect()->back()->with('success', 'User berhasil diperbarui!');
        } catch (\Throwable $th) {
            save_log_error($th);

            return back()->with('error', 'Gagal memperbarui user');
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus!');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->back()->with('success', 'User berhasil dikembalikan!');
    }
}
