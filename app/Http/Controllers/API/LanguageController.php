<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CourseCategory;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    //
    public function classrooms(Request $request)
    {
        if ($request->content == 1) {
            $show['show_pages'] = 1;
        } else if ($request->content == 2) {
            $show['show_video'] = 1;
        } else if ($request->content == 3) {
            $show['show_lessons'] = 1;
        } else if ($request->content == 4) {
            $show['show_pages'] = 1;
            $show['show_video'] = 1;
            $show['show_lessons'] = 1;
        }

        $language = Language::with([
            'classrooms' => function($query) use ($show) {
                $query->where($show);
            }
        ])->where([
            ['id', $request->id],
            [$show]
        ])->select(['id', 'name'])->first();
        return response()->json($language);
    }

    //
    public function classroomsWithSubjectsAndBooks(Request $request)
    {
        if ($request->content == 1) {
            $show['show_pages'] = 1;
        } else if ($request->content == 2) {
            $show['show_video'] = 1;
        } else if ($request->content == 3) {
            $show['show_lessons'] = 1;
        }

        $language = Language::with([
            'classrooms' => function($query) use ($show) {
                $query->with([
                    'subjects' => function($query) use ($show) {
                        $query->with([
                            'books' => function($query) use ($show) {
                                $query->where($show);
                            }
                        ])->where($show);
                    }
                ])->where($show);
            }
        ])->where([
            ['id', $request->id],
            [$show]
        ])->select(['id', 'name'])->first();
        return response()->json([
            'language' => $language,
        ]);
    }

    //
    public function classroom(Request $request)
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
                    ['id', $request->classroom_id],
                    [$show],
                ])->first();
            }
        ])->where([
            ['id', $request->id],
            [$show]
        ])->select(['id', 'name'])->first();

        return response()->json($language);
    }
}
