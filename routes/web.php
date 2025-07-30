<?php

use App\Models\Attendance;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ParentsController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\AnnouncesController;
use App\Http\Controllers\ExamPointController;
use App\Http\Controllers\TestItemsController;
use App\Http\Controllers\AdvertisesController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ResultFormController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ReedemItemsController;
use App\Http\Controllers\ReedemPointController;
use App\Http\Controllers\birthdayPoinController;
use App\Http\Controllers\EcertificateController;
use App\Http\Controllers\historyCertificateController;
use App\Http\Controllers\ScheduleClassController;
use App\Http\Controllers\ParentStudentsController;
use App\Http\Controllers\PointCategoriesController;
use App\Http\Controllers\ReviewTestPaperController;

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

Route::get('/', [UsersController::class, 'viewLogin']);
Route::post('/login', [UsersController::class, 'login']);
Route::get('/logout', [UsersController::class, 'logout']);
// Route::get('/tes', [UsersController::class, 'tes']);


Route::get('/print', [UsersController::class, 'print']);
Route::get('/printInvoice/{paymentId}', [PaymentController::class, 'printInvoice']);
Route::prefix('landing-page')->group(
    function () {
        Route::get('/redeem-point', [LandingPageController::class, 'redeemPoint']);
        Route::get('/redeem-point/{studentId}', [LandingPageController::class, 'redeemPointStudent']);
        Route::post('/store-redeem-point', [LandingPageController::class, 'storeReedemPoint']);
        Route::get('/e-certificate/{id}', [LandingPageController::class, 'eCertificate']);
        Route::get('/extra-point', [LandingPageController::class, 'extraPoint']);
        Route::post('/extra-point/store', [LandingPageController::class, 'storeExtraPoint'])->name('landing-page.extra-point.store');
    }
);

Route::get('generate', function () {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    echo 'ok';
});

Route::middleware(['web'])->group(function () {
    Route::get('/user', [UsersController::class, 'profile']);
    Route::post('/user/{user}', [UsersController::class, 'update']);
    Route::get('/dashboard', [UsersController::class, 'index']);
    Route::resource('/advertise', AdvertisesController::class);
    Route::resource('/announces', AnnouncesController::class);
    Route::resource('/reedemItems', ReedemItemsController::class);
    Route::resource('/pointCategories', PointCategoriesController::class);
    Route::resource('/tests', TestItemsController::class);
    Route::resource('/parents', ParentsController::class);
    Route::delete('/schedule-class/delete', [ScheduleClassController::class, 'delete']);
    Route::resource('/schedule-class', ScheduleClassController::class);
    Route::post('/parentStudent', [ParentsController::class, 'storeParentStudents']);
    Route::delete('/parentStudent/{id}', [ParentsController::class, 'deleteParentStudents']);
    Route::get('/update-otp/{id}', [ParentsController::class, 'updateOtp']);
    Route::prefix('score')->group(function () {
        Route::get('/form', [ScoreController::class, 'index']);
        Route::get('/form-create', [ScoreController::class, 'formCreate']);
        Route::get('/form-last/{priceId}', [ScoreController::class, 'createLast']);
        Route::get('/ajax-last-class', [ScoreController::class, 'ajaxLastClass']);
        Route::get('/create', [ScoreController::class, 'create']);
        Route::get('/create-last', [ScoreController::class, 'createLastFrom']);
        Route::get('/last', [ScoreController::class, 'lastForm']);
        Route::get('/students/filter', [ScoreController::class, 'filterStudent']);
        Route::get('/filterScore', [ScoreController::class, 'filter']);
        Route::post('/store', [ScoreController::class, 'store']);
        Route::post('/update/{score}', [ScoreController::class, 'update']);
    });
    Route::get('/history-test', [ScoreController::class, 'historyTest']);
    Route::prefix('attendance')->group(function () {
        Route::get('/form/{priceId}', [AttendanceController::class, 'create']);
        Route::get('/class', [AttendanceController::class, 'index']);
        Route::post('/store', [AttendanceController::class, 'store']);
        Route::get('/edit/{attendance}', [AttendanceController::class, 'edit']);
        Route::post('/update/{attendance}', [AttendanceController::class, 'update']);
        Route::get('/reminder', [AttendanceController::class, 'reminder']);
        Route::get('/reminder-done', [AttendanceController::class, 'reminderDone']);
        Route::get('/reminder-absen', [AttendanceController::class, 'reminderAbsen']);
        Route::post('/reminder-comment/{id}', [AttendanceController::class, 'addComment']);
        Route::get('/get-class', [AttendanceController::class, 'getClass']);
        Route::post('/update-class', [AttendanceController::class, 'updateClass']);
        Route::get('/get-student', [AttendanceController::class, 'ajaxStudent']);
    });
    Route::get('/mutasi', [AttendanceController::class, 'mutasi']);
    Route::post('/mutasi', [AttendanceController::class, 'storeMutasi']);
    Route::get('/reedemPoint', [ReedemPointController::class, 'create']);
    Route::post('/reedemPoint', [ReedemPointController::class, 'store']);
    Route::get('/saldo-awal', [ReedemPointController::class, 'saldoAwal']);
    Route::get('/saldo-awal/form/{id}', [ReedemPointController::class, 'createSaldoAwal']);
    Route::post('/saldo-awal', [ReedemPointController::class, 'storeSaldoAwal']);
    Route::post('/pemberian-saldo', [ReedemPointController::class, 'giftPoint']);
    Route::get('/history-point', [ReedemPointController::class, 'histori']);
    Route::get('/history-point/{studentid}', [ReedemPointController::class, 'historiAjax']);
    Route::get('/students/filter', [ScoreController::class, 'filterStudentByName']);
    Route::get('/get-barcode-student', [ParentStudentsController::class, 'student']);
    Route::get('/barcode-student', [ParentStudentsController::class, 'barcode']);
    Route::get('/barcode-download/{id}', [ParentStudentsController::class, 'download']);
    Route::get('/barcode-print/{id}', [ParentStudentsController::class, 'print']);
    Route::get('/barcode-print-all', [ParentStudentsController::class, 'printAll']);
    Route::get('/review', [ReviewTestPaperController::class, 'index']);
    Route::get('/review-done/{id}', [ReviewTestPaperController::class, 'done']);
    Route::post('/review-comment/{id}', [ReviewTestPaperController::class, 'comment']);
    Route::delete('/review/{id}', [ReviewTestPaperController::class, 'destroy']);
    Route::resource('/e-certificate', EcertificateController::class);
    Route::delete('bulk/follow-up', [FollowUpController::class, 'bulkDestroy'])->name('bulk.follow-up');
    Route::resource('/follow-up', FollowUpController::class);

    Route::post('/teacher-transfer', [AttendanceController::class, 'mutasi_class'])->name('mutasi-class');

    Route::get('/result-form', [ResultFormController::class, 'index']);
    Route::post('/result-form/{id}', [ResultFormController::class, 'update'])->name('result-form.update');

    Route::get('/birthday-point', [birthdayPoinController::class, 'index']);
    Route::post('/birthday-point/store', [birthdayPoinController::class, 'store'])->name('birthday-point.store');

    Route::get('/extra-point', [ExamPointController::class, 'index']);
    Route::post('/extra-point/store', [ExamPointController::class, 'store'])->name('extra-point.store');

    Route::get('/history-certificate', [historyCertificateController::class, 'index'])->name('history-certificate.index');
});
