<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('items')->insert([
            [
                'name' => '腕時計',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'brand' => 'Rolax',
                'price' => '15000',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'condition' => '良好',
            ],
            [
                'name' => 'HDD',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'brand' => '西芝',
                'price' => '5000',
                'description' => '高速で信頼性の高いハードディスク',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'name' => '玉ねぎ3束',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'brand' => 'なし',
                'price' => '300',
                'description' => '新鮮な玉ねぎ3束のセット',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'name' => '革靴',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'brand' => '',
                'price' => '4000',
                'description' => 'クラシックなデザインの革靴',
                'condition' => '状態が悪い',
            ],
            [
                'name' => 'ノートPC',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'brand' => '',
                'price' => '45000',
                'description' => '高性能なノートパソコン',
                'condition' => '良好',
            ],
            [
                'name' => 'マイク',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'brand' => 'なし',
                'price' => '8000',
                'description' => '高音質のレコーディング用マイク',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'name' => 'ショルダーバッグ',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'brand' => '',
                'price' => '3500',
                'description' => 'おしゃれなショルダーバッグ',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'name' => 'タンブラー',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'brand' => 'なし',
                'price' => '500',
                'description' => '使いやすいタンブラー',
                'condition' => '状態が悪い',
            ],
            [
                'name' => 'コーヒーミル',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'brand' => 'Starbacks',
                'price' => '4000',
                'description' => '手動のコーヒーミル',
                'condition' => '良好',
            ],
            [
                'name' => 'メイクセット',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'brand' => 'Starbacks',
                'price' => '2500',
                'description' => '便利なメイクアップセット',
                'condition' => '目立った傷や汚れなし',
            ],
        ]);
    }
}
