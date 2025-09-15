<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Good;
use App\Models\Comment;
use App\Models\Profile;
use App\Models\Purchase;

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
        $profile = Profile::where('user_id', $user->id)->first();
        return view('profile-setting', compact('user', 'profile'));
    }

    public function detail($id)
    {
        $item = Item::findOrFail($id);
        $isLiked = false;
        if (\Auth::check()) {
            $user = \Auth::user();
            $isLiked = $item->goods()->where('user_id', $user->id)->exists();
        }
        return view('detail', compact('item', 'isLiked'));
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

    public function comment(CommentRequest $request)
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

    public function purchase($id)
    {
        $item = Item::findOrFail($id);
        $profile = Profile::where('user_id', auth()->id())->first();
        if (!Auth::check()) {
            return redirect('/login');
        }
        return view('purchase', compact('item', 'profile'));
    }

    public function goods(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        $item = Item::findOrFail($request->input('item_id'));
        $userId = Auth::id();
        $existingGood = Good::where('user_id', $userId)->where('item_id', $item->id)->first();
        if ($existingGood) {
            // 既にいいねしていれば解除（削除）
            $existingGood->delete();
        } else {
            // いいねしていなければ追加
            $good = new Good();
            $good->user_id = $userId;
            $good->item_id = $item->id;
            $good->save();
        }
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

    public function profileUpdate(ProfileRequest $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        $user->name = $request->input('name');
        $user->save();

        // profilesテーブルの更新
        $profile = Profile::firstOrNew(['user_id' => $user->id]);
        if ($request->hasFile('profile_image')) {
            $imageFile = $request->file('profile_image');
            $imageName = uniqid() . '_' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/profiles', $imageName);
            $profile->profile_image = 'storage/profiles/' . $imageName;
        }
        $profile->postal_code = $request->input('postal_code');
        $profile->address = $request->input('address');
        $profile->building_name = $request->input('building_name');
        $profile->save();

        return redirect('/');
    }

    public function addressChanging($item_id)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();
        return view('address-changing', compact('user', 'profile', 'item_id'));
    }

    public function addressUpdate(AddressRequest $request, $item_id)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // profilesテーブルの更新
        $profile = Profile::where('user_id', $user->id)->firstOrFail();
        $profile->postal_code = $request->input('postal_code');
        $profile->address = $request->input('address');
        $profile->building_name = $request->input('building_name');
        $profile->save();
        return redirect("/purchase/{$item_id}");
    }

    public function purchaseDecision(PurchaseRequest $request, $item_id)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        // profilesテーブルを確認し、なければ住所登録画面へリダイレクト
        $profile = Profile::where('user_id', $user->id)->first();
        if (!$profile || !$profile->postal_code || !$profile->address) {
            return redirect("/purchase/address/{$item_id}")->with('error', '住所情報を登録してください。');
        }


        // 購入処理（purchasesテーブルにレコードを追加）
        Purchase::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
        ]);

        return redirect('/');
    }
}
