<?php

use App\Http\Controllers\Admin\LanguageController as AdminLanguageController;
use App\Http\Controllers\Api\LanguageController as ApiLanguageController;
use App\Http\Controllers\Api\IntroController as ApiIntroController;
use App\Http\Controllers\Admin\IntroController as AdminIntroController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\CheckUserAuthentication;
use App\Http\Middleware\CheckVendorAuthentication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//  user api routes
Route::prefix('user')->group(function () {
    // Public Apis Routes
    Route::get('get-languages', [ApiLanguageController::class, 'getLanguages']);
    Route::get('get-language-labels', [ApiLanguageController::class, 'getLanguageLabels']);
    Route::get('get-intro', [ApiIntroController::class, 'getIntro']);
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('sendResetLinkEmail', [UserController::class, 'sendResetLinkEmail']);
    Route::post('resetPassword', [UserController::class, 'resetPassword']);

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
