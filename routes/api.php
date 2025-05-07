<?php

use App\Http\Controllers\API\InfoController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\SertificateController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\API\UsersController;
use App\Http\Controllers\API\ScoreController;
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

Route::post('signin', [UsersController::class, 'sendOtp']);
Route::post('authenticate', [UsersController::class, 'authenticate']);
Route::post('submit-otp', [UsersController::class, 'submitOtp']);

Route::group(['middleware' => ['jwt.auth']], function () {
    // Route::get('logout', [ApiController::class, 'logout']);
    Route::get('listStudents/{parentId}', [UsersController::class, 'students']);
    Route::get('announcement', [InfoController::class, 'getAnnouncement']);
    Route::get('advertise', [InfoController::class, 'getAdvertise']);
    Route::get('agenda/{studentId}', [InfoController::class, 'getAgenda']);
    Route::get('myPoint/{studentId}', [StudentController::class, 'getMyPoint']);
    Route::get('myNewPoint/{studentId}', [StudentController::class, 'getNewMyPoint']);
    Route::get('attendace/{studentId}', [StudentController::class, 'getAttendance']);
    Route::get('newAttendace/{studentId}', [StudentController::class, 'getNewAttendance']);
    Route::get('classItem/{studentId}', [StudentController::class, 'getClass']);
    Route::get('summaryStudent/{studentId}', [StudentController::class, 'getSummary']);
    Route::get('historyTest/{studentId}', [StudentController::class, 'getHistoryTest']);
    Route::get('class/{studentId}', [StudentController::class, 'getClassStudent']);
    Route::prefix('score')->group(function () {
        Route::get('/getTestItem', [ScoreController::class, 'getTest']);
        Route::get('/getResult/{studentId}', [ScoreController::class, 'getResult']);
        Route::get('/getScore/{studentId}/{testId}', [ScoreController::class, 'getScoreByTest']);
        // Route::get('/getCertificate/{studentId}', [ScoreController::class, 'getCertificate']);
    });
    Route::prefix('payment')->group(function () {
        Route::get('get-bill-month/{studentId}', [PaymentController::class, 'getBillMonth']);
        Route::get('/history/{studentId}', [PaymentController::class, 'getHistory']);
        Route::get('/detail/{idPayment}', [PaymentController::class, 'getDetailHistory']);
        Route::get('/billing/{studentId}', [PaymentController::class, 'listBill']);
        Route::post('/checkout', [PaymentController::class, 'checkout']);
        Route::get('/verify/{transId}', [PaymentController::class, 'verifyPayment']);
        Route::get('/printInvoice/{paymentId}', [PaymentController::class, 'printInvoice']);
        Route::get('/mybill/{studentId}', [PaymentController::class, 'billDetail']);
    });

    Route::get('getMySertificate/{studentId}', [SertificateController::class, 'apiGenerateCertificate']);
    Route::get('getNewResult/{studentId}', [SertificateController::class, 'getNewResult']);

    Route::get('/count-attendence/{studentId}', [InfoController::class, 'countAttendence']);
    Route::get('/e-receipt/{transId}/{phoneNumber}', [PaymentController::class, 'eReceipt']);
    Route::post('/broadcast', [PaymentController::class, 'broadcastLatePayment']);
});
