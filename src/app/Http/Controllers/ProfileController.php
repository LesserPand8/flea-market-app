<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Message;

class ProfileController extends Controller
{
    public function mypage(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();
        $page = $request->query('page', 'sell'); // デフォルトは'sell'

        // 取引中件数を事前算出
        $tradeCount = Item::whereHas('trades', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        // 新規メッセージ数を計算
        $newMessageCount = 0;

        // 自分が取引開始した商品
        $myTradeItems = Item::whereHas('trades', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        // 自分が出品した商品で他者が取引開始したもの
        $othersTradeItems = Item::whereHas('sellers', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereHas('trades', function ($query) use ($user) {
            $query->where('user_id', '!=', $user->id);
        })->get();

        // 全取引中商品をマージ
        $allTradeItems = $myTradeItems->merge($othersTradeItems)->unique('id');

        foreach ($allTradeItems as $item) {
            // 自分の最新メッセージを取得
            $myLatestMessage = Message::where('item_id', $item->id)
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($myLatestMessage) {
                // 自分の最新メッセージより新しい相手のメッセージ数
                $newCount = Message::where('item_id', $item->id)
                    ->where('user_id', '!=', $user->id)
                    ->where('created_at', '>', $myLatestMessage->created_at)
                    ->count();
                $newMessageCount += $newCount;
            } else {
                // 自分のメッセージがない場合、相手の全メッセージ数
                $newCount = Message::where('item_id', $item->id)
                    ->where('user_id', '!=', $user->id)
                    ->count();
                $newMessageCount += $newCount;
            }
        }

        if ($page === 'buy') {
            // 購入した商品
            $items = Item::whereHas('purchases', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
            $itemNewMessageCounts = [];
        } elseif ($page === 'trade') {
            // 取引中の商品：自分が取引開始した商品
            $myTrades = Item::whereHas('trades', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();

            // 自分が出品した商品で他者が取引開始したもの
            $othersTradeOnMySells = Item::whereHas('sellers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->whereHas('trades', function ($query) use ($user) {
                $query->where('user_id', '!=', $user->id);
            })->get();

            // 両方をマージ（重複削除）
            $items = $myTrades->merge($othersTradeOnMySells)->unique('id')->values();

            // 各商品ごとの新規メッセージ数と最新メッセージ日時を計算
            $itemNewMessageCounts = [];
            $itemLatestMessageTimes = [];

            foreach ($items as $item) {
                $myLatestMessage = Message::where('item_id', $item->id)
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($myLatestMessage) {
                    $newCount = Message::where('item_id', $item->id)
                        ->where('user_id', '!=', $user->id)
                        ->where('created_at', '>', $myLatestMessage->created_at)
                        ->count();
                } else {
                    $newCount = Message::where('item_id', $item->id)
                        ->where('user_id', '!=', $user->id)
                        ->count();
                }
                $itemNewMessageCounts[$item->id] = $newCount;

                // 最新の相手メッセージの日時を取得（ソート用）
                $latestOpponentMessage = Message::where('item_id', $item->id)
                    ->where('user_id', '!=', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $itemLatestMessageTimes[$item->id] = $latestOpponentMessage
                    ? $latestOpponentMessage->created_at
                    : null;
            }

            // 最新メッセージが来た順にソート（新しい順）
            $items = $items->sortByDesc(function ($item) use ($itemLatestMessageTimes) {
                return $itemLatestMessageTimes[$item->id] ?? '1970-01-01 00:00:00';
            })->values();
        } else {
            // 出品した商品
            $items = Item::whereHas('sellers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
            $itemNewMessageCounts = [];
        }

        return view('profile', compact('user', 'items', 'page', 'profile', 'tradeCount', 'newMessageCount', 'itemNewMessageCounts'));
    }
}
