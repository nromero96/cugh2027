<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Nilton',
            'lastname' => 'Romero',
            'second_lastname' => 'Agurto',
            'email' => 'niltondeveloper96@gmail.com',
            'nationality' => '1',
            'password' => bcrypt('123456789'),
            'document_number' => '00000000',
            'country' => '1',
            'status' => 'active',
            'photo' => 'default-profile.jpg'
        ])->assignRole('Administrador');

    }
}
