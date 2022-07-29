<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PagePayment;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class PageController extends Controller
{
    // Вывод страницы
    public function show(Request $request)
    {
        $path = storage_path('app/public/lock_closed_icon.png');
        if ($request->has('key')) {
            $page = Page::where('key', $request->key)->first();
            if ($page && isset($page->key)) {
                $path = storage_path('app/public/' . $page->path);
            }
            
            // $key = Str::random(48);
            // Page::where([
            //     ['key', $request->key],
            //     ['updated_at', '<', Carbon::now()->format('Y-m-d')]
            // ])->update([
            //     'key' => $key,
            // ]);
        }
        
        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        
        return $response;
    }

    //
    public function getByKey(Request $request)
    {
        $page = Page::where('key', $request->key)->select('video_exercise')->first();
        return response()->json(['page' => $page]);
    }
}
