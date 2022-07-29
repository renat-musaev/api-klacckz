<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Language;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    //
    public function getByIdAndLanguageId(Request $request)
    {
        if ($request->content == 1) {
            $show['show_pages'] = 1;
        } else if ($request->content == 2) {
            $show['show_video'] = 1;
        } else if ($request->content == 3) {
            $show['show_lessons'] = 1;
        }

        $language = Language::with([
            'classroom' => function($query) use ($request, $show) {
                $query->with([
                    'subjects' => function($query) use ($show) {
                        $query->with([
                            'books' => function($query) use ($show) {
                                $query->where($show);
                            }
                        ])->where($show);
                    }
                ])->where([
                    ['id', $request->id],
                    [$show],
                ])->first();
            }
        ])->where([
            ['id', $request->language_id],
            [$show]
        ])->select(['id', 'name'])->first();

        return response()->json([
            'language' => $language
        ]);
    }

    //
    public function getByIdWithVideo(Request $request)
    {
        $classrooms = Classroom::with([
            'subjects' => function($query) {
                $query->with('video');
            }
        ])->where('language_id', $request->id)->all();
        return response()->json([
            'classrooms' => $classrooms
        ]);
    }

    //
    public function getByIdWithLessons(Request $request)
    {
        $classrooms = Classroom::with([
            'subjects' => function($query) {
                $query->with('lessons');
            }
        ])->where('language_id', $request->id)->all();
        return response()->json([
            'classrooms' => $classrooms
        ]);
    }

    //
    public function getPayments(Request $request) {
        $payments = Payment::where([
            ['user_id', auth()->user()->id],
            ['classroom_id', $request->id],
            ['end_date', '>=', Carbon::now()->format('Y-m-d')],
            ['status', 1]
        ])->first();
    }
}
