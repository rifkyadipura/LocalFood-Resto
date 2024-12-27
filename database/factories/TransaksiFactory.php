<?php

namespace Database\Factories;

use App\Models\Transaksi;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransaksiFactory extends Factory
{
    protected $model = Transaksi::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'kode_transaksi' => 'TRX-' . Str::random(8),
            'menu_id' => Menu::factory(), // Menggunakan factory untuk menu
            'users_id' => User::factory(), // Menggunakan factory untuk user
            'jumlah' => $this->faker->numberBetween(1, 10),
            'total_harga' => $this->faker->numberBetween(50000, 200000),
            'uang_dibayar' => $this->faker->numberBetween(50000, 300000),
            'uang_kembalian' => $this->faker->numberBetween(0, 100000),
            'metode_pembayaran' => $this->faker->randomElement(['Cash', 'QRIS']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
