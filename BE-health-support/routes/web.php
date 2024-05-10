<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\PropertyController;
use App\Http\Controllers\Web\DealersController;

// Guest routes
Route::middleware(['redirectIfSessionExists'])->group(function () {
    
    Route::redirect('/', '/login');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'registerSave'])->name('register.save');
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginAction'])->name('login.action');
});

// Admin routes
Route::middleware(['web', 'admin'])->group(function () {

        Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
        Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
        Route::get('/change-password', [AuthController::class, 'changePasswordView'])->name('changePassword');
        Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change/password');
        Route::get('/dealer/{user_id}/properties',[PropertyController::class, 'showUserProperties'] )->name('user/properties');
        
        Route::get('/dealers', [DealersController::class, 'dealers'])->name('dealers');
        Route::get('/dealers/create', [DealersController::class, 'create'])->name('dealer/create');
        Route::post('/dealers/save', [DealersController::class, 'save'])->name('dealer/save');
        Route::get('/dealers/edit/{id}', [DealersController::class, 'edit'])->name('dealer/edit');
        Route::put('/dealers/update/{id}', [DealersController::class, 'updateDealer'])->name('dealer/update');
        Route::get('/dealers/delete/{id}', [DealersController::class, 'delete'])->name('dealer/delete');

        Route::get('/properties', [PropertyController::class, 'index'])->name('properties');
        Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties/create');
        Route::post('/properties/save', [PropertyController::class, 'save'])->name('properties/save');
        Route::get('/properties/edit/{id}', [PropertyController::class, 'edit'])->name('properties/edit');
        Route::put('/properties/update/{id}', [PropertyController::class, 'update'])->name('properties/update');
        Route::get('/properties/delete/{id}', [PropertyController::class, 'delete'])->name('properties/delete');

        Route::get('/download-apk',  [HomeController::class, 'downloadApk'] )->name('download.apk');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::fallback(function () {
            return view('errors.404');
        });
    });
 
// routes/web.php


    Route::fallback(function () {
        return redirect('/login');
    });