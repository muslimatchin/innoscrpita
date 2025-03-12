<?php

use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\PreferenceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,10'); //adding extra throttle to prevent bot attack with register api
Route::post('/login', [AuthController::class, 'login']);
Route::get('/auto_login', [AuthController::class, 'usereDetails'])->middleware('auth:sanctum');
Route::get('/email/verify/{id}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/password-reset', [AuthController::class, 'sendPasswordResetLink']);

Route::post('/password-reset/confirm', [AuthController::class, 'resetPassword']);


Route::group([
    'prefix' => 'user-preferences',
    'middleware' => ['auth:sanctum'],
], function () {
    Route::get('/', [PreferenceController::class, 'getPreferences']);
    Route::post('/', [PreferenceController::class, 'store']);
});


Route::group([
    'prefix' => 'articles',
    'middleware' => ['auth:sanctum'],
], function () {
    Route::get('/', [ArticleController::class, 'getArticles']);
});

