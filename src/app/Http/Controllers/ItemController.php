<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Good;
use App\Models\Sell;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        $keyword = $request->query('keyword', '');
        if ($tab === 'mylist') {
            if (!Auth::check()) {
                $items = collect();
            } else {
                $user = Auth::user();
                $itemIds = Good::where('user_id', $user->id)->pluck('item_id');
                $query = Item::whereIn('id', $itemIds);
                if (!empty($keyword)) {
                    $query->where('name', 'like', '%' . $keyword . '%');
                }
                $items = $query->get();
            }
        } else {
            if (Auth::check()) {
                $myItemIds = Sell::where('user_id', Auth::id())->pluck('item_id');
                $query = Item::whereNotIn('id', $myItemIds);
            } else {
                $query = Item::query();
            }
            if (!empty($keyword)) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }
            $items = $query->get();
        }
        return view('index', compact('items', 'tab'));
    }

    // 検索専用アクションは不要
}
