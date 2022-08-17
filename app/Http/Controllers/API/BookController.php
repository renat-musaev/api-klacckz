<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\LessonPayment;
use App\Models\PagePayment;
use App\Models\Payment;
use App\Models\VideoPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // Получить книгу по ID
    public function pages(Request $request)
    {
        if (auth()->guard('api')->user()) {
            PagePayment::where([
                'user_id' => auth()->guard('api')->user()->id,
                'status' => 1,
                ['end_date', '<', Carbon::now()->format('Y-m-d')]
            ])->update(['status' => 0]);
        }

        //
        if (auth()->guard('api')->user()) {
            VideoPayment::where([
                'user_id' => auth()->guard('api')->user()->id,
                'status' => 1,
                ['end_date', '<', Carbon::now()->format('Y-m-d')]
            ])->update(['status' => 0]);
        }

        $with['pages'] = function($query) {
            $query->select(['id', 'book_id', 'number', 'free', 'key', 'video_exercise']);
        };

        $with['videos'] = function($query) {
            $query->with(['exercises' => function($query) {
                $query->select(['id', 'video_id', 'name', 'free', 'link', 'key']);
            }])->select(['id', 'book_id', 'name', 'free']);
        };

        $with['subject'] = function($query) {
            $query->with([
                'classroom' => function($query) {
                    $with['language'] = function($query) {
                        $query->select('id', 'name');
                    };
                    if (auth()->guard('api')->user()) {
                        $with['pagePayment'] = function($query) {
                            $query->where([
                                ['user_id', auth()->guard('api')->user()->id],
                                ['status', 1],
                            ])->orderBy('id', 'desc')->first();
                        };
                        $with['videoPayment'] = function($query) {
                            $query->where([
                                ['user_id', auth()->guard('api')->user()->id],
                                ['status', 1],
                            ])->orderBy('id', 'desc')->first();
                        };
                    }

                    $query->with($with)->select(['id', 'language_id', 'name', 'info_payment_1_page', 'info_payment_2_page', 'info_payment_combo', 'show_pages', 'show_video', 'show_lessons']);
                }
            ])->select(['id', 'classroom_id', 'name', 'show_video', 'show_lessons']);
        };
        
        if (auth()->guard('api')->user()) {
            $with['favoriteGDZ'] = function($query) {
                $user = auth()->guard('api')->user();
                $query->where('user_id', $user->id)->select('id', 'user_id', 'book_id');
            };

            $with['favoriteVideo'] = function($query) {
                $user = auth()->guard('api')->user();
                $query->where('user_id', $user->id)->select('id', 'user_id', 'book_id');
            };
        }

        $book = Book::with($with)->where([
            ['id', $request->id],
            ['show_pages', 1]
        ])->select([
            'id', 'subject_id', 'name', 'cover', 'show_video', 'show_lessons',
            'info_payment_1_page', 'info_payment_2_page',
            'info_payment_1_exercise', 'info_payment_2_exercise',
        ])->first();

        // Подписка закончилась
        if (auth()->guard('api')->user() && isset($book->subject->classroom_id)) {
            $payment = PagePayment::where([
                ['user_id', auth()->guard('api')->user()->id],
                ['classroom_id', $book->subject->classroom_id]
            ])->orderBy('id', 'desc')->first();

            if (isset($payment->status) && $payment->status == 0) {
                $book->subscription_ended = $payment->end_date;
                //$book->subscription_ended = "Подписка на данный класс истекла ".Carbon::parse($payment->end_date)->format("Y-m-d");
            }
        }

        return response()->json([
            'book' => $book,
        ]);
    }

    // Получить книгу по ID
    public function video(Request $request)
    {
        if (auth()->guard('api')->user()) {
            VideoPayment::where([
                'user_id' => auth()->guard('api')->user()->id,
                'status' => 1,
                ['end_date', '<', Carbon::now()->format('Y-m-d')]
            ])->update(['status' => 0]);
        }

        $with['videos'] = function($query) {
            $query->with(['exercises' => function($query) {
                $query->select(['id', 'video_id', 'name', 'free', 'link', 'key']);
            }])->select(['id', 'book_id', 'name', 'link', 'free']);
        };

        $with['subject'] = function($query) {
            $query->with([
                'classroom' => function($query) {
                    $with['language'] = function($query) {
                        $query->select('id', 'name');
                    };
                    if (auth()->guard('api')->user()) {
                        $with['videoPayment'] = function($query) {
                            $query->where([
                                ['user_id', auth()->guard('api')->user()->id],
                                ['status', 1],
                            ])->orderBy('id', 'desc')->first();
                        };
                    }

                    $query->with($with)->select(['id', 'language_id', 'name', 'info_payment_1_exercise', 'info_payment_2_exercise', 'info_payment_combo']);
                }
            ])->select(['id', 'classroom_id', 'name', 'show_video']);
        };
        
        if (auth()->guard('api')->user()) {
            $with['favoriteVideo'] = function($query) {
                $user = auth()->guard('api')->user();
                $query->where('user_id', $user->id)->select('id', 'user_id', 'book_id');
            };
        }

        $book = Book::with($with)->where([
            ['id', $request->id],
            ['show_video', 1]
        ])->select([
            'id', 'subject_id', 'name', 'cover', 'show_pages', 'show_lessons',
            'info_payment_1_exercise', 'info_payment_2_exercise',
        ])->first();

        // Подписка закончилась
        if (auth()->guard('api')->user() && isset($book->subject->classroom_id)) {
            $payment = VideoPayment::where([
                ['user_id', auth()->guard('api')->user()->id],
                ['classroom_id', $book->subject->classroom_id]
            ])->orderBy('id', 'desc')->first();

            if (isset($payment->status) && $payment->status == 0) {
                $book->subscription_ended = $payment->end_date;
            }
        }

        return response()->json([
            'book' => $book,
        ]);
    }

    // Получить книгу по ID
    public function lessons(Request $request)
    {
        if (auth()->guard('api')->user()) {
            LessonPayment::where([
                'user_id' => auth()->guard('api')->user()->id,
                'status' => 1,
                ['end_date', '<', Carbon::now()->format('Y-m-d')]
            ])->update(['status' => 0]);
        }

        $with['lessons'] = function($query) {
            $query->select(['id', 'book_id', 'name', 'free', 'key']);
        };

        $with['subject'] = function($query) {
            $query->with([
                'classroom' => function($query) {
                    $with['language'] = function($query) {
                        $query->select('id', 'name');
                    };
                    if (auth()->guard('api')->user()) {
                        $with['lessonPayment'] = function($query) {
                            $query->where([
                                ['user_id', auth()->guard('api')->user()->id],
                                ['status', 1],
                            ])->orderBy('id', 'desc')->first();
                        };
                    }

                    $query->with($with)->select(['id', 'language_id', 'name', 'info_payment_1_lessons', 'info_payment_2_lessons', 'info_payment_combo']);
                }
            ])->select(['id', 'classroom_id', 'name', 'show_video', 'show_lessons']);
        };
        
        if (auth()->guard('api')->user()) {
            $with['favoriteLesson'] = function($query) {
                $user = auth()->guard('api')->user();
                $query->where('user_id', $user->id)->select('id', 'user_id', 'book_id');
            };
        }

        $book = Book::with($with)->where([
            ['id', $request->id],
            ['show_lessons', 1]
        ])->select([
            'id', 'subject_id', 'name', 'cover', 'show_pages', 'show_video',
            'info_payment_1_lesson', 'info_payment_2_lesson',
        ])->first();

        // Подписка закончилась
        if (auth()->guard('api')->user() && isset($book->subject->classroom_id)) {
            $payment = LessonPayment::where([
                ['user_id', auth()->guard('api')->user()->id],
                ['classroom_id', $book->subject->classroom_id]
            ])->orderBy('id', 'desc')->first();

            if (isset($payment->status) && $payment->status == 0) {
                $book->subscription_ended = $payment->end_date;
            }
        }

        return response()->json([
            'book' => $book,
        ]);
    }
}
