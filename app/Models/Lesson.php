<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    
    public function tests()
    {
        return $this->hasMany('App\Models\LessonTest', 'lesson_id', 'id');
    }
}
