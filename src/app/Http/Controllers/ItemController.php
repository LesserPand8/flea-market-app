<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Good;
use App\Models\Comment;

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

    public function comment(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        $item = Item::findOrFail($request->input('item_id'));
        $comment = new Comment();
        $comment->user_id = Auth::id();
        $comment->item_id = $item->id;
        $comment->comment = $request->input('comment');
        $comment->save();

        return redirect()->back();
    }

    public function purchase(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        return view('purchase');
    }

    public function goods(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        $item = Item::findOrFail($request->input('item_id'));
        $good = new Good();
        $good->user_id = Auth::id();
        $good->item_id = $item->id;
        $good->save();

        return redirect()->back();
    }
}
