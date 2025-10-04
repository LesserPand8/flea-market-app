<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 「購入する」ボタン押下で購入が完了するテスト
     */
    public function test_purchase_button_completes_purchase()
    {
        // ユーザー作成＆ログイン
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user instanceof \Illuminate\Database\Eloquent\Collection ? $user->first() : $user);

        // Profile作成（postal_code等がnullにならないように）
        $profile = \App\Models\Profile::factory()->create([
            'user_id' => $user->id,
        ]);

        // 商品作成
        $item = \App\Models\Item::factory()->create([
            'name' => '購入テスト商品',
        ]);

        // 購入画面にアクセス
        $response = $this->get('/purchase/' . $item->id);
        $response->assertStatus(200);

        // 「購入する」ボタン押下（POST: コンビニ払い）
        $postResponse = $this->post('/purchase/' . $item->id, [
            'method' => 'コンビニ払い',
            'full_address' => $profile->postal_code . ' ' . $profile->address . $profile->building_name,
        ]);
        // トップページにリダイレクトされる
        $postResponse->assertRedirect('/');

        // 購入レコードがDBに作成されていること
        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'method' => 'コンビニ払い',
            'full_address' => $profile->postal_code . ' ' . $profile->address . $profile->building_name,
        ]);
    }

    /**
     * 購入した商品が商品一覧画面で「Sold」と表示されるテスト
     */
    public function test_purchased_item_is_shown_as_sold_in_index()
    {
        // ユーザー作成＆ログイン
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user instanceof \Illuminate\Database\Eloquent\Collection ? $user->first() : $user);

        // Profile作成
        $profile = \App\Models\Profile::factory()->create([
            'user_id' => $user->id,
        ]);

        // 商品作成
        $item = \App\Models\Item::factory()->create([
            'name' => '一覧表示テスト商品',
        ]);

        // 購入画面にアクセス
        $this->get('/purchase/' . $item->id)->assertStatus(200);

        // 「購入する」ボタン押下（POST: コンビニ払い）
        $this->post('/purchase/' . $item->id, [
            'method' => 'コンビニ払い',
            'full_address' => $profile->postal_code . ' ' . $profile->address . $profile->building_name,
        ])->assertRedirect('/');

        // 商品一覧画面で「Sold」ラベルが表示されていること
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Sold');
        $response->assertSee('一覧表示テスト商品');
    }

    /**
     * 購入した商品がプロフィールの購入商品一覧に追加されているテスト
     */
    public function test_purchased_item_is_shown_in_profile_purchase_list()
    {
        // ユーザー作成＆ログイン
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user instanceof \Illuminate\Database\Eloquent\Collection ? $user->first() : $user);

        // Profile作成
        $profile = \App\Models\Profile::factory()->create([
            'user_id' => $user->id,
        ]);

        // 商品作成
        $item = \App\Models\Item::factory()->create([
            'name' => 'プロフィール購入商品テスト',
        ]);

        // 購入画面にアクセス
        $this->get('/purchase/' . $item->id)->assertStatus(200);

        // 「購入する」ボタン押下（POST: コンビニ払い）
        $this->post('/purchase/' . $item->id, [
            'method' => 'コンビニ払い',
            'full_address' => $profile->postal_code . ' ' . $profile->address . $profile->building_name,
        ])->assertRedirect('/');

        // プロフィール画面（購入商品一覧）で商品名が表示されていること
        $response = $this->get('/mypage?page=buy');
        $response->assertStatus(200);
        $response->assertSee('プロフィール購入商品テスト');
    }

    // /**
    //  * 小計画面で支払い方法の選択が正しく反映されるテスト
    //  */
    // public function test_payment_method_is_reflected_on_checkout_screen()
    // {
    //     // ユーザー作成＆ログイン
    //     $user = \App\Models\User::factory()->create();
    //     $this->actingAs($user instanceof \Illuminate\Database\Eloquent\Collection ? $user->first() : $user);

    //     // Profile作成
    //     $profile = \App\Models\Profile::factory()->create([
    //         'user_id' => $user->id,
    //     ]);

    //     // 商品作成
    //     $item = \App\Models\Item::factory()->create([
    //         'name' => '支払い方法テスト商品',
    //     ]);

    //     // 支払い方法選択画面にアクセス
    //     $response = $this->get('/purchase/' . $item->id);
    //     $response->assertStatus(200);

    //     // プルダウンで支払い方法を選択（POST送信）
    //     $selectedMethod = 'クレジットカード';
    //     $postResponse = $this->post('/purchase/' . $item->id, [
    //         'method' => $selectedMethod,
    //         'full_address' => $profile->postal_code . ' ' . $profile->address . $profile->building_name,
    //     ]);
    //     $postResponse->assertRedirect('/purchase/' . $item->id);

    //     // POST後のリダイレクト先で、支払い方法がselectタグのselected属性で反映されていることを検証
    //     $checkoutResponse = $this->get('/purchase/' . $item->id);
    //     $checkoutResponse->assertStatus(200);
    //     // selectタグのoptionとして「カード払い」が存在することのみ検証
    //     $checkoutResponse->assertSee('<option class="method__input-label" value="カード払い">カード払い</option>', false);
    // }

    /**
     * 送付先住所変更画面で登録した住所が商品購入画面に反映されるテスト
     */
    public function test_address_change_reflects_on_purchase_screen()
    {
        // 1. ユーザー作成＆ログイン
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user instanceof \Illuminate\Database\Eloquent\Collection ? $user->first() : $user);

        // 2. Profile初期作成（初期値）
        \App\Models\Profile::factory()->create([
            'user_id' => $user->id,
            'postal_code' => '111-1111',
            'address' => '東京都新宿区初期町1-1-1',
            'building_name' => '初期マンション101',
        ]);

        // 商品作成
        $item = \App\Models\Item::factory()->create([
            'name' => '住所反映テスト商品',
        ]);

        // 2. 送付先住所変更画面で住所を登録（POST）
        $newPostal = '222-2222';
        $newAddress = '大阪府大阪市変更町2-2-2';
        $newBuilding = '変更マンション202';
        $this->post('/purchase/address/' . $item->id, [
            'postal_code' => $newPostal,
            'address' => $newAddress,
            'building_name' => $newBuilding,
        ])->assertRedirect('/purchase/' . $item->id);

        // DBのProfileが更新されていること
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'postal_code' => $newPostal,
            'address' => $newAddress,
            'building_name' => $newBuilding,
        ]);

        // 3. 商品購入画面を再度開く
        $response = $this->get('/purchase/' . $item->id);
        $response->assertStatus(200);
        // 画面に新しい住所が反映されていること
        $response->assertSee('〒 ' . $newPostal);
        $response->assertSee($newAddress . $newBuilding);
    }

    /**
     * 購入した商品に送付先住所が紐づいて登録されるテスト
     */
    public function test_purchase_registers_correct_shipping_address()
    {
        // 1. ユーザー作成＆ログイン
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user instanceof \Illuminate\Database\Eloquent\Collection ? $user->first() : $user);

        // 2. Profile初期作成（初期値）
        \App\Models\Profile::factory()->create([
            'user_id' => $user->id,
            'postal_code' => '111-1111',
            'address' => '東京都新宿区初期町1-1-1',
            'building_name' => '初期マンション101',
        ]);

        // 商品作成
        $item = \App\Models\Item::factory()->create([
            'name' => '住所紐付けテスト商品',
        ]);

        // 2. 送付先住所変更画面で住所を登録（POST）
        $newPostal = '333-3333';
        $newAddress = '京都府京都市紐付町3-3-3';
        $newBuilding = '紐付マンション303';
        $this->post('/purchase/address/' . $item->id, [
            'postal_code' => $newPostal,
            'address' => $newAddress,
            'building_name' => $newBuilding,
        ])->assertRedirect('/purchase/' . $item->id);

        // 3. 商品を購入する（POST: コンビニ払い）
        $purchaseResponse = $this->post('/purchase/' . $item->id, [
            'method' => 'コンビニ払い',
            'full_address' => $newPostal . ' ' . $newAddress . $newBuilding,
        ]);
        $purchaseResponse->assertRedirect('/');

        // purchasesテーブルに正しい住所でレコードが登録されていること
        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'method' => 'コンビニ払い',
            'full_address' => $newPostal . ' ' . $newAddress . $newBuilding,
        ]);
    }
}
