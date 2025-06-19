<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $names = ['alvyn', 'adam', 'rifki', 'raka', 'samuel', 'hansen', 'chris', 'jack', 'naufal', 'putri'];

        foreach ($names as $name) {
            User::create([
                'name' => ucfirst($name),
                'email' => $name . '@gmail.com',
                'password' => Hash::make('12345678'),
            ]);
        }
    }
}
