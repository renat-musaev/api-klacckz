<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteLesson extends Model
{
    use HasFactory;

    protected $table = 'favorite_lessons';

    public function book()
    {
        return $this->belongsTo('App\Models\Book');
    }
}
