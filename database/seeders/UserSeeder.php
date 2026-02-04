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
            'password' => bcrypt('123456789'),
            'document_number' => '00000000',
            'status' => 'active',
            'photo' => 'default-profile.jpg'
        ])->assignRole('Administrador');

        User::create([
            'name' => 'Rosa',
            'lastname' => 'Sheen',
            'second_lastname' => '',
            'email' => 'rosmarasoc@rosmarasociados.com',
            'document_number' => '1111111',
            'password' => bcrypt('123456789'),
            'status' => 'active',
            'photo' => 'default-profile.jpg'
        ])->assignRole('Administrador');

        User::create([
            'name' => 'Milagros',
            'lastname' => 'Estrada',
            'second_lastname' => '',
            'email' => 'inscripciones@rosmarasociados.com',
            'document_number' => '22222222',
            'password' => bcrypt('123456789'),
            'status' => 'active',
            'photo' => 'default-profile.jpg'
        ])->assignRole('Secretaria');

        User::create([
            'name' => 'Jhon',
            'lastname' => 'Perez',
            'email' => 'hl@example.com',
            'document_number' => '33333333',
            'password' => bcrypt('123456789'),
            'status' => 'inactive',
            'photo' => 'default-profile.jpg'
        ])->assignRole('Participante');

    }
}
