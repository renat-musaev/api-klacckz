<?php

namespace App\Http\Controllers\API\App;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\PasswordReset;
use App\Models\User;
use App\Models\UserQRCode;
use App\Models\UserVerification;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function get()
    {
        $user = Auth::user();
        $notification = Notification::where('id', '>', $user->notification_id)->get();
        $user->notification = $notification;
        return response()->json($user);
    }

    public function notificationIncrement(Request $request)
    {
        $id = Auth::user()->id;
        $user = User::find($id);
        $user->notification_id = $request->notification_id;
        $response = $user->save();
        return response()->json($response);
    }

    //
    public function signIn(Request $request)
    {
        $token = UserQRCode::where('code', $request->code)->first();
        if ($token && isset($token->token)) {
            UserQRCode::where('code', $request->code)->delete();
            return response()->json([
                'token' => $token->token,
                'code' => $request->code
            ]);
        }
        
        return response()->json(['message' => 'Invalid QR code'], 422);
    }

    // 
    public function signInIOS(Request $request)
    {
        $token = UserQRCode::where('code', $request->code)->first();
        if ($token && isset($token->token)) {
            UserQRCode::where('code', $request->code)->delete();
            return response()->json([
                'data' => [
                    'token' => $token->token,
                    'code' => $request->code
                ]
            ]);
        }
        
        return response()->json(['errorMessage' => 'Invalid QR code'], 422);
    }



    // Авторизация пользователя
    // public function signin(Request $request)
    // {
    //     try {
    //         $this->validate($request, [
    //             'phone' => ['required', 'numeric', 'digits:10'],
    //             'password' => ['required', 'string'],
    //         ]);
    //     } catch (BadResponseException $e) {
    //         return response()->json(['message' => $e->getMessage()], $e->getCode());
    //     }
        
    //     $user = User::where('phone', $request->phone)->first();
        
    //     if ($user && isset($user->id) && Hash::check($request->password, $user->password)) {
            
    //         //$user->device_count == 0;

    //         if ($user->device_count >= $user->device_limit) {
    //             return response()->json(['message' => 'Number of authorized devices exceeded'], 422);
    //         }

    //         // ...
    //         if ($user->device_count == 0) {
    //             DB::table('oauth_access_tokens')->where([
    //                 ['user_id', $user->id],
    //                 ['client_id', 1]
    //             ])->delete();
    //         }

    //         // NEW QR CODE
    //         $qr_code = rand(10000,99999);
    //         $qr_code = Hash::make($request->phone.$qr_code.$request->password);
            
    //         $user->device_count = $user->device_count + 1;
    //         $user->qrcode = $qr_code;
    //         if ($user->save()) {


    //             $data = [
    //                 'phone' => $request->phone,
    //                 'password' => $request->password
    //             ];
        
    //             if (auth()->attempt($data)) {
    //                 $token = auth()->user()->createToken('KlaccKZAuthApp')->accessToken;
    //                 return response()->json(['access_token' => $token]);
    //             } else {
    //                 return response()->json(['message' => 'Unauthorised'], 401);
    //             }


    //             return $this->auth($request);
    //         }
    //     }
        
    //     return response()->json(['message' => 'Invalid login or password'], 422);
    // }

    // // Авторизация пользователя
    // private function auth(Request $request)
    // {
    //     $data = [
    //         'phone' => $request->phone,
    //         'password' => $request->password
    //     ];
 
    //     if (auth()->attempt($data)) {
    //         $token = auth()->user()->createToken('KlaccKZAuthApp')->accessToken;
    //         return response()->json(['access_token' => $token]);
    //     } else {
    //         return response()->json(['message' => 'Unauthorised'], 401);
    //     }
    // }

    // Выход
    // public function logout()
    // {
    //     auth()->user()->decrement('device_count');
        
    //     $token = auth()->user()->token();
    //     if ($token->delete()) {
    //         return response()->json(true);
    //     } else {
    //         return response()->json(['message' => 'System error'], 422);
    //     }
    // }
}
