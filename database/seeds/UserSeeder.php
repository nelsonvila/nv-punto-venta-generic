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
            'nombre' => 'Jose Manuel Bustos',
            'email' => 'josebustos@gmail.com',
            'usuario' => 'admin',
            'condicion' => 1,
            'telefono' => 3865585910,
            'password' => \Illuminate\Support\Facades\Hash::make('secret'), //secret
            'idrol' => 1
        ]);
    }
}
