<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo('App\Models\CourseCategory');
    }

    public function videos()
    {
        return $this->hasMany('App\Models\CourseVideo', 'course_id');
    }
    
    public function payment()
    {
        return $this->hasOne('App\Models\CoursePayment');
    }
}
