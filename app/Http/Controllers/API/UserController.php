<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\Referral;
use App\Models\User;
use App\Models\UserQRCode;
use App\Models\UserVerification;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UserController extends Controller
{
    // Авторизация пользователя
    public function signin(Request $request)
    {
        sleep(1);
        try {
            $this->validate($request, [
                'phone' => ['required', 'numeric', 'digits:10'],
                'password' => ['required', 'string'],
            ]);
        } catch (BadResponseException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
        
        $user = User::where('phone', $request->phone)->first();
        
        if ($user && isset($user->id) && Hash::check($request->password, $user->password)) {
            DB::table('oauth_access_tokens')->where([
                ['user_id', $user->id],
                //['client_id', 1]
            ])->delete();
            
            //$user->device_count = $user->device_count + 1;
            if ($user->save()) {
                return $this->auth($request);
            }
        }
        
        return response()->json(['message' => 'Invalid login or password'], 422);
    }

    // Регистрация пользователя
    public function signup(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => ['required', 'string'],
                'phone' => ['required', 'numeric', 'digits:10', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
                'verify' => ['required', 'numeric', 'digits:5'],
            ]);
        } catch (BadResponseException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
        
        if ($this->verification($request->phone, $request->verify)) {
            $promo_code = mb_strtoupper(Str::random(10));
            $password = $request->password;
            
            $user = new User();
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->password = Hash::make($password);
            $user->promo_code = $promo_code;
            $user->device_count = 1;
            
            if ($user->save()) {
                if ($request->has('promo_code') && !empty($request->promo_code)) {
                    $userInvited = User::where('promo_code', $request->promo_code)->first();
                    if ($userInvited && $userInvited->id) {
                        $ref = new Referral;
                        $ref->user_id = $userInvited->id;
                        $ref->guest = $user->id;
                        $ref->status = 0;
                        $ref->save();
                    }
                }
                return $this->auth($request);
            }
            return response()->json(['message' => 'Error Database'], 422);
        }
        return response()->json(['message' => 'Invalid verification code'], 422);
    }

    // Проверка кода регистрации
    private function verification($phone, $code)
    {
        $verify = UserVerification::where('phone', $phone)->first();
        
        if ($verify && isset($verify->id) && Hash::check($code, $verify->code)) {
            UserVerification::where('phone', $phone)->delete();
            return true;
        }

        return false;
    }

    // Добавляем код для верификации
    public function verify(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => ['required', 'string'],
                'phone' => ['required', 'numeric', 'digits:10', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ]);
        } catch (BadResponseException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
        
        $count = UserVerification::where([
            ['phone', $request->phone],
        ])->count();
        
        if ($count && $count > 0) {
            UserVerification::where('phone', $request->phone)->delete();
        }

        $code = rand(10000,99999);
        
        $verify = new UserVerification();
        $verify->phone = $request->phone;
        $verify->code = Hash::make($code);
        
        if ($verify->save()) {
            $this->sendSMS($request->phone, "Подтверждение на сайте klacc.kz - ".$code);
            return response()->json(true);
        }

        return response()->json(['message' => 'System error'], 422);
    }

    // Добавляем код для сброса пароля
    public function passwordResetVerify(Request $request)
    {
        try {
            $this->validate($request, [
                'phone' => ['required', 'numeric', 'digits:10'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ]);
        } catch (BadResponseException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }

        $user = User::where([
            ['phone', $request->phone],
        ])->count();

        if ($user && $user > 0) {
            $count = PasswordReset::where([
                ['phone', $request->phone],
            ])->count();

            if ($count && $count > 0) {
                PasswordReset::where('phone', $request->phone)->delete();
            }

            $code = rand(10000,99999);
            
            $verify = new PasswordReset();
            $verify->phone = $request->phone;
            $verify->code = Hash::make($code);
            
            if ($verify->save()) {
                $this->sendSMS($request->phone, "Подтверждение на сайте klacc.kz - ".$code);
                return response()->json(true);
            }
    
            return response()->json(['message' => 'System error'], 500);
        }
        return response()->json(['message' => 'This number is not registered'], 500);
    }

    // Проверка кода для сброса пароля
    private function passwordResetVerification($phone, $code)
    {
        $verify = PasswordReset::where('phone', $phone)->first();
        
        if ($verify && isset($verify->id) && Hash::check($code, $verify->code)) {
            PasswordReset::where('phone', $phone)->delete();
            return true;
        }

        return false;
    }

    // Сброс пароля
    public function passwordReset(Request $request)
    {
        try {
            $this->validate($request, [
                'phone' => ['required', 'numeric', 'digits:10'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
                'verify' => ['required', 'numeric', 'digits:5'],
            ]);
        } catch (BadResponseException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
        
        if ($this->passwordResetVerification($request->phone, $request->verify)) {
            $user = User::where('phone', $request->phone)->first();
            $user->password = Hash::make($request->password);
            $user->device_count = 1;

            DB::table('oauth_access_tokens')->where('user_id', $user->id)->delete();
            
            if ($user->save()) {
                return $this->auth($request);
            }
            return response()->json(['message' => 'System error'], 500);
        }
        return response()->json(['message' => 'Invalid verification code'], 422);
    }

    // Авторизация пользователя
    private function auth(Request $request)
    {
        $data = [
            'phone' => $request->phone,
            'password' => $request->password
        ];
 
        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('KlaccKZAuthApp')->accessToken;
            return response()->json(['access_token' => $token]);
        } else {
            return response()->json(['message' => 'Unauthorised'], 401);
        }
    }

    //
    public function qrCodeGeneration(Request $request)
    {
        sleep(3);
        $user = Auth::user();
        UserQRCode::where('user_id', $user->id)->delete();

        $qr_code = Str::random(16);

        $token = $request->header('Authorization');
        $token = explode(' ', $token);
        $token = $token[1];
        if (!empty($token) && $token[1] != null) {
            $code = new UserQRCode();
            $code->user_id = $user->id;
            $code->code = $qr_code;
            $code->token = $token;
            if ($code->save()) {
                $userToken = User::find($user->id);
                $userToken->code_android = $qr_code;
                $userToken->save();
            }
        }

        return response()->json([
            'code' => $qr_code,
        ]);
    }

    //
    public function getQRCode()
    {
        $user = Auth::user();
        $qr_code = UserQRCode::where([
            'user_id' => $user->id,
        ])->first();

        return QrCode::encoding('UTF-8')->size(240)->generate($qr_code->code);
    }

    // Выход
    public function logout()
    {
        auth()->user()->decrement('device_count');
        
        $token = auth()->user()->token();
        if ($token->delete()) {
            return response()->json(true);
        } else {
            return response()->json(['message' => 'System error'], 422);
        }
    }

    //
    public function referrals()
    {
        $user = Auth::user();

        if ($user) {

            $date1 = Carbon::now()->subDay();
            $date2 = Carbon::now()->subDays(7);
            $date3 = Carbon::now()->subMonth();

            $day = Referral::where([
                'user_id' => $user->id,
                ['created_at', '>=', $date1],
            ])->get();

            $week = Referral::where([
                'user_id' => $user->id,
                ['created_at', '>=', $date2],
            ])->get();

            $month = Referral::where([
                'user_id' => $user->id,
                ['created_at', '>=', $date3],
            ])->get();

            return response()->json([
                'day' => $day,
                'week' => $week,
                'month' => $month,
            ]);
        }
    }
}
