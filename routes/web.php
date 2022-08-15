<?php

use App\Models\Classroom;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserQRCode;
use App\Models\Video;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('storage/{dir}/{filename}', function ($dir, $filename) {
	$path = storage_path('app/public/' . $dir . '/' . $filename);
    if (!File::exists($path) || $dir == 'pages') {
        abort(404);
    }
    $file = File::get($path);
    $type = File::mimeType($path);
    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
});

//Route::get('/', function () {
    //dd(date('YmdHis'));
    // for ($i=0; $i < 700; $i++) { 
    //     $key = Str::random(48);
    //     Lesson::where([
    //         ['id', $i],
    //     ])->update([
    //         'key' => $key,
    //     ]);
    // }
    // return view('welcome');
//});

// Route::get('qr-code', function () {
//     return QrCode::encoding('UTF-8')->size(500)->generate('Добро пожаловать на codehunter.kz');
// });

Route::get('qrcode/{code}', function($code) {
    QrCode::format('png')->size(420)->generate($code, storage_path('app/public/qrcode/'.$code.".png"));
    if (file_exists(storage_path('app/public/qrcode/'.$code.".png"))) {
        $filename = storage_path('app/public/qrcode/'.$code.".png");
        return response()->download($filename, "my-qrcode.png");
    }
});

Route::post('walletone/payment', 'App\Http\Controllers\WalletoneController@payment');
Route::get('walletone/payment/test', 'App\Http\Controllers\WalletoneController@paymentTest');

Route::get('dWPJtU6a3BIX/{classroom}/{start}', function($classroom, $start) {
    $end = $start + 2000;
    $users = User::with([
        'pagePayment' => function($query) use($classroom) {
            $query->with('classroom')->where("classroom_id", $classroom)->orderBy('id', 'desc');
        },
        'lessonPayment' => function($query) use($classroom) {
            $query->with('classroom')->where("classroom_id", $classroom)->first();
        },
        'videoPayment' => function($query) use($classroom) {
            $query->with('classroom')->where("classroom_id", $classroom)->first();
        },
    ])->where([
        ['id', '>', $start],
        ['id', '<', $end]
    ])->get();
    
    if ($users) {
        $string = "";
        foreach ($users as $key => $user) {
            if (
                !empty($user->pagePayment) &&
                !$user->lessonPayment &&
                !$user->videoPayment
            ) {
				$string .= "Телефон: " . $user->phone . "\n\r" .
                    "От: ".mb_strimwidth($user->pagePayment->created_at, 0, 10) . "\n\r" .
                    "До: ".mb_strimwidth($user->pagePayment->end_date, 0, 10) . "\n\r\n\r";
            }
        }
        $filename = $classroom."_".$start."-".$end.'.txt';
        Storage::put($filename, $string);
        $storage_path = storage_path('app/'.$filename);
        return response()->download($storage_path, $filename);
    }
});

Route::get('/vimeo', 'App\Http\Controllers\VimeoController@vimeo');

// Route::get('/update', function () {
//     User::where('id', '>', 0)->update(array('device_count' => 1));
// })->name('shop');



Route::get('/', function () {
    $number = 2000;
    $proc = 30;
$result = ($number/100*$proc);
//$result = $number - $result;
dd($result);
});