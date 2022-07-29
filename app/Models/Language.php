<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    public function classrooms()
    {
        return $this->hasMany('App\Models\Classroom', 'language_id', 'id');
    }

    public function classroom()
    {
        return $this->hasOne('App\Models\Classroom', 'language_id', 'id');
    }

    public function courses()
    {
        return $this->hasMany('App\Models\CourseCategory');
    }

    public function courseCategories()
    {
        return $this->hasMany('App\Models\CourseCategory');
    }
}
