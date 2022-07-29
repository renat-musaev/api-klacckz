<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseVideoTest extends Model
{
    use HasFactory;

    public function answers()
    {
        return $this->hasMany('App\Models\CourseVideoTestAnswer', 'course_video_test_id', 'id');
    }
}
