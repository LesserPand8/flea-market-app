<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'comment',
    ];

    // コメント投稿者のユーザー情報を取得
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
