<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Create the admin user.
     * @return void
    */
    public function run(): void
    {
        if (User::where('email', 'smguardiapediatrica@gmail.com')->exists()) {
            return;
        }

        User::create([
            'name' => 'Administrador Guardias',
            'email' => 'smguardiapediatrica@gmail.com',
            'password' => Hash::make('Gu4RD1a5_'),
        ]);
    }
}
