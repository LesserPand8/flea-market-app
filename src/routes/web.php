<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileSettingController;
use App\Models\Profile;
use App\Models\Purchase;

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
Route::post('/comment', [DetailController::class, 'comment']);
Route::post('/goods/{item_id}', [DetailController::class, 'goods']);

Route::get('/purchase/{item_id}', [PurchaseController::class, 'purchase']);
Route::post('/purchase/{item_id}', [PurchaseController::class, 'purchaseDecision']);
Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'addressChanging']);
Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'addressUpdate']);

Route::get('/mypage', [ProfileController::class, 'mypage']);

Route::get('/mypage/profile', [ProfileSettingController::class, 'profileSetting']);
Route::post('/profile', [ProfileSettingController::class, 'profileUpdate']);



Route::get('/search', [ItemController::class, 'search']);



Route::get('/mylist', [ItemController::class, 'mylist'])->name('mylist');






Route::get('/sell', [ItemController::class, 'sell']);
Route::post('/sell', [ItemController::class, 'sellRegister']);
