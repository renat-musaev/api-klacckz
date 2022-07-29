<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FAQ;

class FAQController extends Controller
{
    //
    public function get() {
        $data = FAQ::all();
        return response()->json($data);
    }
}
