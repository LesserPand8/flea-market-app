<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
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
        $user = Auth::user();
        return view('profile-setting', compact('user'));
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

    public function sell()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        $categories = \App\Models\Category::all();
        return view('sell', compact('categories'));
    }

    public function sellRegister(ExhibitionRequest $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $validated = $request->validated();

        // 画像を保存
        $imageFile = $request->file('image');
        $imageName = uniqid() . '_' . $imageFile->getClientOriginalName();
        $imageFile->storeAs('public/items', $imageName);

        // 商品を保存
        $item = Item::create([
            'name' => $validated['name'],
            'image' => 'storage/items/' . $imageName,
            'brand' => $validated['brand'] ?? null,
            'price' => $validated['price'],
            'description' => $validated['description'],
            'condition' => $validated['condition'],
        ]);

        // 出品者の紐付け（sellsテーブル）
        $item->sellers()->sync([Auth::id()]);

        // カテゴリーの紐付け
        $item->categories()->sync($validated['category']);

        return redirect('/')->with('success', 'Item listed successfully!');
    }
}
