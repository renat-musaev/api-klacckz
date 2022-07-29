<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class CourseCategory extends Model
{
    use HasFactory;
    use NodeTrait;
    
    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo('App\Models\CourseCategory', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('App\Models\CourseCategory', 'parent_id');
    }

    public function courses()
    {
        return $this->hasMany('App\Models\Course', 'category_id');
    }

    public function language()
    {
        return $this->belongsTo('App\Models\CourseLanguage', 'language_id');
    }
}
