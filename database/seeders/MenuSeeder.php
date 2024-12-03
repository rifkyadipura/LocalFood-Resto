<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menu')->insert([
            [
                'id' => 1,
                'name' => 'Nasi Goreng Biasa',
                'harga' => 15000.00,
                'stok' => 50,
                'status' => 1,
                'foto' => 'uploads/menu/nasi_goreng_Biasa.jpg',
                'deskripsi' => 'Nasi goreng biasa adalah hidangan sederhana khas.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
