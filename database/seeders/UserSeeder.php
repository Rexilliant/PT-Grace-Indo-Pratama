<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        $role = Role::firstOrCreate([
            'name' => 'master',
            'guard_name' => 'web',
        ]);

        $allPermissions = Permission::where('guard_name', 'web')->get();

        $role->syncPermissions($allPermissions);

        if (! $user->hasRole($role->name)) {
            $user->assignRole($role);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
