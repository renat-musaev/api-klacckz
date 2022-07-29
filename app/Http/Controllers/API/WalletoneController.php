<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Basket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletoneController extends Controller
{
    // Формирование платежной формы
    public function fpf(Request $request)
    {
        if (auth()->guard('api')->user() && $request->has('cid') && $request->has('amount') && $request->has('type')) {
            $user   = auth()->guard('api')->user();
            $amount = $request->amount;
            $cid    = $request->cid;
            $code   = rand(10000, 99999) . $user->id;
            $type   = $request->type;
            
            // Добавление полей формы в ассоциативный массив
            $fields["WMI_MERCHANT_ID"]      = "192944545189";
            $fields["WMI_PAYMENT_AMOUNT"]   = $amount;
            $fields["WMI_CURRENCY_ID"]      = "398";
            $fields["WMI_AUTO_LOCATION"]    = 1;
            $fields["WMI_PAYMENT_NO"]       = $code;
            $fields["WMI_DESCRIPTION"]      = "BASE64:".base64_encode(''.$code);
            $fields["WMI_EXPIRED_DATE"]     = date('Y-m-d', strtotime('+10 day'))."T23:59:59";
            //$fields["WMI_SUCCESS_URL"]      = "https://klacc.kz/payment/success";
            //$fields["WMI_FAIL_URL"]         = "https://klacc.kz/";
            $fields["UID"]                  = (int) $user->id;
            $fields["CID"]                  = (int) $cid;
            $fields["TID"]                  = (int) $type;

            //Сортировка значений внутри полей
            foreach($fields as $name => $val) {
                if(is_array($val)) {
                    usort($val, "strcasecmp");
                    $fields[$name] = $val;
                }
            }

            uksort($fields, "strcasecmp");
            $fieldValues = "";

            foreach($fields as $value) {
                if(is_array($value)) {
                    foreach($value as $v) {
                        $v = iconv("utf-8", "windows-1251", $v);
                        $fieldValues .= $v;
                    }
                } else {
                    $value = iconv("utf-8", "windows-1251", $value);
                    $fieldValues .= $value;
                }
            }
            $signature = base64_encode(pack("H*", md5($fieldValues . $this->walletone_key)));
            $fields["WMI_SIGNATURE"] = $signature;

            $fields["kaspi"] = $this->addToBasket($request);
            return response()->json($fields);
        }
        return response()->json('Error');
    }

    //
    public function addToBasket(Request $request)
    {
        $user = Auth::user();
        $basket = new Basket();
        $account = rand(100000, 999999).$user->id;
        $txn_id = rand(10000, 99999).$user->id;

        $basket->account = $account;
        $basket->txn_id = $txn_id;
        $basket->txn_date = date('YmdHis');
        $basket->user_id = $user->id;
        $basket->content_id = $request->cid;
        $basket->content_type = $request->type;
        $basket->sum = $request->amount . '.00';
        $basket->paid = 0;

        $fields["TranId"] = $txn_id;
        $fields["OrderId"] = $account;
        $fields["Amount"] = $request->amount . '00';
        $fields["Service"] = "Shin";
        $fields["returnUrl"] = "https://klacc.kz/kaspi/success/";
        $fieldValues = "";

        foreach($fields as $value) {
            if(is_array($value)) {
                foreach($value as $v) {
                    $v = iconv("utf-8", "windows-1251", $v);
                    $fieldValues .= $v;
                }
            } else {
                $value = iconv("utf-8", "windows-1251", $value);
                $fieldValues .= $value;
            }
        }
        $signature = base64_encode($fieldValues);

        $fields["Signature"] = $signature;
        
        if ($basket->save()) {
            return $fields;
        }
    }
}
