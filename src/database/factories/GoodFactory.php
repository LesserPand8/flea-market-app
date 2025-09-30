<?php

namespace Database\Factories;

use App\Models\Good;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class GoodFactory extends Factory
{
    protected $model = Good::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
        ];
    }
}
