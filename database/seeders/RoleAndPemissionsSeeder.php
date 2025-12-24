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
        Permission::create(['name' => 'view products']);
        Permission::create(['name' => 'create products']);
        Permission::create(['name' => 'edit products']);
        Permission::create(['name' => 'delete products']);

        Permission::create(['name' => 'view orders']);
        Permission::create(['name' => 'create orders']);
        Permission::create(['name' => 'edit orders']);
        Permission::create(['name' => 'cancel orders']);

        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);

        Permission::create(['name' => 'view deliveries']);
        Permission::create(['name' => 'update deliveries status']);

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->can([
            'view products',
            'create products',
            'edit products',
            'delete products',
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
        ]);
        $customerRole = Role::create(['name' => 'customer']);
        $customerRole->givePermissionTo(['view products', 'create orders', 'view orders', 'cancel orders']);
        $deliveryRole = Role::create(['name' => 'delivery']);
        $deliveryRole->givePermissionTo(['view deliveries', 'update deliveries status']);
    }
}
