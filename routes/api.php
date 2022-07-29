<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('language')->group(function() {
    Route::post('/classrooms', 'App\Http\Controllers\API\LanguageController@classrooms');
    Route::post('/classrooms-with-subjects-and-books', 'App\Http\Controllers\API\LanguageController@classroomsWithSubjectsAndBooks');
    Route::post('/classroom', 'App\Http\Controllers\API\LanguageController@classroom');
    Route::post('gdz', 'App\Http\Controllers\API\LanguageController@gdz');
    Route::post('video', 'App\Http\Controllers\API\LanguageController@video');
    Route::post('lessons', 'App\Http\Controllers\API\LanguageController@lessons');
});

Route::prefix('classroom')->group(function() {
    Route::post('get-by-id-and-language-id', 'App\Http\Controllers\API\ClassroomController@getByIdAndLanguageId');
});

Route::prefix('book')->group(function() {
    Route::post('pages', 'App\Http\Controllers\API\BookController@pages');
    Route::post('video', 'App\Http\Controllers\API\BookController@video');
    Route::post('lessons', 'App\Http\Controllers\API\BookController@lessons');
});

Route::prefix('page')->group(function() {
    Route::get('show', 'App\Http\Controllers\API\PageController@show');
    Route::post('get-by-key', 'App\Http\Controllers\API\PageController@getByKey');
});

Route::prefix('lesson')->group(function() {
    Route::post('show', 'App\Http\Controllers\API\LessonController@show');
});

Route::prefix('faq')->group(function () {
    Route::post('get', 'App\Http\Controllers\API\FAQController@get');
});

Route::prefix('courses')->group(function() {
    Route::post('/', 'App\Http\Controllers\API\CourseController@courses');
    Route::post('get-by-id-category', 'App\Http\Controllers\API\CourseController@getByIDCategory');
    Route::post('get-by-id', 'App\Http\Controllers\API\CourseController@getByID');
    Route::post('video', 'App\Http\Controllers\API\CourseController@video');
});

// Route::prefix('pb')->group(function() {
//     Route::post('check', 'App\Http\Controllers\API\PBController@check');        // URL для проверки возможности платежа.
//     Route::post('result', 'App\Http\Controllers\API\PBController@result');      // URL для сообщения о результате платежа.
//     Route::post('refund', 'App\Http\Controllers\API\PBController@refund');      // URL для сообщения об отмене платежа.
//     Route::post('capture', 'App\Http\Controllers\API\PBController@capture');    // URL для сообщения о проведении клиринга платежа по банковской карте.
// });

Route::group(['middleware' => 'auth:api'], function() {
    Route::prefix('user')->group(function() {
        Route::post('logout', 'App\Http\Controllers\API\UserController@logout');
        Route::post('qr-code-generation', 'App\Http\Controllers\API\UserController@qrCodeGeneration');
        Route::post('get-qr-code', 'App\Http\Controllers\API\UserController@getQRCode');
        Route::post('referrals', 'App\Http\Controllers\API\UserController@referrals');
    });

    Route::prefix('walletone')->group(function() {
        Route::post('fpf', 'App\Http\Controllers\API\WalletoneController@fpf');
    });

    //payment
    Route::prefix('payment')->group(function() {
        Route::post('history', 'App\Http\Controllers\API\PaymentController@history');
    });

    Route::prefix('favorite')->group(function() {
        Route::post('gdz', 'App\Http\Controllers\API\FavoriteController@gdz');
        Route::post('exercise', 'App\Http\Controllers\API\FavoriteController@exercise');
        Route::post('lesson', 'App\Http\Controllers\API\FavoriteController@lesson');
        Route::post('favorites', 'App\Http\Controllers\API\FavoriteController@favorites');
    });

    Route::post('qr-code', 'App\Http\Controllers\API\QRCodeController@get');
});

Route::group(['middleware' => 'guest'], function() {
    Route::prefix('user')->group(function() {
        Route::post('signin', 'App\Http\Controllers\API\UserController@signin');
        Route::post('signup', 'App\Http\Controllers\API\UserController@signup');
        Route::post('verify', 'App\Http\Controllers\API\UserController@verify');
        Route::post('password-reset-verify', 'App\Http\Controllers\API\UserController@passwordResetVerify');
        Route::post('password-reset', 'App\Http\Controllers\API\UserController@passwordReset');
    });
});
    
//
Route::prefix('ad')->group(function() {
    Route::post('/', 'App\Http\Controllers\API\AdController@get');
});

//
Route::prefix('kaspi')->group(function() {
    Route::get('payment', 'App\Http\Controllers\API\KaspiController@payment');
});



// Application
Route::prefix('app')->group(function() {
    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::group(['middleware' => 'auth:api'], function() {
        Route::prefix('user')->group(function() {
            Route::post('/', 'App\Http\Controllers\API\App\UserController@get');
            Route::post('/notification-increment', 'App\Http\Controllers\API\App\UserController@notificationIncrement');
        });

        Route::prefix('favorite')->group(function() {
            Route::post('gdz', 'App\Http\Controllers\API\App\FavoriteController@gdz');
            Route::post('exercise', 'App\Http\Controllers\API\App\FavoriteController@exercise');
            Route::post('lesson', 'App\Http\Controllers\API\App\FavoriteController@lesson');
            Route::post('favorites', 'App\Http\Controllers\API\App\FavoriteController@favorites');
        });

        Route::prefix('notification')->group(function() {
            Route::post('android/get-relevant', 'App\Http\Controllers\API\App\NotificationAndroidController@getRelevant');
        });

        //payment
        Route::prefix('payment')->group(function() {
            Route::post('history', 'App\Http\Controllers\API\App\PaymentController@history');
        });
    });

    Route::group(['middleware' => 'guest'], function() {
        Route::prefix('user')->group(function() {
            Route::post('sign-in', 'App\Http\Controllers\API\App\UserController@signIn');
            Route::post('sign-in-ios', 'App\Http\Controllers\API\App\UserController@signInIOS');
        });
    });
    
    Route::prefix('classroom')->group(function() {
        Route::post('by-lang-id', 'App\Http\Controllers\API\App\ClassroomController@byLangId');
        Route::post('by-id', 'App\Http\Controllers\API\App\ClassroomController@byId');
    });

    Route::prefix('book')->group(function() {
        Route::post('gdz', 'App\Http\Controllers\API\App\BookController@gdz');
        Route::post('video-solutions', 'App\Http\Controllers\API\App\BookController@videoSolutions');
        Route::post('video-lessons', 'App\Http\Controllers\API\App\BookController@videoLessons');
    });

    Route::prefix('gdz-page')->group(function() {
        Route::get('get', 'App\Http\Controllers\API\App\PageController@get');
    });

    Route::prefix('video-lesson')->group(function() {
        Route::post('show', 'App\Http\Controllers\API\App\LessonController@show');
    });

    Route::prefix('courses')->group(function() {
        Route::post('/', 'App\Http\Controllers\API\App\CourseController@courses');
        Route::post('get-by-id-category', 'App\Http\Controllers\API\App\CourseController@getByIDCategory');
        Route::post('get-by-id', 'App\Http\Controllers\API\App\CourseController@getByID');
        Route::post('get-video-by-id', 'App\Http\Controllers\API\App\CourseController@videoById');
    });
    
    //
    Route::prefix('ad')->group(function() {
        Route::post('/', 'App\Http\Controllers\API\App\AdController@get');
    });
});
