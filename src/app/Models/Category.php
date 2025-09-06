<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = ['category'];
    public $timestamps = true;

    public function items()
    {
        return $this->belongsToMany(Item::class, 'category_item');
    }
}
