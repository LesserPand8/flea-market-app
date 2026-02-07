<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Transaction;
use App\Models\Message;
use App\Models\Evaluation;
use App\Models\Purchase;
use App\Mail\TradeCompletedNotification;
use App\Http\Requests\TradingChatRequest;
use App\Http\Requests\EvaluationRequest;

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

        // 取引完了状態を確認
        $transaction = $myTransaction ?? $othersTransaction;
        $isCompleted = $transaction ? $transaction->is_completed : false;

        // 出品者側で購入者が評価済みの場合、自動でモーダルを表示
        $showEvaluationModal = false;
        if (!$isPurchaser && $isCompleted) {
            $showEvaluationModal = true;
        }

        return view('trading-chat', compact('item', 'currentUser', 'otherUser', 'profile', 'isPurchaser', 'otherTradeItems', 'messages', 'isCompleted', 'showEvaluationModal'));
    }

    public function finish(EvaluationRequest $request, $item_id)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $validated = $request->validated();
        $currentUser = Auth::user();
        $item = Item::findOrFail($item_id);

        // 自分が購入者か出品者かを判定
        $myTransaction = Transaction::where('item_id', $item_id)
            ->where('user_id', $currentUser->id)
            ->first();

        $isSeller = $item->sellers()->where('user_id', $currentUser->id)->exists();

        // 取引相手のIDを決定
        if ($myTransaction) {
            // 自分が購入者の場合、出品者のIDを評価対象に
            $evaluatedUserId = $item->sellers()->first()->id;
        } elseif ($isSeller) {
            // 自分が出品者の場合、購入者のIDを評価対象に
            $othersTransaction = Transaction::where('item_id', $item_id)
                ->where('user_id', '!=', $currentUser->id)
                ->first();

            if (!$othersTransaction) {
                return redirect('/mypage?page=trade')->withErrors(['evaluation_score' => '取引が見つかりません。']);
            }
            $evaluatedUserId = $othersTransaction->user_id;
        } else {
            return redirect('/mypage?page=trade')->withErrors(['evaluation_score' => '取引が見つかりません。']);
        }

        // 評価を保存（user_idは評価対象者）
        Evaluation::create([
            'user_id' => $evaluatedUserId,
            'evaluation_score' => $validated['evaluation_score'],
        ]);

        // 取引の完了処理
        if ($myTransaction) {
            // 購入者が評価した場合
            if ($myTransaction->is_completed) {
                // 既に完了フラグが立っている場合（出品者が先に評価済み）
                // 両者評価済みなのでPurchaseテーブルに登録して取引を削除

                // transactionsテーブルから購入情報を取得
                if ($myTransaction->method && $myTransaction->full_address) {
                    Purchase::create([
                        'item_id' => $item->id,
                        'user_id' => $currentUser->id,
                        'method' => $myTransaction->method,
                        'full_address' => $myTransaction->full_address,
                    ]);
                }

                $myTransaction->delete();
            } else {
                // まだ出品者が評価していない場合
                // 完了フラグを立てる
                $myTransaction->is_completed = true;
                $myTransaction->save();

                // 出品者にメール送信
                $seller = $item->sellers()->first();
                Mail::to($seller->email)->send(new TradeCompletedNotification($item, $currentUser, $seller));
            }
        } elseif ($isSeller) {
            // 出品者が評価した場合
            $othersTransaction = Transaction::where('item_id', $item_id)
                ->where('user_id', '!=', $currentUser->id)
                ->first();

            if ($othersTransaction && $othersTransaction->is_completed) {
                // 購入者が既に評価済み（is_completed = true）なのでPurchaseテーブルに登録して削除

                // transactionsテーブルから購入情報を取得
                if ($othersTransaction->method && $othersTransaction->full_address) {
                    Purchase::create([
                        'item_id' => $item->id,
                        'user_id' => $othersTransaction->user_id,
                        'method' => $othersTransaction->method,
                        'full_address' => $othersTransaction->full_address,
                    ]);
                }

                $othersTransaction->delete();
            }
        }

        return redirect('/')->with('success', '取引が完了しました。');
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

        $item = Item::findOrFail($message->item_id);

        return view('edit-message', compact('message', 'item'));
    }

    public function updateMessage(TradingChatRequest $request, $message_id)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $message = Message::findOrFail($message_id);

        // 自分のメッセージであることを確認
        if ($message->user_id !== Auth::id()) {
            return redirect()->back()->withErrors(['message' => 'このメッセージは編集できません。']);
        }

        $validated = $request->validated();

        // メッセージ本文を更新
        $message->message = $validated['message_text'];

        // 画像がアップロードされた場合は更新
        if ($request->hasFile('chat_image')) {
            $imageFile = $request->file('chat_image');
            $imageName = uniqid() . '_' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/messages', $imageName);
            $message->image = 'storage/messages/' . $imageName;
        }

        $message->save();

        return redirect("/trade/chat/{$message->item_id}")->with('success', 'メッセージを更新しました。');
    }
}
