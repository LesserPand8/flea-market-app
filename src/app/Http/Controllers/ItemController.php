<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Good;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        if ($tab === 'mylist') {
            if (!Auth::check()) {
                $items = collect();
            } else {
                $user = Auth::user();
                $itemIds = Good::where('user_id', $user->id)->pluck('item_id');
                $items = Item::whereIn('id', $itemIds)->get();
            }
        } else {
            $items = Item::all();
        }
        return view('index', compact('items', 'tab'));
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $items = Item::where('name', 'like', '%' . $keyword . '%')->get();
        return view('index', compact('items'));
    }
}
