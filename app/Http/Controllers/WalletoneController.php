<?php

namespace App\Http\Controllers;

use App\Models\ComboPayment;
use App\Models\Referral;
use App\Models\VideoPayment;
use Illuminate\Http\Request;

class WalletoneController extends Controller
{
    // Функция, которая возвращает результат в Единую кассу
    private function printAnswer($result, $description) {
        print "WMI_RESULT=" . strtoupper($result) . "&";
        print "WMI_DESCRIPTION=" .$description;
        exit();
    }

    //
    public function payment(Request $request)
    {
        // Проверка наличия необходимых параметров в POST-запросе
        if (!isset($_POST['WMI_SIGNATURE']))
            $this->printAnswer("Retry", "Отсутствует параметр WMI_SIGNATURE");

        if (!isset($_POST['WMI_PAYMENT_NO']))
            $this->printAnswer("Retry", "Отсутствует параметр WMI_PAYMENT_NO");

        if (!isset($_POST['WMI_ORDER_STATE']))
            $this->printAnswer("Retry", "Отсутствует параметр WMI_ORDER_STATE");

        // Извлечение всех параметров POST-запроса, кроме WMI_SIGNATURE
        foreach($_POST as $name => $value) {
            if ($name !== "WMI_SIGNATURE") $params[$name] = $value;
        }

        // Сортировка массива по именам ключей в порядке возрастания
        // и формирование сообщения, путем объединения значений формы
        uksort($params, "strcasecmp"); $values = "";

        foreach($params as $name => $value) {
            $values .= $value;
        }

        // Формирование подписи для сравнения ее с параметром WMI_SIGNATURE
        $signature = base64_encode(pack("H*", md5($values . $this->walletone_key)));

        //Сравнение полученной подписи с подписью W1
        if ($signature == $_POST['WMI_SIGNATURE']) {
            if (strtoupper($_POST['WMI_ORDER_STATE']) == "ACCEPTED") {

                if ($_POST['TID'] == 1) {
                    $this->payPerPage($_POST['UID'], $_POST['CID'], $_POST['WMI_PAYMENT_AMOUNT'], $_POST['WMI_PAYMENT_NO']);

                    if (
                        $_POST['CID'] == 3 ||
                        $_POST['CID'] == 4 ||
                        $_POST['CID'] == 5 ||
                        $_POST['CID'] == 6 ||
                        $_POST['CID'] == 7
                    ) {
                        $count = VideoPayment::where([
                            'user_id' => $_POST['UID'],
                            //'classroom_id' => $_POST['CID'],
                            ['free', '>', 0]
                        ])->count();
                        if ($count == 0) {
                            $this->payPerVideo($_POST['UID'], $_POST['CID'], 'free', $_POST['WMI_PAYMENT_NO']);
                            $this->payPerLessosn($_POST['UID'], $_POST['CID'], 'free', $_POST['WMI_PAYMENT_NO']);
                        }
                    }
                } elseif ($_POST['TID'] == 2) {
                    $this->payPerVideo($_POST['UID'], $_POST['CID'], $_POST['WMI_PAYMENT_AMOUNT'], $_POST['WMI_PAYMENT_NO']);
                } elseif ($_POST['TID'] == 3) {
                    $this->payPerLessosn($_POST['UID'], $_POST['CID'], $_POST['WMI_PAYMENT_AMOUNT'], $_POST['WMI_PAYMENT_NO']);
                } elseif ($_POST['TID'] == 4) {
                    $this->payPerCourse($_POST['UID'], $_POST['CID'], $_POST['WMI_PAYMENT_AMOUNT'], $_POST['WMI_PAYMENT_NO']);
                } elseif ($_POST['TID'] == 5) {
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

                $ref = Referral::where([
                    'guest' => $_POST['UID'],
                    'status' => 0,
                ])->first();
                if ($ref && $ref->guest && $ref->status == 0) {
                    $ref->status = 1;
                    $ref->save();
                }

                // TODO: Пометить заказ, как «Оплаченный» в системе учета магазина
                $this->printAnswer("Ok", "Заказ #" . $_POST['WMI_PAYMENT_NO'] . " оплачен!");
            } else {
                // Случилось что-то странное, пришло неизвестное состояние заказа
                $this->printAnswer("Retry", "Неверное состояние ". $_POST['WMI_ORDER_STATE']);
            }
        } else {
            // Подпись не совпадает, возможно вы поменяли настройки интернет-магазина
            $this->printAnswer("Retry", "Неверная подпись " . $_POST['WMI_SIGNATURE']);
        }
    }

    //
    public function paymentTest()
    {
        $this->payPerPage(7, 1, 2000, 1234, true);
        $this->payPerVideo(7, 1, 2000, 1234, true);
        $this->payPerLessosn(7, 1, 2000, 1234, true);
    }
}
