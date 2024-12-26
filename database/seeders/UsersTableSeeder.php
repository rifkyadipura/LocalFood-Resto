<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
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
                'nama_lengkap' => 'admin',
                'email' => 'penerimaanmahasiswabaru10@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
                'remember_token' => Str::random(10),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Agus',
                'email' => 'agus@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('agus123'),
                'remember_token' => Str::random(10),
                'role' => 'Kepala Staf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Rifky Najra Adipura',
                'email' => 'rifkyadipura@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('rifky123'),
                'remember_token' => Str::random(10),
                'role' => 'Kasir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Hafizh Fakhri Muharram',
                'email' => 'fakhri@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('fakhri123'),
                'remember_token' => Str::random(10),
                'role' => 'Kasir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
