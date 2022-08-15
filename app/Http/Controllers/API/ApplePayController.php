<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApplePayController extends Controller
{
    //
    public function payment(Request $request)
    {
        $request->validate([
            'content' => ['required'],
            'uid' => ['required'],
            'cid' => ['required'],
            'amount' => ['required'],
        ]);

        $code = 5000;

        //$request->amount = $request->amount - ($request->amount/(100*30));

        if ($request->content == 1) {
            $this->payPerPage($request->uid, $request->cid, $request->amount, $code);

            // if (
            //     $_POST['CID'] == 3 ||
            //     $_POST['CID'] == 4 ||
            //     $_POST['CID'] == 5 ||
            //     $_POST['CID'] == 6 ||
            //     $_POST['CID'] == 7
            // ) {
            //     $count = VideoPayment::where([
            //         'user_id' => $_POST['UID'],
            //         //'classroom_id' => $_POST['CID'],
            //         ['free', '>', 0]
            //     ])->count();
            //     if ($count == 0) {
            //         $this->payPerVideo($_POST['UID'], $_POST['CID'], 'free', $_POST['WMI_PAYMENT_NO']);
            //         $this->payPerLessosn($_POST['UID'], $_POST['CID'], 'free', $_POST['WMI_PAYMENT_NO']);
            //     }
            // }
        } elseif ($request->content == 2) {
            $this->payPerVideo($_POST['UID'], $_POST['CID'], $_POST['WMI_PAYMENT_AMOUNT'], $_POST['WMI_PAYMENT_NO']);
        } elseif ($request->content == 3) {
            $this->payPerLessosn($_POST['UID'], $_POST['CID'], $_POST['WMI_PAYMENT_AMOUNT'], $_POST['WMI_PAYMENT_NO']);
        } elseif ($request->content == 4) {
            $this->payPerCourse($_POST['UID'], $_POST['CID'], $_POST['WMI_PAYMENT_AMOUNT'], $_POST['WMI_PAYMENT_NO']);
        } elseif ($request->content == 5) {
            $this->payPerPage($_POST['UID'], $_POST['CID'], $_POST['WMI_PAYMENT_AMOUNT'], $_POST['WMI_PAYMENT_NO'], true);
            $this->payPerVideo($_POST['UID'], $_POST['CID'], $_POST['WMI_PAYMENT_AMOUNT'], $_POST['WMI_PAYMENT_NO'], true);
            $this->payPerLessosn($_POST['UID'], $_POST['CID'], $_POST['WMI_PAYMENT_AMOUNT'], $_POST['WMI_PAYMENT_NO'], true);

            // $combo = new ComboPayment();
            // $combo->user_id = $_POST['UID'];
            // $combo->classroom_id = $_POST['CID'];
            // $combo->vendor_code = $_POST['WMI_PAYMENT_NO'];
            // $combo->status = 1;
            // $combo->save();
        } else {
            return false;
        }

        return true;

        // $ref = Referral::where([
        //     'guest' => $_POST['UID'],
        //     'status' => 0,
        // ])->first();
        // if ($ref && $ref->guest && $ref->status == 0) {
        //     $ref->status = 1;
        //     $ref->save();
        // }
    }
}
