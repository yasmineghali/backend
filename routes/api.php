<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\TrackController;
use Illuminate\Support\Facades\Route;

Route::post('/contacts', [ContactController::class, 'store'])
    ->middleware('throttle:5,1');

Route::post('/track', [TrackController::class, 'store'])
    ->middleware('throttle:120,1');

Route::get('/news',[NewsController::class, 'index']);
Route::get('/news/{slug}', [NewsController::class, 'show']);
