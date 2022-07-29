<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends Controller
{
    public function get(Request $request)
    {
        return QrCode::encoding('UTF-8')->size(320)->generate($request->token);
    }
}
