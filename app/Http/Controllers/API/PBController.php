<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PBController extends Controller
{
    // URL для проверки возможности платежа.
    public function check(Request $request)
    {

    }

    // URL для сообщения о результате платежа.
    public function result(Request $request)
    {
        if (
            $request->has('KEY') && $request->KEY == "f6qVeMieqiiSFjRlSrlgIQDuBzm2SNyr" &&
            $request->has('UID') &&
            $request->has('CID') &&
            $request->has('pg_amount') &&
            $request->has('pg_result') && $request->pg_result == 1 &&
            $request->has('CODE')
        ) {
            $user_id = $request->UID;
            $classroom_id = $request->CID;
            $amount = $request->pg_amount;
            $code = $request->CODE;

            // Пытаемся получить дыннае о действующей подписки
            $subscription = Payment::where([
                ['user_id', $user_id],
                ['classroom_id', $classroom_id],
                ['end_date', '>', date('Y-m-d')]
            ])->orderBy('id', 'desc')->first();

            // Если подписка еще не закончена
            if ($subscription && isset($subscription->end_date)) {
                $carbon = new Carbon($subscription->end_date);
                if ($amount == 2000) {
                    $date = $carbon->addMonth();
                } elseif ($amount == 4000) {
                    $date = $carbon->addMonths(3);
                } elseif ($amount == 6000) {
                    $date = $carbon->addMonths(6);
                } else {
                    return false;
                }
            } else if ($amount == 2000) {
                $date = Carbon::now()->addMonth();
            } elseif ($amount == 4000) {
                $date = Carbon::now()->addMonths(3);
            } elseif ($amount == 6000) {
                $date = Carbon::now()->addMonths(6);
            } else {
                return false;
            }

            $pay = new Payment();
            $pay->user_id = $user_id;
            $pay->classroom_id = $classroom_id;
            $pay->amount = $amount;
            $pay->end_date = $date;
            $pay->vendor_code = $code;
            $pay->status = 1;
            $pay->save();
        } else {
			return false;
		}
    }

    // URL для сообщения об отмене платежа.
    public function refund(Request $request)
    {

    }

    // URL для сообщения о проведении клиринга платежа по банковской карте.
    public function capture(Request $request)
    {

    }
}
