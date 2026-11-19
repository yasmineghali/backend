<?php

use App\Http\Controllers\Api\Bimrevo\ContactController as BimrevoContactController;
use App\Http\Controllers\Api\Bimrevo\NewsController as BimrevoNewsController;
use App\Http\Controllers\Api\Bimrevo\TrackController as BimrevoTrackController;
use App\Http\Controllers\Api\Maprevo\ContactController as MaprevoContactController;
use App\Http\Controllers\Api\Maprevo\NewsController as MaprevoNewsController;
use App\Http\Controllers\Api\Maprevo\TrackController as MaprevoTrackController;
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
RateLimiter::for('bimrevo-news', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));
RateLimiter::for('bimrevo-track', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));

Route::prefix('bimrevo')->name('bimrevo.')->group(function () {
    Route::post('/contacts', [BimrevoContactController::class, 'store'])
        ->middleware('throttle:bimrevo-contact')
        ->name('contacts.store');

    Route::post('/track', [BimrevoTrackController::class, 'store'])
        ->middleware('throttle:bimrevo-track')
        ->name('track.store');

    Route::middleware('throttle:bimrevo-news')->group(function () {
        Route::get('/news', [BimrevoNewsController::class, 'index'])->name('news.index');
        Route::get('/news/{slug}', [BimrevoNewsController::class, 'show'])->name('news.show');
    });
});

// MapRevo (separate site, separate table). Admin side lives in the Filament panel.
RateLimiter::for('maprevo-contact', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
RateLimiter::for('maprevo-news', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));
RateLimiter::for('maprevo-track', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));

Route::prefix('maprevo')->name('maprevo.')->group(function () {
    Route::post('/contacts', [MaprevoContactController::class, 'store'])
        ->middleware('throttle:maprevo-contact')
        ->name('contacts.store');

    Route::post('/track', [MaprevoTrackController::class, 'store'])
        ->middleware('throttle:maprevo-track')
        ->name('track.store');

    Route::middleware('throttle:maprevo-news')->group(function () {
        Route::get('/news', [MaprevoNewsController::class, 'index'])->name('news.index');
        Route::get('/news/{slug}', [MaprevoNewsController::class, 'show'])->name('news.show');
    });
});
