<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

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
Route::get('/search', [ItemController::class, 'search']);
Route::get('/mypage/profile', [ItemController::class, 'profileSetting']);
Route::get('/item/{id}', [ItemController::class, 'detail']);
Route::get('/mylist', [ItemController::class, 'mylist'])->name('mylist');

Route::post('/comment', [ItemController::class, 'comment']);
Route::post('/goods/{item_id}', [ItemController::class, 'goods']);

Route::get('/sell', [ItemController::class, 'sell']);
Route::post('/sell', [ItemController::class, 'sellRegister']);

Route::get('/mypage', [ItemController::class, 'mypage']);
Route::post('/profile', [ItemController::class, 'profileUpdate']);

Route::get('/purchase/{item_id}', [ItemController::class, 'purchase']);
Route::post('/purchase/{item_id}', [ItemController::class, 'purchaseDecision']);
Route::get('/purchase/address/{item_id}', [ItemController::class, 'addressChanging']);
Route::post('/purchase/address/{item_id}', [ItemController::class, 'addressUpdate']);
