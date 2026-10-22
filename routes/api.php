<?php

use App\Http\Controllers\Api\Bimrevo\ContactController as BimrevoContactController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\TrackController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

Route::post('/contacts', [ContactController::class, 'store'])
    ->middleware('throttle:5,1');

Route::post('/track', [TrackController::class, 'store'])
    ->middleware('throttle:120,1');

Route::get('/news',[NewsController::class, 'index']);
Route::get('/news/{slug}', [NewsController::class, 'show']);

// BIMRevo (separate site, separate table). Admin side lives in the Filament panel.
RateLimiter::for('bimrevo-contact', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));

Route::prefix('bimrevo')->name('bimrevo.')->group(function () {
    Route::post('/contacts', [BimrevoContactController::class, 'store'])
        ->middleware('throttle:bimrevo-contact')
        ->name('contacts.store');
});
