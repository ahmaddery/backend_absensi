<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\CartController;

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


// Route untuk mengambil semua produk
//Route::middleware('auth')->group(function () {
Route::get('/product', [ProductController::class, 'index']);
//});


Route::get('/customers', [CustomerController::class, 'index']);    // READ
Route::post('/customers', [CustomerController::class, 'store']);   // CREATE
Route::put('/customers/{id}', [CustomerController::class, 'update']);  // EDIT




Route::get('/cart', [CartController::class, 'index']);
//Route::get('/cart/{id}', [CartController::class, 'show']);
Route::post('/cart', [CartController::class, 'store']);
Route::put('/cart/{id}', [CartController::class, 'update']);
Route::delete('/cart/{id}', [CartController::class, 'destroy']);
Route::get('/cart/{cartId}/items', [CartController::class, 'showCartItems']);
Route::post('/cart/add-item', [CartController::class, 'addCartItem']);
 // Menambah item ke cart berdasarkan customer_id