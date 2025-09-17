<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Purchase;

class PurchaseController extends Controller
{
    public function purchase($id)
    {
        $item = Item::findOrFail($id);
        $profile = Profile::where('user_id', auth()->id())->first();
        if (!Auth::check()) {
            return redirect('/login');
        }
        return view('purchase', compact('item', 'profile'));
    }

    public function purchaseDecision(PurchaseRequest $request, $item_id)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        // 購入処理（purchasesテーブルにレコードを追加）
        Purchase::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'method' => $request->input('method'),
            'full_address' => $request->input('full_address'),
        ]);

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
}
