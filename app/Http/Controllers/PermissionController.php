<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function create(){
        return view("admin.permissions.permissions-create");
    }
    public function store(Request $request){
        $request->validate([
            'name' => 'required|unique:permissions,name'
        ]);
        try {
            Permission::create([
                "name" => $request->name,
                "guard_name" => "web",
            ]);
            return redirect()->back()->with('success', "Berhasil Menyimpan Data");
        } catch (\Throwable $th) {
            save_log_error($th);
            return redirect()->back()->with('error', "Gagal Menyimpan Data");
        }
    }
}
