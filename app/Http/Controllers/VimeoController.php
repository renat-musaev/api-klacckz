<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VimeoController extends Controller
{
    //
    public function vimeo(Request $request) {

        return view('vimeo', [
            'id' => $request->id
        ]);
    }
}
