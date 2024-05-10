<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ImageController;
use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;
use Dedoc\Scramble\Facades\Generator;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReviewController;




Route::group([
    'middleware' => 'api',
], function ($router) {

    // Users
    Route::post('login', [UserController::class, 'loginOrRegister']);
    Route::post('register', [UserController::class, 'register']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('refresh', [UserController::class, 'refresh']);
    Route::get('/user/{_id}', [UserController::class, 'getUserById']);
    Route::get('/user/mobile/{mobile}', [UserController::class, 'getUserByMobile']);
    Route::put('/user/{_id}', [UserController::class, 'updateUser']);
    Route::delete('/user/{_id}', [UserController::class, 'deleteUserById']);
    Route::get('/users', [UserController::class, 'User_list']);

    // Properties
    Route::get('/properties', [PropertyController::class, 'property_list']);
    Route::get('/getproperties/{id}',  [PropertyController::class, 'getPropertiesByUserId']);
    Route::post('/property/add', [PropertyController::class, 'storeProperty']);
    Route::put('/property/{_id}', [PropertyController::class, 'updateProperty']);
    Route::delete('/property/{_id}', [PropertyController::class, 'deletePropertyById']);

    // Payments
    Route::get('/payments', [PaymentController::class, 'Payment_list']);
    Route::post('/make/payment', [PaymentController::class, 'MakePayment']);
    Route::get('/payment/{user_id}', [PaymentController::class, 'getPaymentsByUserID']);
    Route::get('/payment/{_id}', [PaymentController::class, 'getPaymentById']);
    Route::put('/payment/{_id}', [PaymentController::class, 'updatePayment']);
    Route::delete('/payment/{_id}', [PaymentController::class, 'deletePaymentById']);

    //chat
    Route::post('/chat', [ChatController::class, 'sendMessage']);
    Route::post('/chat/messages/{id}', [ChatController::class, 'getChat']);

    // rating
    Route::post('/ratings', [RatingController::class, 'store']);
    Route::get('/ratings/total', [RatingController::class, 'getUserRatings']);

    // review
    Route::post('/reviews', [ReviewController::class, 'storeReview']);
    Route::put('reviews/{id}', [ReviewController::class, 'updateReview']);
    Route::delete('reviews/{id}', [ReviewController::class, 'deleteReview']);



});


