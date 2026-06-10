<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosRolesSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@erpsolis.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('12345678'),
                'rol' => 'Administrador',
            ]
        );

        User::updateOrCreate(
            ['email' => 'directivo@erpsolis.com'],
            [
                'name' => 'Directivo',
                'password' => Hash::make('12345678'),
                'rol' => 'Directivo',
            ]
        );

        User::updateOrCreate(
            ['email' => 'operativo@erpsolis.com'],
            [
                'name' => 'Operativo',
                'password' => Hash::make('12345678'),
                'rol' => 'Operativo',
            ]
        );
    }
}