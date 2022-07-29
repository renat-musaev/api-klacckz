<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LessonController extends Controller
{
    // ...
    public function show(Request $request)
    {   
        if ($request->has('key')) {
            $lesson = Lesson::with([
                'tests' => function($query) {
                    $query->with('answers');
                }
            ])->where('key', $request->key)->first();

            if ($lesson && !empty($lesson->tests)) {
                foreach ($lesson->tests as $key => $test) {
                    $lesson->tests[$key]['choice'] = 0;
                }
            }
            
            $key = Str::random(48);
            Lesson::where([
                ['key', $request->key],
                ['updated_at', '<', Carbon::now()->format('Y-m-d')]
            ])->update([
                'key' => $key,
            ]);
        }
        
        return response()->json([
            'lesson' => $lesson
        ]);
    }
}
