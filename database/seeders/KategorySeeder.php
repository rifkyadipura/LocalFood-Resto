<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class KategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('kategory')->insert([
            ['kategory_id' => 1, 'nama_kategory' => 'Makanan Berat', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategory_id' => 2, 'nama_kategory' => 'Makanan Ringan', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategory_id' => 3, 'nama_kategory' => 'Minuman', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
