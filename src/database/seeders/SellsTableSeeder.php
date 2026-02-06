<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SellsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sells')->insert(
            [
                [
                    'item_id' => '1',
                    'user_id' => '1',
                ],
                [
                    'item_id' => '2',
                    'user_id' => '1',
                ],
                [
                    'item_id' => '3',
                    'user_id' => '1',
                ],
                [
                    'item_id' => '4',
                    'user_id' => '1',
                ],
                [
                    'item_id' => '5',
                    'user_id' => '1',
                ],
                [
                    'item_id' => '6',
                    'user_id' => '2',
                ],
                [
                    'item_id' => '7',
                    'user_id' => '2',
                ],
                [
                    'item_id' => '8',
                    'user_id' => '2',
                ],
                [
                    'item_id' => '9',
                    'user_id' => '2',
                ],
                [
                    'item_id' => '10',
                    'user_id' => '2',
                ],
            ]
        );
    }
}
