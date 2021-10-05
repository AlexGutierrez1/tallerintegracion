<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'rut' => '111111111',
            'name' => 'Administrador',
            'username' => 'Admin',
            'email' => 'admin@correo.cl',
            'password' => Hash::make('password')
        ]);

        $admin->givePermissionTo(Permission::orderBy('id')->pluck('id')->toArray());

        // Two full Appointment permission users
        for( $i = 0; $i < 2; $i++) {
            $createdUser = User::factory()->create();
            $createdUser->givePermissionTo(
                'user_login',
                'appointment_list',
                'appointment_create',
                'appointment_update',
                'appointment_archive',
                'appointment_destroy'
            );
        }

        // Two almost full Appointment permission users
        for( $i = 0; $i < 2; $i++) {
            $createdUser = User::factory()->create();
            $createdUser->givePermissionTo(
                'user_login',
                'appointment_list',
                'appointment_create',
                'appointment_update',
            );
        }

        // Two only listing Appointment permission users
        for( $i = 0; $i < 2; $i++) {
            $createdUser = User::factory()->create();
            $createdUser->givePermissionTo(
                'user_login',
                'appointment_list',
            );
        }
    }
}
