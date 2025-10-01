<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DetailTest extends TestCase
{
    use RefreshDatabase;
    /**
     * 商品詳細ページに必要な情報がすべて表示されるテスト
     */
    public function test_detail_page_displays_all_required_information()
    {
        // ユーザー・カテゴリ作成
        $user = \App\Models\User::factory()->create(['name' => 'テストユーザー']);
        $category = \App\Models\Category::factory()->create(['category' => '家電']);
        // 商品作成
        $item = \App\Models\Item::factory()->create([
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 12345,
            'description' => 'テスト商品説明',
            'condition' => 'new',
        ]);
        // カテゴリ紐付け
        $item->categories()->attach($category->id);
        // いいね（Good）
        \App\Models\Good::factory()->count(2)->create(['item_id' => $item->id]);
        // コメント
        $commentUser = \App\Models\User::factory()->create(['name' => 'コメントユーザー']);
        $comment = \App\Models\Comment::factory()->create([
            'user_id' => $commentUser->id,
            'item_id' => $item->id,
            'comment' => 'とても良い商品です',
        ]);

        // 商品詳細ページへアクセス
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
        // 商品画像
        $response->assertSee($item->image);
        // 商品名
        $response->assertSee('テスト商品');
        // ブランド名
        $response->assertSee('テストブランド');
        // 価格
        $response->assertSee('12345');
        // いいね数
        $response->assertSee('2');
        // コメント数
        $response->assertSee('1');
        // 商品説明
        $response->assertSee('テスト商品説明');
        // カテゴリ
        $response->assertSee('家電');
        // 商品の状態
        $response->assertSee('new');
        // コメントしたユーザー情報
        $response->assertSee('コメントユーザー');
        // コメント内容
        $response->assertSee('とても良い商品です');
    }

    /**
     * 商品詳細ページで複数カテゴリが表示されるテスト
     */
    public function test_detail_page_displays_multiple_categories()
    {
        // 商品作成
        $item = \App\Models\Item::factory()->create([
            'name' => 'カテゴリテスト商品',
        ]);
        // カテゴリを複数作成
        $category1 = \App\Models\Category::factory()->create(['category' => '家電']);
        $category2 = \App\Models\Category::factory()->create(['category' => '家具']);
        $category3 = \App\Models\Category::factory()->create(['category' => '雑貨']);
        // 商品にカテゴリを紐付け
        $item->categories()->attach([$category1->id, $category2->id, $category3->id]);

        // 商品詳細ページへアクセス
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
        // 複数カテゴリが表示されているか検証
        $response->assertSee('家電');
        $response->assertSee('家具');
        $response->assertSee('雑貨');
    }

    /**
     * いいねアイコン押下で商品が「いいね」登録され、合計値が増加するテスト
     */
    public function test_like_icon_registers_good_and_count_increases()
    {
        // ユーザー作成
        $user = \App\Models\User::factory()->create();
        // 商品作成
        $item = \App\Models\Item::factory()->create([
            'name' => 'いいねテスト商品',
        ]);
        // いいね前の合計値
        $this->actingAs($user instanceof \Illuminate\Database\Eloquent\Collection ? $user->first() : $user);
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee('0'); // いいね数初期値

        // いいねアイコン押下（POSTリクエスト）
        $postResponse = $this->post('/goods/' . $item->id, [
            'item_id' => $item->id,
        ]);
        $postResponse->assertStatus(302); // リダイレクト

        // 再度詳細ページでいいね数が1に増加していることを確認
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee('1'); // いいね数増加
    }

    /**
     * いいねアイコン押下で色が変化する（likedクラス付与）テスト
     */
    public function test_like_icon_changes_color_when_liked()
    {
        // ユーザー作成
        $user = \App\Models\User::factory()->create();
        // 商品作成
        $item = \App\Models\Item::factory()->create([
            'name' => '色変化テスト商品',
        ]);
        // ログイン
        $this->actingAs($user instanceof \Illuminate\Database\Eloquent\Collection ? $user->first() : $user);
        // いいね前の状態（likedクラスなし）
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee('☆');
        $response->assertDontSee('liked');

        // いいねアイコン押下（POSTリクエスト）
        $this->post('/goods/' . $item->id, [
            'item_id' => $item->id,
        ]);
        // いいね後の状態（likedクラス付与、★表示）
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee('★');
        $response->assertSee('liked');
    }

    /**
     * いいねアイコンを再度押下でいいね解除・合計値減少のテスト
     */
    public function test_like_icon_unregisters_good_and_count_decreases()
    {
        // ユーザー作成
        $user = \App\Models\User::factory()->create();
        // 商品作成
        $item = \App\Models\Item::factory()->create([
            'name' => 'いいね解除テスト商品',
        ]);
        // ログイン
        $this->actingAs($user instanceof \Illuminate\Database\Eloquent\Collection ? $user->first() : $user);
        // いいねアイコン押下（登録）
        $this->post('/goods/' . $item->id, [
            'item_id' => $item->id,
        ]);
        // いいね数が1になることを確認
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee('1');
        $response->assertSee('★');
        $response->assertSee('liked');

        // もう一度いいねアイコン押下（解除）
        $this->post('/goods/' . $item->id, [
            'item_id' => $item->id,
        ]);
        // いいね数が0に減少していることを確認
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee('0');
        $response->assertSee('☆');
        $response->assertDontSee('liked');
    }

    /**
     * ログイン済みユーザーがコメントを送信でき、コメント数が増加するテスト
     */
    public function test_logged_in_user_can_post_comment_and_count_increases()
    {
        // ユーザー作成
        $user = \App\Models\User::factory()->create();
        // 商品作成
        $item = \App\Models\Item::factory()->create([
            'name' => 'コメントテスト商品',
        ]);
        // ログイン
        $this->actingAs($user);
        // コメント送信前の状態（コメント数0）
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee('0'); // コメント数初期値

        // コメント送信（POSTリクエスト）
        $postResponse = $this->post('/comment', [
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);
        $postResponse->assertStatus(302); // リダイレクト

        // 再度詳細ページでコメント数が1に増加していることを確認
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee('1'); // コメント数増加
        $response->assertSee('テストコメント'); // コメント内容表示
    }
}
