<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteVideo extends Model
{
    use HasFactory;

    protected $table = 'favorite_videos';

    public function book()
    {
        return $this->belongsTo('App\Models\Book');
    }
}
