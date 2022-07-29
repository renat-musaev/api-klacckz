<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseVideo extends Model
{
    use HasFactory;
    
    public function tests()
    {
        return $this->hasMany('App\Models\CourseVideoTest', 'video_id', 'id');
    }
}
