<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Purchase;
use App\Models\Transaction;

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

        if ($request->input('method') === 'カード払い') {
            // 決済画面へリダイレクト（必要な情報をセッションで渡す）
            session([
                'purchase_item_id' => $item->id,
                'purchase_user_id' => $user->id,
                'purchase_method' => $request->input('method'),
                'purchase_full_address' => $request->input('full_address'),
            ]);
            // 取引中に追加
            Transaction::create([
                'item_id' => $item->id,
                'user_id' => $user->id,
            ]);
            return redirect()->route('payment.index');
        }

        // コンビニ払いも購入情報をセッションに保存
        session([
            'purchase_item_id' => $item->id,
            'purchase_user_id' => $user->id,
            'purchase_method' => $request->input('method'),
            'purchase_full_address' => $request->input('full_address'),
        ]);
        // 取引中に追加
        Transaction::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
        ]);
        return redirect("/trade/chat/{$item->id}");
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
