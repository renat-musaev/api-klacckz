<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CoursePayment;
use App\Models\LessonPayment;
use App\Models\PagePayment;
use App\Models\Payment;
use App\Models\VideoPayment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // История
    public function history(Request $request)
    {
        $gdz = PagePayment::with([
            'classroom' => function($query) {
                $query->with([
                    'language' => function($query) {
                        $query->select(['id', 'name']);
                    }
                ])->select(['id', 'language_id', 'name']);
            }
        ])->where([
            'user_id' => auth()->user()->id
        ])->orderBy('id', 'desc')->get();

        // 
        // $exercises = VideoPayment::with([
        //     'book' => function($query) {
        //         $query->with([
        //             'subject' => function($query) {
        //                 $query->with([
        //                     'classroom' => function($query) {
        //                         $query->with([
        //                             'language' => function($query) {
        //                                 $query->select(['id', 'name']);
        //                             }
        //                         ])->select(['id', 'language_id', 'name']);
        //                     }
        //                 ])->select(['id', 'classroom_id', 'name']);
        //             }
        //         ]);
        //     }
        // ])->where([
        //     'user_id' => auth()->user()->id
        // ])->orderBy('id', 'desc')->get();

        // 
        $exercises = VideoPayment::with([
            'classroom' => function($query) {
                $query->with([
                    'language' => function($query) {
                        $query->select(['id', 'name']);
                    }
                ])->select(['id', 'language_id', 'name']);
            }
        ])->where([
            'user_id' => auth()->user()->id
        ])->orderBy('id', 'desc')->get();

        // 
        $lessons = LessonPayment::with([
            'classroom' => function($query) {
                $query->with([
                    'language' => function($query) {
                        $query->select(['id', 'name']);
                    }
                ])->select(['id', 'language_id', 'name']);
            }
        ])->where([
            'user_id' => auth()->user()->id
        ])->orderBy('id', 'desc')->get();

        // 
        $courses = CoursePayment::with([
            'course' => function($query) {
                $query->with([
                    'category' => function($query) {
                        $query->select(['id', 'name']);
                    }
                ])->select(['id', 'category_id', 'name']);
            }
        ])->where([
            'user_id' => auth()->user()->id
        ])->orderBy('id', 'desc')->get();
        
        return response()->json([
            'payment' => [
                'gdz' => $gdz,
                'exercises' => $exercises,
                'lessons' => $lessons,
                'courses' => $courses,
            ]
        ]);
    }
}
