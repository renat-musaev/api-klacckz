<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\NotificationAndroid;
use Illuminate\Http\Request;

class NotificationAndroidController extends Controller
{
    //
    public function getRelevant() {
        $notification = NotificationAndroid::where('status', 1)->get();
        return response()->json([
            'notification' => $notification
        ]);
    }
}
