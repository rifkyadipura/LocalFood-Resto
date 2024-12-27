<?php

namespace Database\Factories;

use App\Models\Kategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class KategoryFactory extends Factory
{
    protected $model = Kategory::class;

    public function definition()
    {
        return [
            'nama_kategory' => $this->faker->word,
        ];
    }
}
