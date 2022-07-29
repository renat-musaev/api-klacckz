<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function username()
    {
        return 'email';
    }

    public function pagePayment()
    {
        return $this->hasOne('App\Models\PagePayment');
    }

    public function pagePayments()
    {
        return $this->hasMany('App\Models\PagePayment');
    }

    public function lessonPayment()
    {
        return $this->hasOne('App\Models\LessonPayment');
    }

    public function lessonPayments()
    {
        return $this->hasMany('App\Models\LessonPayment');
    }

    public function videoPayment()
    {
        return $this->hasOne('App\Models\VideoPayment');
    }

    public function videoPayments()
    {
        return $this->hasMany('App\Models\VideoPayment');
    }

    public function coursePayment()
    {
        return $this->hasOne('App\Models\CoursePayment');
    }

    public function coursePayments()
    {
        return $this->hasMany('App\Models\CoursePayment');
    }

    public function comboPayment()
    {
        return $this->hasOne('App\Models\ComboPayment');
    }

    public function comboPayments()
    {
        return $this->hasMany('App\Models\ComboPayment');
    }

    public function notificationAndroid()
    {
        return $this->hasMany('App\Models\Notification', 'id', 'notification_id');
    }
}
