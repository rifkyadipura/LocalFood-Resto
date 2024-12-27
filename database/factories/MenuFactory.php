<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\User;
use App\Models\Kategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition()
    {
        return [
            'nama_menu' => $this->faker->word,
            'harga' => $this->faker->numberBetween(10000, 50000),
            'stok' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->boolean,
            'kategory_id' => Kategory::factory(),
            'foto' => null,
            'deskripsi' => $this->faker->sentence,
            'dibuat_oleh' => User::factory(), // Gunakan factory User
            'diperbarui_oleh' => User::factory(), // Gunakan factory User
        ];
    }
}
