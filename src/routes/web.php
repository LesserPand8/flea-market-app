<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileSettingController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\PaymentController;

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

Route::get('/', [ItemController::class, 'index']);

Route::get('/item/{id}', [DetailController::class, 'detail']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/comment', [DetailController::class, 'comment']);
    Route::post('/goods/{item_id}', [DetailController::class, 'goods']);

    Route::get('/purchase/{item_id}', [PurchaseController::class, 'purchase']);
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'purchaseDecision']);
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'addressChanging']);
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'addressUpdate']);

    Route::get('/mypage', [ProfileController::class, 'mypage']);

    Route::get('/mypage/profile', [ProfileSettingController::class, 'profileSetting']);
    Route::post('/profile', [ProfileSettingController::class, 'profileUpdate']);

    Route::get('/sell', [SellController::class, 'sell']);
    Route::post('/sell', [SellController::class, 'sellRegister']);
});

use Illuminate\Foundation\Auth\EmailVerificationRequest;
// メール認証完了時にプロフィール編集画面へ遷移
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/mypage/profile');
})->middleware(['auth', 'signed'])->name('verification.verify');


// 決済画面表示
Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
// 決済処理実行
Route::post('/payment', [PaymentController::class, 'store'])->name('payment.store');
