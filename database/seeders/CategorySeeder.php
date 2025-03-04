<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            ['category_id' => 1, 'category_name' => 'Makanan Berat', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['category_id' => 2, 'category_name' => 'Makanan Ringan', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['category_id' => 3, 'category_name' => 'Minuman', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
