<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Basket;
use App\Models\Classroom;
use App\Models\ComboPayment;
use App\Models\Course;
use App\Models\PagePayment;
use App\Models\VideoPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KaspiController extends Controller
{
    //
    public function payment(Request $request)
    {
        //if ($request->ip() == '194.187.247.152') {
            if ($request->command == "check") {
                $request->validate([
                    'txn_id' => ['required'],
                    'account' => ['required', 'string'],
                    //'sum' => ['required', 'string'],
                ]);

                $json['txn_id'] = $request->txn_id;

                $basket = Basket::where([
                    'account' => $request->account,
                    ['created_at', '>=', Carbon::now()->subMinutes(60)->toDateTimeString()],
                ])->first();

                $result = 5; // Другая ошибка провайдера
                $comment = 'Ошибка провайдера';
                if ($basket && $basket->id && $basket->id > 0) {
                    if ($basket->paid == 1) {
                        $result = 3; // Заказ уже оплачен
                        $comment = 'The order has already been paid';
                    } else if ($basket->paid == 0) {

                        $basket->txn_id = $request->txn_id;
                        $basket->save();

                        $result = 0; // Счёт найден и доступен для оплаты

                        if ($basket->content_type == 1) {
                            $classroom = Classroom::find($basket->content_id);
                            if ($classroom) {
                                $comment = "ГДЗ / ".$classroom->name;
                            }
                        } elseif ($basket->content_type == 2) {
                            $classroom = Classroom::find($basket->content_id);
                            if ($classroom) {
                                $comment = "Видеорешение / ".$classroom->name;
                            }
                        } elseif ($basket->content_type == 3) {
                            $classroom = Classroom::find($basket->content_id);
                            if ($classroom) {
                                $comment = "Видеоуроки / ".$classroom->name;
                            }
                        } elseif ($basket->content_type == 4) {
                            $course = Course::find($basket->content_id);
                            if ($course) {
                                $comment = "Курс / ".$course->name;
                            }
                        } elseif ($basket->content_type == 5) {
                            $classroom = Classroom::find($basket->content_id);
                            if ($classroom) {
                                $comment = "КОМБО / ".$classroom->name;
                            }
                        }
                    }
                    $json['sum'] = $basket->sum;
                } else {
                    $result = 1; // Счёт не найден
                    $comment = 'Account not found';
                }

                $json['result'] = $result;
                $json['comment'] = $comment;
                $json['fields'] = [
                    'FIO' => 'Медетбеков Габит Алтаевич'
                ];
        
                return response()->json($json);

            } elseif ($request->command == "pay") {

                $request->validate([
                    'txn_id' => ['required'],
                    'txn_date' => ['required', 'string'],
                    'account' => ['required', 'string'],
                    'sum' => ['required', 'string'],
                ]);

                $basket = Basket::where([
                    'account' => $request->account,
                ])->first();

                $result = 5; // Другая ошибка провайдера
                $comment = 'Provider error';
                if ($basket && $basket->id && $basket->id > 0) {
                    if ($basket->paid == 1) {
                        $result = 3; // Заказ уже оплачен
                        $comment = 'The order has already been paid';
                    } else if ($basket->paid == 0) {

                        $basket->paid = 1;
                        //$basket->txn_date = $request->txn_date;

                        if ($basket->save()) {

                            if ($basket->content_type == 1) {
                                $this->payPerPage(
                                    $basket->user_id,
                                    $basket->content_id,
                                    $basket->sum,
                                    $basket->account
                                );

                                if (
                                    $basket->content_id == 3 ||
                                    $basket->content_id == 4 ||
                                    $basket->content_id == 5 ||
                                    $basket->content_id == 6 ||
                                    $basket->content_id == 7
                                ) {
                                    $count = VideoPayment::where([
                                        'user_id' => $basket->user_id,
                                        ['free', '>', 0]
                                    ])->count();
                                    if ($count == 0) {
                                        $this->payPerVideo(
                                            $basket->user_id,
                                            $basket->content_id,
                                            'free',
                                            $basket->account
                                        );
                                        $this->payPerLessosn(
                                            $basket->user_id, 
                                            $basket->content_id, 
                                            'free', 
                                            $basket->account
                                        );
                                    }
                                }
                            } elseif ($basket->content_type == 2) {
                                $this->payPerVideo(
                                    $basket->user_id,
                                    $basket->content_id,
                                    $basket->sum,
                                    $basket->account
                                );
                            } elseif ($basket->content_type == 3) {
                                $this->payPerLessosn(
                                    $basket->user_id, 
                                    $basket->content_id, 
                                    $basket->sum, 
                                    $basket->account
                                );
                            } elseif ($basket->content_type == 4) {
                                $this->payPerCourse(
                                    $basket->user_id, 
                                    $basket->content_id, 
                                    $basket->sum, 
                                    $basket->account
                                );
                            } elseif ($basket->content_type == 5) {
                                $this->payPerPage(
                                    $basket->user_id, 
                                    $basket->content_id, 
                                    $basket->sum, 
                                    $basket->account,
                                    true
                                );
                                $this->payPerVideo(
                                    $basket->user_id, 
                                    $basket->content_id, 
                                    $basket->sum, 
                                    $basket->account,
                                    true
                                );
                                $this->payPerLessosn(
                                    $basket->user_id, 
                                    $basket->content_id, 
                                    $basket->sum,
                                    $basket->account,
                                    true
                                );
                                
                                // $combo = new ComboPayment();
                                // $combo->user_id = $basket->user_id;
                                // $combo->classroom_id = $basket->content_id;
                                // $combo->vendor_code = $basket->account;
                                // $combo->status = 1;
                                // $combo->save();
                            }
                            
                            $result = 0; // Счёт найден и доступен для оплаты
                            $comment = 'The bill is paid';
                        }
                    }
                } else {
                    $result = 1; // Счёт не найден
                    $comment = 'Account not found';
                }

                return response()->json([
                    'txn_id' => $request->txn_id,
                    'prv_txn' => $basket->id,
                    'sum' => $request->sum,
                    'result' => $result,
                    'comment' => $comment,
                ]);
            } else {
                abort(404);
            }
        // } else {
        //     abort(404);
        // }
    }
}
