<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Requests\TransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Good;
use App\Models\Transaction;

class DetailController extends Controller
{
    public function detail($id)
    {
        $item = Item::findOrFail($id);
        $isLiked = false;
        if (Auth::check()) {
            $user = Auth::user();
            $isLiked = $item->goods()->where('user_id', $user->id)->exists();
        }
        return view('detail', compact('item', 'isLiked'));
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

    public function trade(TransactionRequest $request, $item_id)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        $item = Item::findOrFail($item_id);
        $userId = Auth::id();

        // 他のユーザーが既に取引を開始しているかチェック
        $otherTransaction = Transaction::where('item_id', $item->id)
            ->where('user_id', '!=', $userId)
            ->first();

        if ($otherTransaction) {
            return redirect()->back()->withErrors([
                'trade' => '他のユーザーが既にこの商品の取引を開始しています。',
            ]);
        }

        $existingTransaction = Transaction::where('user_id', $userId)
            ->where('item_id', $item->id)
            ->first();

        if ($existingTransaction) {
            return redirect()->back()->withErrors([
                'trade' => 'この商品の取引はすでに開始されています。',
            ]);
        }

        // 取引テーブルに新しい取引を作成
        $transaction = new Transaction();
        $transaction->user_id = $userId;
        $transaction->item_id = $item->id;
        $transaction->save();

        return redirect()->back()->with('success', '取引を開始しました。');
    }
}
