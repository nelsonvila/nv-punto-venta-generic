<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\User::class)->create([
            'nombre' => 'Nelson Vila',
            'email' => 'nelsonvila.22@gmail.com',
            'usuario' => 'nelsonvila',
            'condicion' => 1,
            'telefono' => 3865585910,
            'password' => \Illuminate\Support\Facades\Hash::make('secret'), //secret
            'idrol' => 1
        ]);
    }
}
