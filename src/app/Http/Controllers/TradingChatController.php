<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Transaction;

class TradingChatController extends Controller
{
    public function show($item_id)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $item = Item::findOrFail($item_id);
        $currentUser = Auth::user();
        $profile = Profile::where('user_id', $currentUser->id)->first();

        // 自分が取引を開始した商品か確認
        $myTransaction = Transaction::where('item_id', $item_id)
            ->where('user_id', $currentUser->id)
            ->first();

        // 自分が出品した商品で他者が取引開始したか確認
        $isSeller = $item->sellers()->where('user_id', $currentUser->id)->exists();
        $othersTransaction = null;

        if ($isSeller) {
            $othersTransaction = Transaction::where('item_id', $item_id)
                ->where('user_id', '!=', $currentUser->id)
                ->first();
        }

        // どちらでもない場合はエラー
        if (!$myTransaction && !$othersTransaction) {
            return redirect('/mypage?page=trade')->withErrors(['trade' => '取引が見つかりません。']);
        }

        // 購入者かどうかを判定
        $isPurchaser = (bool)$myTransaction;

        // 相手のユーザー情報を取得
        if ($isPurchaser) {
            // 自分が購入者の場合、出品者を取得
            $otherUser = $item->sellers()->first();
        } else {
            // 自分が出品者の場合、購入者（取引開始者）を取得
            $otherUser = \App\Models\User::find($othersTransaction->user_id);
        }

        // その他の取引中商品を取得（現在表示中の商品を除く）
        // 自分が取引開始した商品
        $myTrades = Item::whereHas('trades', function ($query) use ($currentUser) {
            $query->where('user_id', $currentUser->id);
        })->where('id', '!=', $item_id)->get();

        // 自分が出品した商品で他者が取引開始したもの
        $othersTradeOnMySells = Item::whereHas('sellers', function ($query) use ($currentUser) {
            $query->where('user_id', $currentUser->id);
        })->whereHas('trades', function ($query) use ($currentUser) {
            $query->where('user_id', '!=', $currentUser->id);
        })->where('id', '!=', $item_id)->get();

        // 両方をマージ（重複削除）
        $otherTradeItems = $myTrades->merge($othersTradeOnMySells)->unique('id')->values();

        return view('trading-chat', compact('item', 'currentUser', 'otherUser', 'profile', 'isPurchaser', 'otherTradeItems'));
    }

    public function finish(Request $request, $item_id)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 取引完了処理
        $transaction = Transaction::where('item_id', $item_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($transaction) {
            $transaction->delete();
        }

        return redirect('/mypage?page=trade')->with('success', '取引が完了しました。');
    }
}
