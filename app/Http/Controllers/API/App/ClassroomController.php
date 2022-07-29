<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    //
    public function byLangId(Request $request)
    {
        if ($request->content == 1) {
            $show['show_pages'] = 1;
        } else if ($request->content == 2) {
            $show['show_video'] = 1;
        } else if ($request->content == 3) {
            $show['show_lessons'] = 1;
        }

        $classrooms = Classroom::where([
            ['language_id', $request->lang_id],
            [$show]
        ])->get();
        return response()->json([
            'classrooms' => $classrooms
        ]);
    }

    //
    public function byId(Request $request)
    {
        if ($request->content == 1) {
            $show['show_pages'] = 1;
        } else if ($request->content == 2) {
            $show['show_video'] = 1;
        } else if ($request->content == 3) {
            $show['show_lessons'] = 1;
        }

        $classroom = Classroom::with([
            'subjects' => function($query) use ($show) {
                $query->with([
                    'books' => function($query) use ($show) {
                        $query->where($show);
                    }
                ])->where($show);
            }
        ])->where([
            ['id', $request->id],
            [$show]
        ])->first();

        return response()->json([
            'classroom' => $classroom
        ]);
    }
}
