<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonTest extends Model
{
    use HasFactory;

    public function answers()
    {
        return $this->hasMany('App\Models\LessonTestAnswer', 'lesson_test_id', 'id');
    }
}
