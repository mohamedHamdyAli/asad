<?php

use App\Http\Controllers\Admin\BannerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\CheckUserAuthentication;
use App\Http\Middleware\CheckVendorAuthentication;
use App\Http\Controllers\Api\IntroController as ApiIntroController;
use App\Http\Controllers\Admin\IntroController as AdminIntroController;
use App\Http\Controllers\Api\LanguageController as ApiLanguageController;
use App\Http\Controllers\Admin\LanguageController as AdminLanguageController;

//  user api routes
Route::prefix('uaer')->group(function () {
    // Public Apis Routes
    Route::get('get-languages', [ApiLanguageController::class, 'getLanguages']);
    Route::get('get-language-labels', [ApiLanguageController::class, 'getLanguageLabels']);
    Route::get('get-intro', [ApiIntroController::class, 'getIntro']);

    // Protected routes with middleware
    Route::middleware([CheckUserAuthentication::class])->group(function () {
        // user Profile

    });
});


Route::group(['prefix' => 'language'], static function () {
    Route::post('/create', [AdminLanguageController::class, 'store'])->name('language.store');
    Route::post('/update/{id}', [AdminLanguageController::class, 'update'])->name('language.update');
    Route::get('/show/{id}', [AdminLanguageController::class, 'show'])->name('language.show');
    Route::get('/set-language/{lang}', [AdminLanguageController::class, 'setLanguage'])->name('language.set-current');
    Route::get('/languageedit/{id}/{type}', [AdminLanguageController::class, 'editLanguage'])->name('languageedit');
    Route::post('/updatelanguageValues/{id}/{type}', [AdminLanguageController::class, 'updatelanguageValues'])->name('updatelanguageValues');
    Route::delete('/delete/{id}', [AdminLanguageController::class, 'destroy'])->name('language.delete');

});

Route::group(['prefix' => 'intro'], static function () {
    Route::get('/', [AdminIntroController::class, 'index'])->name('intro.index');
    Route::post('/create', [AdminIntroController::class, 'store'])->name('intro.store');
    Route::get('/show/{id}', [AdminIntroController::class, 'show'])->name('intro.show');
    Route::get('/edit/{id}', [AdminIntroController::class, 'edit'])->name('intro.edit');
    Route::post('/update/{id}', [AdminIntroController::class, 'update'])->name('intro.update');
    Route::delete('/delete/{id}', [AdminIntroController::class, 'destroy'])->name('intro.delete');
});


Route::group(['prefix' => 'users'], static function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::post('/create', [UserController::class, 'store'])->name('users.store');
    Route::get('/show/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/update/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('users.delete');
});

Route::group(['prefix' => 'banners'], static function () {
    Route::get('/', [BannerController::class, 'index'])->name('banners.index');
    Route::post('/create', [BannerController::class, 'store'])->name('banners.store');
    Route::get('/show/{id}', [BannerController::class, 'show'])->name('banners.show');
    Route::get('/edit/{id}', [BannerController::class, 'edit'])->name('banners.edit');
    Route::post('/update/{id}', [BannerController::class, 'update'])->name('banners.update');
    Route::delete('/delete/{id}', [BannerController::class, 'destroy'])->name('banners.delete');
});
