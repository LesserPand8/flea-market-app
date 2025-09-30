<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(100, 10000),
            'image' => $this->faker->imageUrl(640, 480, 'cats'),
            'condition' => $this->faker->randomElement(['new', 'used']),
            // 必要に応じて他のカラムも追加
        ];
    }
}
