<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * プロフィールページで必要な情報が取得できるテスト
     */
    public function test_profile_page_shows_all_required_info()
    {
        // 1. ユーザー作成＆ログイン
        $user = \App\Models\User::factory()->create([
            'name' => 'テストユーザー',
        ]);
        $this->actingAs($user instanceof \Illuminate\Database\Eloquent\Collection ? $user->first() : $user);

        // プロフィール作成
        $profile = \App\Models\Profile::factory()->create([
            'user_id' => $user->id,
            'profile_image' => 'storage/images/test-profile.png',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building_name' => 'テストマンション101',
        ]);

        // 出品商品作成
        $sellItem = \App\Models\Item::factory()->create([
            'name' => '出品商品A',
            'image' => 'storage/images/sell-item.png',
        ]);
        $sellItem->sellers()->attach($user->id); // sellersリレーション

        // 購入商品作成
        $buyItem = \App\Models\Item::factory()->create([
            'name' => '購入商品B',
            'image' => 'storage/images/buy-item.png',
        ]);
        $buyItem->purchases()->create([
            'user_id' => $user->id,
            'method' => 'コンビニ払い',
            'full_address' => '123-4567 東京都渋谷区1-1-1テストマンション101',
        ]);

        // 2. プロフィールページ（出品一覧）
        $responseSell = $this->get('/mypage?page=sell');
        $responseSell->assertStatus(200);
        $responseSell->assertSee($profile->profile_image);
        $responseSell->assertSee($user->name);
        $responseSell->assertSee('出品商品A');

        // 3. プロフィールページ（購入一覧）
        $responseBuy = $this->get('/mypage?page=buy');
        $responseBuy->assertStatus(200);
        $responseBuy->assertSee($profile->profile_image);
        $responseBuy->assertSee($user->name);
        $responseBuy->assertSee('購入商品B');
    }

    /**
     * プロフィール設定ページで各項目の初期値が正しく表示されているテスト（個別追加）
     */
    public function test_profile_setting_page_shows_initial_values()
    {
        // 1. ユーザー作成＆ログイン
        $user = \App\Models\User::factory()->create([
            'name' => '初期ユーザー',
        ]);
        $this->actingAs($user instanceof \Illuminate\Database\Eloquent\Collection ? $user->first() : $user);

        // プロフィール作成（初期値）
        $profile = \App\Models\Profile::factory()->create([
            'user_id' => $user->id,
            'profile_image' => 'storage/images/default-profile.png',
            'postal_code' => '999-8888',
            'address' => '北海道札幌市初期町9-9-9',
            'building_name' => '初期マンション999',
        ]);

        // 2. プロフィール設定ページを開く
        $response = $this->get('/mypage/profile');
        $response->assertStatus(200);
        $response->assertSee($profile->profile_image);
        $response->assertSee($user->name);
        $response->assertSee($profile->postal_code);
        $response->assertSee($profile->address);
        $response->assertSee($profile->building_name);
    }
}
