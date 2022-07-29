<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonPayment extends Model
{
    use HasFactory;
    
    protected $casts = [
        'end_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function classroom()
    {
        return $this->belongsTo('App\Models\Classroom');
    }
}
