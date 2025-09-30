<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\ItemsTableSeeder;

class IndexTest extends TestCase
{
    use RefreshDatabase;
    /**
     * 全商品を取得できるテスト
     */
    public function test_all_items_are_displayed_on_index()
    {
        $this->seed(ItemsTableSeeder::class);

        $items = \App\Models\Item::all();

        $response = $this->get('/');

        $response->assertStatus(200);
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    /**
     * 購入済み商品は「Sold」と表示されるテスト
     */
    public function test_sold_label_is_displayed_for_purchased_items()
    {
        // 必要ならSeederを実行
        $this->seed(ItemsTableSeeder::class);

        // 購入済み商品を1件取得し、購入済み状態にする
        $item = \App\Models\Item::first();
        \App\Models\Purchase::factory()->create([
            'item_id' => $item->id,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /**
     * 自分が出品した商品は表示されないテスト
     */
    public function test_own_items_are_not_displayed_in_index()
    {
        // ユーザー作成
        $user = \App\Models\User::factory()->create();
        // Ensure $user is a single instance
        if ($user instanceof \Illuminate\Database\Eloquent\Collection) {
            $user = $user->first();
        }


        // 他ユーザーの商品
        $otherItem = \App\Models\Item::factory()->create([
            'name' => 'OtherUserItem',
        ]);
        // 自分の商品
        $ownItem = \App\Models\Item::factory()->create([
            'name' => $user->name . '_item',
        ]);
        // Sellテーブルに自分の商品を登録
        \App\Models\Sell::factory()->create([
            'user_id' => $user->id,
            'item_id' => $ownItem->id,
        ]);

        // ログイン
        $this->actingAs($user);

        // 商品一覧ページへアクセス
        $response = $this->get('/');
        $response->assertStatus(200);
        // 自分の商品名は表示されない
        $response->assertDontSee($ownItem->name);
        // 他人の商品名は表示される
        $response->assertSee($otherItem->name);
    }

    /**
     * いいねした商品だけが表示されるテスト
     */
    public function test_only_liked_items_are_displayed_in_mylist()
    {
        // ユーザー作成
        $user = \App\Models\User::factory()->create();
        if ($user instanceof \Illuminate\Database\Eloquent\Collection) {
            $user = $user->first();
        }
        // 他ユーザーの商品
        $otherItem = \App\Models\Item::factory()->create([
            'name' => 'OtherUserItem',
        ]);
        // いいねした商品
        $likedItem = \App\Models\Item::factory()->create([
            'name' => 'LikedItem',
        ]);
        // Goodテーブルに「いいね」登録
        \App\Models\Good::factory()->create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        // ログイン
        $this->actingAs($user);

        // マイリストページへアクセス
        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);
        // いいねした商品は表示される
        $response->assertSee($likedItem->name);
        // いいねしていない商品は表示されない
        $response->assertDontSee($otherItem->name);
    }

    /**
     * マイリストで購入済み商品に「Sold」ラベルが表示されるテスト
     */
    public function test_sold_label_is_displayed_for_purchased_items_in_mylist()
    {
        // ユーザー作成
        $user = \App\Models\User::factory()->create();
        if ($user instanceof \Illuminate\Database\Eloquent\Collection) {
            $user = $user->first();
        }
        // いいねした商品（未購入）
        $likedItem = \App\Models\Item::factory()->create([
            'name' => 'LikedItem',
        ]);
        // いいねした商品（購入済み）
        $purchasedItem = \App\Models\Item::factory()->create([
            'name' => 'PurchasedItem',
        ]);
        // Goodテーブルに「いいね」登録
        \App\Models\Good::factory()->create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);
        \App\Models\Good::factory()->create([
            'user_id' => $user->id,
            'item_id' => $purchasedItem->id,
        ]);
        // 購入済み商品としてPurchase登録
        \App\Models\Purchase::factory()->create([
            'item_id' => $purchasedItem->id,
        ]);

        // ログイン
        $this->actingAs($user);

        // マイリストページへアクセス
        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);
        // 購入済み商品に「Sold」ラベルが表示される
        $response->assertSee('PurchasedItem');
        $response->assertSee('Sold');
        // 未購入商品は「Sold」ラベルが表示されない
        $response->assertSee('LikedItem');
    }

    /**
     * 未認証の場合はマイリストに何も表示されないテスト
     */
    public function test_mylist_displays_nothing_when_unauthenticated()
    {
        // いいね商品を作成（未ログインなので関係なし）
        $user = \App\Models\User::factory()->create();
        $likedItem = \App\Models\Item::factory()->create([
            'name' => 'LikedItem',
        ]);
        \App\Models\Good::factory()->create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        // 未ログインでマイリストページへアクセス
        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);
        // 何も表示されない（item-contents内に商品名が出ない）
        $response->assertDontSee('LikedItem');
    }

    /**
     * 商品名で部分一致検索ができるテスト
     */
    public function test_items_can_be_searched_by_partial_name()
    {
        // 商品を複数作成
        $item1 = \App\Models\Item::factory()->create(['name' => 'テスト商品A']);
        $item2 = \App\Models\Item::factory()->create(['name' => 'サンプル商品B']);
        $item3 = \App\Models\Item::factory()->create(['name' => 'テスト商品C']);

        // 検索キーワード「テスト」で検索
        $response = $this->get('/?keyword=テスト');
        $response->assertStatus(200);
        // 部分一致する商品が表示される
        $response->assertSee($item1->name);
        $response->assertSee($item3->name);
        // 部分一致しない商品は表示されない
        $response->assertDontSee($item2->name);
    }

    /**
     * 検索状態がマイリストでも保持されているテスト
     */
    public function test_search_keyword_is_preserved_in_mylist_tab()
    {
        // ユーザー作成
        $user = \App\Models\User::factory()->create();
        if ($user instanceof \Illuminate\Database\Eloquent\Collection) {
            $user = $user->first();
        }
        // いいねした商品
        $likedItem = \App\Models\Item::factory()->create([
            'name' => 'テスト商品A',
        ]);
        $otherLikedItem = \App\Models\Item::factory()->create([
            'name' => 'サンプル商品B',
        ]);
        // Goodテーブルに「いいね」登録
        \App\Models\Good::factory()->create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);
        \App\Models\Good::factory()->create([
            'user_id' => $user->id,
            'item_id' => $otherLikedItem->id,
        ]);

        // ログイン
        $this->actingAs($user);

        // ホームページで検索
        $response = $this->get('/?keyword=テスト');
        $response->assertStatus(200);
        // 検索結果が表示される
        $response->assertSee($likedItem->name);
        $response->assertDontSee($otherLikedItem->name);
        // マイリストページに遷移（検索キーワード付き）
        $response = $this->get('/?tab=mylist&keyword=テスト');
        $response->assertStatus(200);
        // 検索キーワードが保持されている（部分一致する商品だけ表示）
        $response->assertSee($likedItem->name);
        $response->assertDontSee($otherLikedItem->name);
    }
}
