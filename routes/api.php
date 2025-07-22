<?php


use App\Http\Controllers\Admin\LanguageController as AdminLanguageController;
use App\Http\Controllers\Admin\IntroController as AdminIntroController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LanguageController as ApiLanguageController;
use App\Http\Controllers\Api\IntroController as ApiIntroController;

use App\Http\Middleware\CheckUserAuthentication;
use Illuminate\Support\Facades\Route;

//  user api routes
Route::prefix('user')->group(function () {
    // Public Apis Routes
    Route::get('get-languages', [ApiLanguageController::class, 'getLanguages']);
    Route::get('get-language-labels', [ApiLanguageController::class, 'getLanguageLabels']);
    Route::get('get-intro', [ApiIntroController::class, 'getIntro']);
    Route::post('register', [UserController::class, 'register']);

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
    Route::get('/', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/create', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/show/{id}', [AdminUserController::class, 'show'])->name('users.show');
    Route::get('/edit/{id}', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::post('/update/{id}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/delete/{id}', [AdminUserController::class, 'destroy'])->name('users.delete');
});

Route::group(['prefix' => 'banners'], static function () {
    Route::get('/', [AdminBannerController::class, 'index'])->name('banners.index');
    Route::post('/create', [AdminBannerController::class, 'store'])->name('banners.store');
    Route::get('/show/{id}', [AdminBannerController::class, 'show'])->name('banners.show');
    Route::get('/edit/{id}', [AdminBannerController::class, 'edit'])->name('banners.edit');
    Route::post('/update/{id}', [AdminBannerController::class, 'update'])->name('banners.update');
    Route::delete('/delete/{id}', [AdminBannerController::class, 'destroy'])->name('banners.delete');
});
