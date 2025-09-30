<?php

namespace Database\Factories;

use App\Models\Sell;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class SellFactory extends Factory
{
    protected $model = Sell::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
        ];
    }
}
