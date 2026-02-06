<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class SellController extends Controller
{
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
