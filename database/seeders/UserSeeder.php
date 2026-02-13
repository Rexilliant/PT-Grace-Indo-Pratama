<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ========================
        // 1. Buat Employee
        // ========================

        // ========================
        // 2. Buat User
        // ========================
        $user = User::create([
            'name' => 'Admin',
            'email' => 'test@example.com',
            'password' => Hash::make('password'), // <-- password login
        ]);

        // ========================
        // 3. Role & Permission
        // ========================
        $permission = Permission::firstOrCreate([
            'name' => 'access dashboard',
            'guard_name' => 'web',
        ]);

        $role = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $role->givePermissionTo($permission);

        // ========================
        // 4. Assign Role ke User
        // ========================
        $user->assignRole($role);
    }
}
