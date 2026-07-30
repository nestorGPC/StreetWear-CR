<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            [
                'email' => 'admin@streetwearcr.test',
            ],
            [
                'name' => 'Administrador StreetWear CR',
                'password' => Hash::make('Admin12345'),
            ]
        );

        $admin->syncRoles(['super_admin']);


        $customer = User::updateOrCreate(
            [
                'email' => 'cliente@streetwearcr.test',
            ],
            [
                'name' => 'Cliente Prueba',
                'password' => Hash::make('Cliente12345'),
            ]
        );

        $customer->syncRoles(['customer']);
    }
}