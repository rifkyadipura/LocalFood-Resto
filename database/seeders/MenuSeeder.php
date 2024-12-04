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
                'name' => 'Soto Bandung',
                'harga' => 15000.00,
                'stok' => 50,
                'status' => 1,
                'foto' => 'uploads/menu/Soto_Bandung.jpg',
                'deskripsi' => 'Soto Bandung adalah hidangan khas Sunda dengan kuah bening kaldu sapi, lobak, kacang kedelai goreng, dan bawang goreng, bercita rasa gurih dan segar.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'name' => 'Mie Kocok Bandung',
                'harga' => 10000.00,
                'stok' => 45,
                'status' => 1,
                'foto' => 'uploads/menu/Mie_Kocok_Bandung.jpg',
                'deskripsi' => 'Mie Kocok Bandung adalah hidangan khas Bandung dengan mie kuning, kuah kaldu sapi, kikil, tauge, dan bawang goreng, berpadu cita rasa gurih dan segar.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
