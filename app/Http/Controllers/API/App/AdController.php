<?php

namespace App\Http\Controllers\API\App;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Http\Request;

class AdController extends Controller
{
    //
    public function get() {
        $ad = Ad::where('status', 1)->orderBy('position', 'desc')->get();
        return response()->json(['ad' => $ad]);
    }
}
