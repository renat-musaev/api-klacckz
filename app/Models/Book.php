<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public function subject()
    {
        return $this->belongsTo('App\Models\Subject');
    }
    
    public function pages()
    {
        return $this->hasMany('App\Models\Page', 'book_id', 'id');
    }
    
    public function videos()
    {
        return $this->hasMany('App\Models\Video', 'book_id', 'id');
    }
    
    public function lessons()
    {
        return $this->hasMany('App\Models\Lesson', 'book_id', 'id');
    }
    
    public function pagePayment()
    {
        return $this->hasOne('App\Models\VideoPayment');
    }
    
    public function pagePayments()
    {
        return $this->hasMany('App\Models\VideoPayment', 'classroom_id', 'id');
    }
    
    public function videoPayment()
    {
        return $this->hasOne('App\Models\VideoPayment');
    }
    
    public function videoPayments()
    {
        return $this->hasMany('App\Models\VideoPayment', 'classroom_id', 'id');
    }

    public function favoriteGDZ()
    {
        return $this->hasOne('App\Models\FavoriteGDZ', 'book_id', 'id');
    }

    public function favoriteLesson()
    {
        return $this->hasOne('App\Models\FavoriteLesson', 'book_id', 'id');
    }

    public function favoriteVideo()
    {
        return $this->hasOne('App\Models\FavoriteVideo', 'book_id', 'id');
    }
}
