<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Good;

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
}
