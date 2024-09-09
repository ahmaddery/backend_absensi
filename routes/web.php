<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\PosController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rute untuk halaman dashboard admin dengan middleware 'admin'
// route untuk user ke halaman utama users
Route::get('/home',[HomeController::class,'index']);
Route::middleware('admin')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/auth/redirect', [SocialiteController::class, 'redirect']);
Route::get('/auth/google/callback', [SocialiteController::class, 'callback']);
 

require __DIR__.'/auth.php';


// route untuk adminnn

// Rute untuk mengelola peran pengguna
Route::middleware('admin')->group(function () {
    Route::get('/permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
    Route::get('permissions/{id}/{name}/edit', [PermissionController::class, 'edit'])->name('admin.permissions.edit');
    Route::put('permissions/{id}/{name}', [PermissionController::class, 'update'])->name('admin.permissions.update');
    Route::delete('/permissions/{id}', [PermissionController::class, 'destroy'])->name('admin.permissions.destroy');
    Route::post('/permissions/{id}/restore', [PermissionController::class, 'restore'])->name('admin.permissions.restore');
});


Route::middleware('admin')->group(function () {
    Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/admin/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/admin/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::get('/admin/products/trashed', [ProductController::class, 'trashed'])->name('admin.products.trashed');
    Route::post('/admin/products/{id}/restore', [ProductController::class, 'restore'])->name('admin.products.restore');
});

Route::middleware('admin')->group(function () {
    Route::get('/admin/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/admin/customers/create', [CustomerController::class, 'create'])->name('admin.customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::get('/admin/customers/{customer}', [CustomerController::class, 'show'])->name('admin.customers.show');
    Route::get('/admin/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');
    Route::patch('/admin/customers/{id}/restore', [CustomerController::class, 'restore'])->name('admin.customers.restore');

});


Route::middleware('admin')->group(function () {
    Route::get('/admin/discounts', [DiscountController::class, 'index'])->name('admin.discounts.index');
    Route::get('/admin/discounts/create', [DiscountController::class, 'create'])->name('admin.discounts.create');
    Route::post('/discounts', [DiscountController::class, 'store'])->name('admin.discounts.store');
    Route::get('/admin/discounts/{discount}', [DiscountController::class, 'show'])->name('admin.discounts.show');
    Route::get('/admin/discounts/{discount}/edit', [DiscountController::class, 'edit'])->name('admin.discounts.edit');
    Route::put('/admin/discounts/{discount}', [DiscountController::class, 'update'])->name('admin.discounts.update');
    Route::delete('/admin/discounts/{discount}', [DiscountController::class, 'destroy'])->name('admin.discounts.destroy');

});

Route::middleware('admin')->group(function () {
    Route::get('pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('pos', [PosController::class, 'store'])->name('admin.pos.store');
});