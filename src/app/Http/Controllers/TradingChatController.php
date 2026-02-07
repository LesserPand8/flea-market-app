<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Transaction;
use App\Models\Message;
use App\Http\Requests\TradingChatRequest;

class TradingChatController extends Controller
{
    public function show($item_id)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $item = Item::findOrFail($item_id);
        $currentUser = Auth::user();
        $profile = Profile::where('user_id', $currentUser->id)->first();

        // 自分が取引を開始した商品か確認
        $myTransaction = Transaction::where('item_id', $item_id)
            ->where('user_id', $currentUser->id)
            ->first();

        // 自分が出品した商品で他者が取引開始したか確認
        $isSeller = $item->sellers()->where('user_id', $currentUser->id)->exists();
        $othersTransaction = null;

        if ($isSeller) {
            $othersTransaction = Transaction::where('item_id', $item_id)
                ->where('user_id', '!=', $currentUser->id)
                ->first();
        }

        // どちらでもない場合はエラー
        if (!$myTransaction && !$othersTransaction) {
            return redirect('/mypage?page=trade')->withErrors(['trade' => '取引が見つかりません。']);
        }

        // 購入者かどうかを判定
        $isPurchaser = (bool)$myTransaction;

        // 相手のユーザー情報を取得
        if ($isPurchaser) {
            // 自分が購入者の場合、出品者を取得
            $otherUser = $item->sellers()->first();
        } else {
            // 自分が出品者の場合、購入者（取引開始者）を取得
            $otherUser = \App\Models\User::find($othersTransaction->user_id);
        }

        // その他の取引中商品を取得（現在表示中の商品を除く）
        // 自分が取引開始した商品
        $myTrades = Item::whereHas('trades', function ($query) use ($currentUser) {
            $query->where('user_id', $currentUser->id);
        })->where('id', '!=', $item_id)->get();

        // 自分が出品した商品で他者が取引開始したもの
        $othersTradeOnMySells = Item::whereHas('sellers', function ($query) use ($currentUser) {
            $query->where('user_id', $currentUser->id);
        })->whereHas('trades', function ($query) use ($currentUser) {
            $query->where('user_id', '!=', $currentUser->id);
        })->where('id', '!=', $item_id)->get();

        // 両方をマージ（重複削除）
        $otherTradeItems = $myTrades->merge($othersTradeOnMySells)->unique('id')->values();

        // メッセージを古い順に取得
        $messages = Message::where('item_id', $item_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('trading-chat', compact('item', 'currentUser', 'otherUser', 'profile', 'isPurchaser', 'otherTradeItems', 'messages'));
    }

    public function finish(Request $request, $item_id)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 取引完了処理
        $transaction = Transaction::where('item_id', $item_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($transaction) {
            $transaction->delete();
        }

        return redirect('/mypage?page=trade')->with('success', '取引が完了しました。');
    }

    public function message(TradingChatRequest $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $validated = $request->validated();

        $item_id = $validated['item_id'];
        $messageText = $validated['message_text'];
        $imagePath = null;

        // 画像をアップロード
        if ($request->hasFile('chat_image')) {
            $imageFile = $request->file('chat_image');
            $imageName = uniqid() . '_' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/messages', $imageName);
            $imagePath = 'storage/messages/' . $imageName;
        }

        // メッセージをデータベースに保存
        Message::create([
            'user_id' => Auth::id(),
            'item_id' => $item_id,
            'message' => $messageText,
            'image' => $imagePath,
        ]);

        return redirect()->back()->with('success', 'メッセージを送信しました。');
    }

    public function deleteMessage($message_id)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $message = Message::findOrFail($message_id);

        // 自分のメッセージであることを確認
        if ($message->user_id !== Auth::id()) {
            return redirect()->back()->withErrors(['message' => 'このメッセージは削除できません。']);
        }

        $item_id = $message->item_id;
        $message->delete();

        return redirect("/trade/chat/{$item_id}")->with('success', 'メッセージを削除しました。');
    }

    public function editMessage($message_id)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $message = Message::findOrFail($message_id);

        // 自分のメッセージであることを確認
        if ($message->user_id !== Auth::id()) {
            return redirect()->back()->withErrors(['message' => 'このメッセージは編集できません。']);
        }

        $item_id = $message->item_id;
        $message->delete();

        return redirect("/trade/chat/{$item_id}")->with('success', 'メッセージを編集しました。');
    }
}
