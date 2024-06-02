<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrlController;


Route::get('/{slug}', [UrlController::class, 'redirect']);
