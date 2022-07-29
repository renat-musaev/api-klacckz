<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Basket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BasketController extends Controller
{
    //
    public function create(Request $request)
    {
        $request->validate([
            'user_id' => ['required'],
            'content_id' => ['required'],
            'content_type' => ['required', 'string', 'min:1', 'max:10'],
            'sum' => ['required', 'string'],
        ]);

        $user = Auth::user();

        $basket = new Basket();
        $basket->user_id = $user->id;
        $basket->account = $user->phone;
        $basket->content_id = $request->content_id;
        $basket->content_type = $request->content_type;
        $basket->sum = $request->sum;
        $basket->paid = 0;
        if ($basket->save()) {
            return response()->json([
                'basket' => $basket,
            ]);
        }
    }
}
