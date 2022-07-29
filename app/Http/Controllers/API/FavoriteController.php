<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FavoriteGDZ;
use App\Models\FavoriteLesson;
use App\Models\FavoriteVideo;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function gdz(Request $request)
    {
        $count = FavoriteGDZ::where([
            ['user_id', auth()->user()->id],
            ['book_id', $request->book_id]
        ])->limit(1)->count();

        if ($count == 0) {
            $fav = new FavoriteGDZ;
            $fav->user_id = auth()->user()->id;
            $fav->book_id = $request->book_id;
            $fav->save();
            return response()->json(true);
        }
        
        FavoriteGDZ::where([
            ['user_id', auth()->user()->id],
            ['book_id', $request->book_id]
        ])->delete();
        return response()->json(false);
    }

    public function lesson(Request $request)
    {
        $count = FavoriteLesson::where([
            ['user_id', auth()->user()->id],
            ['book_id', $request->book_id]
        ])->limit(1)->count();

        if ($count == 0) {
            $fav = new FavoriteLesson;
            $fav->user_id = auth()->user()->id;
            $fav->book_id = $request->book_id;
            $fav->save();
            return response()->json(true);
        }
        
        FavoriteLesson::where([
            ['user_id', auth()->user()->id],
            ['book_id', $request->book_id]
        ])->delete();
        return response()->json(false);
    }

    public function exercise(Request $request)
    {
        $count = FavoriteVideo::where([
            ['user_id', auth()->user()->id],
            ['book_id', $request->book_id]
        ])->limit(1)->count();

        if ($count == 0) {
            $fav = new FavoriteVideo;
            $fav->user_id = auth()->user()->id;
            $fav->book_id = $request->book_id;
            $fav->save();
            return response()->json(true);
        }
        
        FavoriteVideo::where([
            ['user_id', auth()->user()->id],
            ['book_id', $request->book_id]
        ])->delete();
        return response()->json(false);
    }

    public function favorites()
    {
        $gdzs = FavoriteGDZ::with([
            'book' => function($query) {
                $query->with([
                    'subject' => function($query) {
                        $query->with('classroom');
                    },
                    // 'favorite' => function($query) {
                    //     $query->where('user_id', auth()->user()->id);
                    // },
                ]);
            },
        ])->where('user_id', auth()->user()->id)->get();

        $lessons = FavoriteLesson::with([
            'book' => function($query) {
                $query->with([
                    'subject' => function($query) {
                        $query->with('classroom');
                    },
                    // 'favorite' => function($query) {
                    //     $query->where('user_id', auth()->user()->id);
                    // },
                ]);
            },
        ])->where('user_id', auth()->user()->id)->get();

        $video = FavoriteVideo::with([
            'book' => function($query) {
                $query->with([
                    'subject' => function($query) {
                        $query->with('classroom');
                    },
                    // 'favorite' => function($query) {
                    //     $query->where('user_id', auth()->user()->id);
                    // },
                ]);
            },
        ])->where('user_id', auth()->user()->id)->get();


        return response()->json([
            'gdzs' => $gdzs,
            'video' => $video,
            'lessons' => $lessons,
        ]);
    }
}
