<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\CoursePayment;
use App\Models\LessonPayment;
use App\Models\PagePayment;
use App\Models\VideoPayment;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // ГДЗ
    protected $month_1_pages = 1;
    protected $price_1_pages = 2000;
    protected $month_2_pages = 3;
    protected $price_2_pages = 4000;
    protected $month_3_pages = 6;
    protected $price_3_pages = 6000;
    protected $month_4_pages = 9;               // 9-месяцев
    protected $price_4_pages = 9000;            // 9-месяцев 9000
    protected $month_5_pages = 12;              // 12-месяцев
    protected $price_5_pages = 10000;           // 12-месяцев 10000₸

    // Видеоесептер
    protected $month_1_video = 1;               // 1-месяц
    protected $price_1_video = 3000;            // 1-месяц 3000, тенге
    protected $month_2_video = 3;               // 3-месяца
    protected $price_2_video = 7000;            // 3-месяца 7000
    protected $month_3_video = 6;               // 6-месяцев
    protected $price_3_video = 12000;           // 6-месяцев 12000
    protected $month_4_video = 9;               // 9-месяцев
    protected $price_4_video = 16000;           // 9-месяцев 16000
    protected $month_5_video = 12;              // 12-месяцев
    protected $price_5_video = 20000;           // 12-месяцев 20000

    // Видеоуроки
    protected $month_1_lessosn = 1;             // 1-месяц
    protected $price_1_lessosn = 4000;          // 1-месяц 4000
    protected $month_2_lessosn = 3;             // 3-месяца
    protected $price_2_lessosn = 12000;         // 3-месяца 12000
    protected $month_3_lessosn = 6;             // 6-месяцев
    protected $price_3_lessosn = 20000;         // 6-месяцев 20000
    protected $month_4_lessosn = 9;            // 12-месяцев
    protected $price_4_lessosn = 25000;         // 12-месяцев 30000
    protected $month_5_lessosn = 12;            // 12-месяцев
    protected $price_5_lessosn = 30000;         // 12-месяцев 30000

    // Комбо
    protected $month_1_combo = 1;               // 1 месяц
    protected $price_1_combo = 5000;            // 1 месяц 5000
    protected $month_2_combo = 3;               // 3 месяца
    protected $price_2_combo = 10000;           // 3 месяца 10000
    protected $month_3_combo = 6;               // 6-месяцев
    protected $price_3_combo = 15000;           // 6-месяцев 15000
    protected $month_4_combo = 9;               // 9-месяцев
    protected $price_4_combo = 20000;           // 9-месяцев 20000
    protected $month_5_combo = 12;              // 12-месяцев
    protected $price_5_combo = 25000;           // 12-месяцев 25000

    //Секретный ключ интернет-магазина
    protected $walletone_key = "51744c6d5932413076324d7b4336696a5c38343868634c74696778";

    // Отправка SMS
    protected function sendSMS($phone, $message)
    {
        $client = new Client(['verify' => false]);
        
        try {
            $client->request('POST', config('services.smsc.link'), [
                'form_params' => [
                    'login' => config('services.smsc.login'),
                    'psw' => config('services.smsc.password'),
                    'phones' => "+7".$phone,
                    'mes' => $message
                ]
            ]);
        } catch (GuzzleException $e) {
            dd($e->getMessage());
        }
    }

    // Оплата за ГДЗ
    protected function payPerPage($uid, $cid, $amount, $code, $combo = false)
    {
        // Пытаемся получить дыннае о действующей подписки
        $subscription = PagePayment::where([
            ['user_id', $uid],
            ['classroom_id', $cid],
            ['end_date', '>', Carbon::now()->format('Y-m-d')]
        ])->orderBy('id', 'desc')->first();

        if (!$combo)
        {
            // Если подписка еще не закончена
            if ($subscription && isset($subscription->end_date)) {
                $carbon = new Carbon($subscription->end_date);
                if ($amount == $this->price_1_pages) {
                    $date = $carbon->addMonth();
                } elseif ($amount == $this->price_2_pages) {
                    $date = $carbon->addMonths($this->month_2_pages);
                } elseif ($amount == $this->price_3_pages) {
                    $date = $carbon->addMonths($this->month_3_pages);
                } elseif ($amount == $this->price_4_pages) {
                    $date = $carbon->addMonths($this->month_4_pages);
                } elseif ($amount == $this->price_5_pages) {
                    $date = $carbon->addMonths($this->month_5_pages);
                } else {
                    return false;
                }
            } else if ($amount == $this->price_1_pages) {
                $date = Carbon::now()->addMonth();
            } elseif ($amount == $this->price_2_pages) {
                $date = Carbon::now()->addMonths($this->month_2_pages);
            } elseif ($amount == $this->price_3_pages) {
                $date = Carbon::now()->addMonths($this->month_3_pages);
            } elseif ($amount == $this->price_4_pages) {
                $date = Carbon::now()->addMonths($this->month_4_pages);
            } elseif ($amount == $this->price_5_pages) {
                $date = Carbon::now()->addMonths($this->month_5_pages);
            } else {
                return false;
            }
        } else if ($this->comboDate($subscription, $amount)) {
            $date = $this->comboDate($subscription, $amount);
        } else {
            return false;
        }

        $pay = new PagePayment();
        $pay->user_id = $uid;
        $pay->classroom_id = $cid;
        $pay->end_date = $date;
        $pay->vendor_code = $code;
        $pay->status = 1;
        $pay->save();
    }

    // Оплата за видео решения
    protected function payPerVideo($uid, $cid, $amount, $code, $combo = false)
    {
        // Пытаемся получить дыннае о действующей подписки
        $subscription = VideoPayment::where([
            ['user_id', $uid],
            ['classroom_id', $cid],
            ['end_date', '>', Carbon::now()->format('Y-m-d')]
        ])->orderBy('id', 'desc')->first();

        if (!$combo)
        {
            // Если подписка еще не закончена
            if ($subscription && isset($subscription->end_date)) {
                $carbon = new Carbon($subscription->end_date);
                if ($amount == $this->price_1_video) {
                    $date = $carbon->addMonth();
                } elseif ($amount == $this->price_2_video) {
                    $date = $carbon->addMonths($this->month_2_video);
                } elseif ($amount == $this->price_3_video) {
                    $date = $carbon->addMonths($this->month_3_video);
                } elseif ($amount == $this->price_4_video) {
                    $date = $carbon->addMonths($this->month_4_video);
                } elseif ($amount == $this->price_5_video) {
                    $date = $carbon->addMonths($this->month_5_video);
                } elseif ($amount == 'free') {
                    $date = $carbon->addDays(2);
                } else {
                    return false;
                }
            } else if ($amount == $this->price_1_video) {
                $date = Carbon::now()->addMonth();
            } elseif ($amount == $this->price_2_video) {
                $date = Carbon::now()->addMonths($this->month_2_video);
            } elseif ($amount == $this->price_3_video) {
                $date = Carbon::now()->addMonths($this->month_3_video);
            } elseif ($amount == $this->price_4_video) {
                $date = Carbon::now()->addMonths($this->month_4_video);
            } elseif ($amount == 'free') {
                $date = Carbon::now()->addDays(2);
            } elseif ($amount == $this->price_5_video) {
                $date = Carbon::now()->addMonths($this->month_5_video);
            } else {
                return false;
            }
        } else if ($this->comboDate($subscription, $amount)) {
            $date = $this->comboDate($subscription, $amount);
        } else {
            return false;
        }

        $pay = new VideoPayment();
        $pay->user_id = $uid;
        $pay->classroom_id = $cid;
        $pay->end_date = $date;
        $pay->vendor_code = $code;
        $pay->status = 1;
        if ($amount == 'free') {
            $pay->free = 2;
        }
        $pay->save();
    }

    // Оплата за видеоуроки
    protected function payPerLessosn($uid, $cid, $amount, $code, $combo = false)
    {
        // Пытаемся получить дыннае о действующей подписки
        $subscription = LessonPayment::where([
            ['user_id', $uid],
            ['classroom_id', $cid],
            ['end_date', '>', Carbon::now()->format('Y-m-d')]
        ])->orderBy('id', 'desc')->first();

        if (!$combo)
        {
            // Если подписка еще не закончена
            if ($subscription && isset($subscription->end_date)) {
                $carbon = new Carbon($subscription->end_date);
                if ($amount == $this->price_1_lessosn) {
                    $date = $carbon->addMonth();
                } elseif ($amount == $this->price_2_lessosn) {
                    $date = $carbon->addMonths($this->month_2_lessosn);
                } elseif ($amount == $this->price_3_lessosn) {
                    $date = $carbon->addMonths($this->month_3_lessosn);
                } elseif ($amount == $this->price_4_lessosn) {
                    $date = $carbon->addMonths($this->month_4_lessosn);
                } elseif ($amount == $this->price_5_lessosn) {
                    $date = $carbon->addMonths($this->month_5_lessosn);
                } elseif ($amount == 'free') {
                    $date = $carbon->addDays(2);
                } else {
                    return false;
                }
            } else if ($amount == $this->price_1_lessosn) {
                $date = Carbon::now()->addMonth();
            } elseif ($amount == $this->price_2_lessosn) {
                $date = Carbon::now()->addMonths($this->month_2_lessosn);
            } elseif ($amount == $this->price_3_lessosn) {
                $date = Carbon::now()->addMonths($this->month_3_lessosn);
            } elseif ($amount == $this->price_4_lessosn) {
                $date = Carbon::now()->addMonths($this->month_4_lessosn);
            } elseif ($amount == $this->price_5_lessosn) {
                $date = Carbon::now()->addMonths($this->month_5_lessosn);
            } elseif ($amount == 'free') {
                $date = Carbon::now()->addDays(2);
            } else {
                return false;
            }
        } else if ($this->comboDate($subscription, $amount)) {
            $date = $this->comboDate($subscription, $amount);
        } else {
            return false;
        }

        $pay = new LessonPayment();
        $pay->user_id = $uid;
        $pay->classroom_id = $cid;
        $pay->end_date = $date;
        $pay->vendor_code = $code;
        $pay->status = 1;
        if ($amount == 'free') {
            $pay->free = 2;
        }
        $pay->save();
    }

    //
    public function payPerCourse($uid, $cid, $amount, $code)
    {
        $course = Course::where('id', $cid)->first();
        
        if ($course && isset($course->id)) {

            // Пытаемся получить дыннае о действующей подписки
            $subscription = CoursePayment::where([
                ['user_id', $uid],
                ['course_id', $cid],
                ['end_date', '>', Carbon::now()->format('Y-m-d')]
            ])->orderBy('id', 'desc')->first();

            // Если подписка еще не закончена
            if ($subscription && isset($subscription->end_date)) {
                $carbon = new Carbon($subscription->end_date);
                if ($amount == $course->price_1) {
                    $date = $carbon->addMonth();
                } elseif ($amount == $course->price_2) {
                    $date = $carbon->addMonths(3);
                } elseif ($amount == $course->price_3) {
                    $date = $carbon->addMonths(6);
                } elseif ($amount == $course->price_4) {
                    $date = $carbon->addMonths(12);
                } else {
                    return false;
                }
            } else if ($amount == $course->price_1) {
                $date = Carbon::now()->addMonth();
            } elseif ($amount == $course->price_2) {
                $date = Carbon::now()->addMonths(3);
            } elseif ($amount == $course->price_3) {
                $date = Carbon::now()->addMonths(6);
            } elseif ($amount == $course->price_4) {
                $date = Carbon::now()->addMonths(12);
            } else {
                return false;
            }

            $pay = new CoursePayment();
            $pay->user_id = $uid;
            $pay->course_id = $cid;
            $pay->end_date = $date;
            $pay->vendor_code = $code;
            $pay->status = 1;
            $pay->save();
        }
    }

    //
    private function comboDate($subscription, $amount)
    {
        $date = false;
        // Если подписка еще не закончена
        if ($subscription && isset($subscription->end_date)) {
            $carbon = new Carbon($subscription->end_date);
            if ($amount == $this->price_1_combo) {
                $date = $carbon->addMonth();
            } elseif ($amount == $this->price_2_combo) {
                $date = $carbon->addMonths($this->month_2_combo);
            } elseif ($amount == $this->price_3_combo) {
                $date = $carbon->addMonths($this->month_3_combo);
            } elseif ($amount == $this->price_4_combo) {
                $date = $carbon->addMonths($this->month_4_combo);
            } elseif ($amount == $this->price_5_combo) {
                $date = $carbon->addMonths($this->month_5_combo);
            }
        } else if ($amount == $this->price_1_combo) {
            $date = Carbon::now()->addMonth();
        } elseif ($amount == $this->price_2_combo) {
            $date = Carbon::now()->addMonths($this->month_2_combo);
        } elseif ($amount == $this->price_3_combo) {
            $date = Carbon::now()->addMonths($this->month_3_combo);
        } elseif ($amount == $this->price_4_combo) {
            $date = Carbon::now()->addMonths($this->month_4_combo);
        } elseif ($amount == $this->price_5_combo) {
            $date = Carbon::now()->addMonths($this->month_5_combo);
        }

        return $date;
    }
    
    //
    private function present()
    {

    }
}
