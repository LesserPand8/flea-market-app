<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'item_id' => null, // テスト側で指定
            'user_id' => \App\Models\User::factory(),
            'method' => 'credit',
            'full_address' => $this->faker->address,
        ];
    }
}
