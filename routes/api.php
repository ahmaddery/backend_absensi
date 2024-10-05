<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PasswordResetLinkController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//request login  dan register
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('register', [RegisterController::class, 'register']);
Route::post('/password/reset-link', [PasswordResetLinkController::class, 'sendResetLink']);


// Route untuk mengambil semua produk
Route::middleware(['check.token'])->group(function () {
    Route::get('/product', [ProductController::class, 'index']); // Route untuk mengambil semua produk
    Route::get('/product/{id}', [ProductController::class, 'show']); // Route untuk mengambil produk berdasarkan ID
    Route::get('products/search', [ProductController::class, 'search']);
    Route::get('products/barcode/{barcode}', [ProductController::class, 'checkPriceByBarcode']);
    Route::post('/products', [ProductController::class, 'create']);
});