<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'brand',
        'price',
        'description',
        'condition',
        'user_id'
    ];

    public function goods()
    {
        return $this->hasMany(Good::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item');
    }

    public function sellers()
    {
        return $this->belongsToMany(User::class, 'sells');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
