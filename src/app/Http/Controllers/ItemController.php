<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Good;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();
        return view('index', compact('items'));
    }

    public function profileSetting()
    {
        return view('profile-setting');
    }

    public function detail($id)
    {
        $item = Item::findOrFail($id);
        return view('detail', compact('item'));
    }

    public function mylist()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        $user = Auth::user();
        $itemIds = $user->goods()->pluck('item_id');
        $items = \App\Models\Item::whereIn('id', $itemIds)->get();
        return view('index', compact('items'));
    }
}
