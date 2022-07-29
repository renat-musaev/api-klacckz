<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    public function language()
    {
        return $this->belongsTo('App\Models\Language');
    }
    
    public function subjects()
    {
        return $this->hasMany('App\Models\Subject', 'classroom_id', 'id');
    }
    
    public function pagePayment()
    {
        return $this->hasOne('App\Models\PagePayment');
    }
    
    public function pagePayments()
    {
        return $this->hasMany('App\Models\PagePayment', 'classroom_id', 'id');
    }
    
    public function payments()
    {
        return $this->hasMany('App\Models\Payment', 'classroom_id', 'id');
    }
    
    public function lessonPayment()
    {
        return $this->hasOne('App\Models\LessonPayment');
    }
    
    public function lessonPayments()
    {
        return $this->hasMany('App\Models\LessonPayment', 'classroom_id', 'id');
    }
    
    public function videoPayment()
    {
        return $this->hasOne('App\Models\VideoPayment');
    }
    
    public function videoPayments()
    {
        return $this->hasMany('App\Models\VideoPayment', 'classroom_id', 'id');
    }
}
