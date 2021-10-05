<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'user_login']);
        Permission::create(['name' => 'user_list']);
        Permission::create(['name' => 'user_create']);
        Permission::create(['name' => 'user_update']);
        Permission::create(['name' => 'user_destroy']);
        Permission::create(['name' => 'user_block']);
        Permission::create(['name' => 'appointment_list']);
        Permission::create(['name' => 'appointment_create']);
        Permission::create(['name' => 'appointment_update']);
        Permission::create(['name' => 'appointment_archive']);
        Permission::create(['name' => 'appointment_destroy']);
    }
}
