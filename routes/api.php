<?php

use App\Http\Controllers\UrlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('token', [UrlController::class, 'getToken']);

Route::prefix('admin')->middleware('auth:api')->group(function () {
    Route::get('/urls', [UrlController::class, 'index']);
    Route::get('/urls/{id}', [UrlController::class, 'show']);
    Route::post('/urls', [UrlController::class, 'store']);
    Route::put('/urls/{id}', [UrlController::class, 'update']);
    Route::delete('/urls/{id}', [UrlController::class, 'destroy']);
});

