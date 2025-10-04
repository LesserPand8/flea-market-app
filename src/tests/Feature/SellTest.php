<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SellTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品出品画面で必要な情報が保存できるテスト
     */
    public function test_sell_page_saves_all_required_info()
    {
        // 1. ユーザー作成＆ログイン
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user instanceof \Illuminate\Database\Eloquent\Collection ? $user->first() : $user);

        // カテゴリ作成
        $category = \App\Models\Category::factory()->create([
            'category' => '家電',
        ]);

        // 2. 商品出品画面を開く（GET）
        $response = $this->get('/sell');
        $response->assertStatus(200);

        // 3. 各項目に適切な情報を入力して保存（POST）
        $imagePath = storage_path('app/public/items/default.png');
        $postData = [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト商品の説明',
            'price' => 12345,
            'condition' => '良好',
            'category' => [$category->id],
            'image' => new \Illuminate\Http\UploadedFile(
                $imagePath,
                'default.png',
                'image/png',
                null,
                true
            ),
        ];
        $postResponse = $this->post('/sell', $postData);
        $postResponse->assertRedirect('/');

        // DBに正しく保存されていること
        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト商品の説明',
            'price' => 12345,
            'condition' => '良好',
        ]);
        $this->assertDatabaseHas('category_item', [
            'item_id' => \App\Models\Item::where('name', 'テスト商品')->first()->id,
            'category_id' => $category->id,
        ]);
    }
}
