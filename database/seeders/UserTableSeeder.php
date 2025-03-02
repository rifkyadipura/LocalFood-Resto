<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'full_name' => 'admin',
                'email' => 'penerimaanmahasiswabaru10@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
                'remember_token' => Str::random(10),
                'role' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'Agus',
                'email' => 'agus@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('agus123'),
                'remember_token' => Str::random(10),
                'role' => 'Head Staff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'Rifky Najra Adipura',
                'email' => 'rifkyadipura@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('rifky123'),
                'remember_token' => Str::random(10),
                'role' => 'Cashier',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'Hafizh Fakhri Muharram',
                'email' => 'fakhri@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('fakhri123'),
                'remember_token' => Str::random(10),
                'role' => 'Cashier',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
