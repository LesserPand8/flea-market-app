<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Profile;

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

        if ($page === 'buy') {
            // 購入した商品
            $items = Item::whereHas('purchases', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
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
        } else {
            // 出品した商品
            $items = Item::whereHas('sellers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
        }

        return view('profile', compact('user', 'items', 'page', 'profile', 'tradeCount'));
    }
}
