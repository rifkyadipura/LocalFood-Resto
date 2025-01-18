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
        $date = Carbon::create(2024, 11, 30, 0, 0, 0);

        DB::table('menu')->insert([
            // Kategori 1: Makanan Berat
            [
                'menu_id' => 1,
                'nama_menu' => 'Soto Bandung',
                'harga' => 15000.00,
                'stok' => 50,
                'status' => 1,
                'foto' => 'uploads/menu/Soto_Bandung.jpg',
                'deskripsi' => 'Soto Bandung adalah hidangan khas Sunda dengan kuah bening kaldu sapi.',
                'kategory_id' => 1,
                'dibuat_oleh' => 2,
                'diperbarui_oleh' => 2,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 2,
                'nama_menu' => 'Mie Kocok Bandung',
                'harga' => 10000.00,
                'stok' => 45,
                'status' => 1,
                'foto' => 'uploads/menu/Mie_Kocok_Bandung.jpg',
                'deskripsi' => 'Mie Kocok Bandung adalah hidangan khas Bandung dengan mie kuning dan kuah kaldu sapi.',
                'kategory_id' => 1,
                'dibuat_oleh' => 2,
                'diperbarui_oleh' => 2,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 3,
                'nama_menu' => 'Nasi Goreng Spesial',
                'harga' => 18000.00,
                'stok' => 40,
                'status' => 1,
                'foto' => 'uploads/menu/Nasi_Goreng_Spesial.jpg',
                'deskripsi' => 'Nasi goreng dengan campuran telur, ayam, dan sosis yang nikmat.',
                'kategory_id' => 1,
                'dibuat_oleh' => 2,
                'diperbarui_oleh' => 2,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 4,
                'nama_menu' => 'Ayam Penyet Sambal',
                'harga' => 20000.00,
                'stok' => 35,
                'status' => 1,
                'foto' => 'uploads/menu/Ayam_Penyet_Sambal.jpg',
                'deskripsi' => 'Ayam goreng dengan sambal pedas khas yang menggugah selera.',
                'kategory_id' => 1,
                'dibuat_oleh' => 2,
                'diperbarui_oleh' => 2,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 5,
                'nama_menu' => 'Rendang Daging Sapi',
                'harga' => 25000.00,
                'stok' => 30,
                'status' => 1,
                'foto' => 'uploads/menu/Rendang_Daging_Sapi.jpg',
                'deskripsi' => 'Rendang daging sapi dengan bumbu rempah khas Minang.',
                'kategory_id' => 1,
                'dibuat_oleh' => 2,
                'diperbarui_oleh' => 2,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 6,
                'nama_menu' => 'Pecel Lele',
                'harga' => 15000.00,
                'stok' => 50,
                'status' => 1,
                'foto' => 'uploads/menu/Pecel_Lele.jpg',
                'deskripsi' => 'Lele goreng dengan sambal khas dan lalapan segar.',
                'kategory_id' => 1,
                'dibuat_oleh' => 2,
                'diperbarui_oleh' => 2,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 7,
                'nama_menu' => 'Ikan Bakar Kecap',
                'harga' => 20000.00,
                'stok' => 25,
                'status' => 1,
                'foto' => 'uploads/menu/Ikan_Bakar_Kecap.jpg',
                'deskripsi' => 'Ikan bakar dengan olesan kecap manis dan bumbu spesial.',
                'kategory_id' => 1,
                'dibuat_oleh' => 2,
                'diperbarui_oleh' => 2,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            // Kategori 2: Makanan Ringan
            [
                'menu_id' => 8,
                'nama_menu' => 'Pisang Goreng Keju',
                'harga' => 8000.00,
                'stok' => 30,
                'status' => 1,
                'foto' => 'uploads/menu/Pisang_Goreng_Keju.jpg',
                'deskripsi' => 'Pisang goreng dengan taburan keju parut yang lezat.',
                'kategory_id' => 2,
                'dibuat_oleh' => 2,
                'diperbarui_oleh' => 2,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 9,
                'nama_menu' => 'Tahu Isi',
                'harga' => 5000.00,
                'stok' => 40,
                'status' => 1,
                'foto' => 'uploads/menu/Tahu_Isi.jpg',
                'deskripsi' => 'Tahu goreng dengan isian sayuran yang nikmat.',
                'kategory_id' => 2,
                'dibuat_oleh' => 2,
                'diperbarui_oleh' => 2,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 10,
                'nama_menu' => 'Risoles Mayo',
                'harga' => 7000.00,
                'stok' => 25,
                'status' => 1,
                'foto' => 'uploads/menu/Risoles_Mayo.jpg',
                'deskripsi' => 'Risoles dengan isian mayonnaise, smoked beef, dan telur.',
                'kategory_id' => 2,
                'dibuat_oleh' => 2,
                'diperbarui_oleh' => 2,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            // Kategori 3: Minuman
            [
                'menu_id' => 11,
                'nama_menu' => 'Es Teh Manis',
                'harga' => 5000.00,
                'stok' => 60,
                'status' => 1,
                'foto' => 'uploads/menu/Es_Teh_Manis.jpg',
                'deskripsi' => 'Es teh manis yang menyegarkan.',
                'kategory_id' => 3,
                'dibuat_oleh' => 2,
                'diperbarui_oleh' => 2,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 12,
                'nama_menu' => 'Es Kopi Susu',
                'harga' => 12000.00,
                'stok' => 50,
                'status' => 1,
                'foto' => 'uploads/menu/Es_Kopi_Susu.jpg',
                'deskripsi' => 'Es kopi susu dengan rasa khas.',
                'kategory_id' => 3,
                'dibuat_oleh' => 2,
                'diperbarui_oleh' => 2,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 13,
                'nama_menu' => 'Es Jeruk Segar',
                'harga' => 7000.00,
                'stok' => 55,
                'status' => 1,
                'foto' => 'uploads/menu/Es_Jeruk_Segar.jpg',
                'deskripsi' => 'Es jeruk segar dengan rasa manis dan asam alami.',
                'kategory_id' => 3,
                'dibuat_oleh' => 2,
                'diperbarui_oleh' => 2,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'menu_id' => 14,
                'nama_menu' => 'Teh Tarik',
                'harga' => 10000.00,
                'stok' => 40,
                'status' => 1,
                'foto' => 'uploads/menu/Teh_Tarik.jpg',
                'deskripsi' => 'Minuman teh dengan campuran susu khas.',
                'kategory_id' => 3,
                'dibuat_oleh' => 2,
                'diperbarui_oleh' => 2,
                'created_at' => $date,
                'updated_at' => $date,
            ],
        ]);
    }
}
