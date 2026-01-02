<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPemissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::create(['name' => 'view products', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'create products', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'edit products', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'delete products', 'guard_name' => 'sanctum']);

        Permission::create(['name' => 'view categories', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'create categories', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'edit categories', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'delete categories', 'guard_name' => 'sanctum']);

        Permission::create(['name' => 'view orders', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'create orders', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'edit orders', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'cancel orders', 'guard_name' => 'sanctum']);

        Permission::create(['name' => 'view users', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'create users', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'edit users', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'delete users', 'guard_name' => 'sanctum']);

        Permission::create(['name' => 'view deliveries', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'update deliveries status', 'guard_name' => 'sanctum']);

        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'sanctum']);
        $permissions_admin = Permission::whereIn('name', [
            'view products',
            'create products',
            'edit products',
            'delete products',
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            'view orders',
            'create orders',
            'edit orders',
            'cancel orders',
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view deliveries',
            'update deliveries status'
        ])->where('guard_name', 'sanctum')->get();
        $adminRole->givePermissionTo($permissions_admin);
        $customerRole = Role::create(['name' => 'customer', 'guard_name' => 'sanctum']);
        $permissions_customer = Permission::whereIn('name', [
            'view products',
            'create orders',
            'view orders',
            'cancel orders',
            'view categories'
        ])->where('guard_name', 'sanctum')->get();
        $customerRole->givePermissionTo($permissions_customer);
        $deliveryRole = Role::create(['name' => 'delivery', 'guard_name' => 'sanctum']);
        $permissions_delivery = Permission::whereIn('name', [
            'view deliveries',
            'update deliveries status'
        ])->where('guard_name', 'sanctum')->get();
        $deliveryRole->givePermissionTo($permissions_delivery);
    }
}
