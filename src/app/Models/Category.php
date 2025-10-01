<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $fillable = ['category'];
    public $timestamps = true;

    public function items()
    {
        return $this->belongsToMany(Item::class, 'category_item');
    }
}
