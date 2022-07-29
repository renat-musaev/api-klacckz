<?php

namespace App\Http\Controllers\API\App;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CoursePayment;
use App\Models\CourseVideo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    // courses
    public function courses()
    {
        $courses = CourseCategory::with([
            'courses',
            'language',
        ])->get()->toTree();

        return response()->json([
            'courses' => $courses,
        ]);
    }

    //
    public function getByIDCategory(Request $request)
    {
        $courses = CourseCategory::where('id', $request->id)->with([
            'courses' => function($query) {
                $query->with([
                    'payment' => function($query) {
                        if (auth()->guard('api')->user()) {
                            $query->where([
                                ['user_id', auth()->guard('api')->user()->id],
                                ['status', 1],
                            ])->orderBy('id', 'desc')->first();
                        }
                    }
                ])->where('show', 1)->select(['id', 'category_id', 'name', 'text', 'cover', 'price_1', 'price_2', 'price_3', 'price_4', 'info_payment_1']);
            },
            'language',
        ])->first();

        return response()->json([
            'courses' => $courses,
        ]);
    }

    // course
    public function getByID(Request $request)
    {
        if (auth()->guard('api')->user()) {
            CoursePayment::where([
                'user_id' => auth()->guard('api')->user()->id,
                'status' => 1,
                ['end_date', '<', Carbon::now()->format('Y-m-d')]
            ])->update(['status' => 0]);
        }

        $with['videos'] = function($query) {
            $query->with('tests');
        };
        $with['category'] = function($query) {
            $query->with('language');
        };
        if (auth()->guard('api')->user()) {
            $with['payment'] = function($query) {
                $query->where([
                    ['user_id', auth()->guard('api')->user()->id],
                    ['status', 1],
                ])->orderBy('id', 'desc')->first();
            };
        }

        $course = Course::with($with)->where([
            'id' => $request->id,
            'show' => 1
        ])->first();
        
        return response()->json([
            'course' => $course,
        ]);
    }

    //
    public function videoById(Request $request)
    {
        if ($request->has('id')) {
            $video = CourseVideo::with([
                'tests' => function($query) {
                    $query->with('answers');
                }
            ])->where('id', $request->id)->first();

            if ($video && !empty($video->tests)) {
                foreach ($video->tests as $key => $test) {
                    $video->tests[$key]['choice'] = 0;
                }
            }
            
            $key = Str::random(48);
            CourseVideo::where([
                ['id', $request->id],
                ['updated_at', '<', Carbon::now()->format('Y-m-d')]
            ])->update([
                'key' => $key,
            ]);
        }
        
        return response()->json([
            'video' => $video
        ]);
    }
}
