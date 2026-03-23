<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'Pán',
            'last_name' => 'Admin',
            'email' => 'admin@ukf.sk',
            'password' => Hash::make('123'),
            'role' => 'admin',
            'premium_until' => null,
        ]);

        User::create([
            'first_name' => 'Dávid',
            'last_name' => 'Držík',
            'email' => 'ddrzik@ukf.sk',
            'password' => Hash::make('456'),
            'role' => 'user',
            'premium_until' => now()->addDays(30),
        ]);

        User::create([
            'first_name' => 'Jozef',
            'last_name' => 'Kapusta',
            'email' => 'jkapusta@ukf.sk',
            'password' => Hash::make('789'),
            'role' => 'user',
            'premium_until' => null,
        ]);

        User::create([
            'first_name' => 'Mária',
            'last_name' => 'Nováková',
            'email' => 'mnovakova@ukf.sk',
            'password' => Hash::make('111'),
            'role' => 'user',
            'premium_until' => now()->addDays(15),
        ]);

        User::create([
            'first_name' => 'Peter',
            'last_name' => 'Kováč',
            'email' => 'pkovac@ukf.sk',
            'password' => Hash::make('222'),
            'role' => 'user',
            'premium_until' => null,
        ]);

        User::create([
            'first_name' => 'Lucia',
            'last_name' => 'Horváthová',
            'email' => 'lhorvathova@ukf.sk',
            'password' => Hash::make('333'),
            'role' => 'user',
            'premium_until' => now()->addDays(60),
        ]);

        User::create([
            'first_name' => 'Martin',
            'last_name' => 'Varga',
            'email' => 'mvarga@ukf.sk',
            'password' => Hash::make('444'),
            'role' => 'user',
            'premium_until' => null,
        ]);

        User::create([
            'first_name' => 'Eva',
            'last_name' => 'Tóthová',
            'email' => 'etothova@ukf.sk',
            'password' => Hash::make('555'),
            'role' => 'user',
            'premium_until' => now()->addDays(10),
        ]);
    }
}
