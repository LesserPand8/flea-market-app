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

        if ($page === 'buy') {
            // 購入した商品
            $items = Item::whereHas('purchases', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
        } else {
            // 出品した商品
            $items = Item::whereHas('sellers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
        }

        return view('profile', compact('user', 'items', 'page', 'profile'));
    }
}
