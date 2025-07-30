<?php


use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckUserAuthentication;
use App\Http\Controllers\Api\IntroController as ApiIntroController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LanguageController as ApiLanguageController;
use App\Http\Controllers\Api\BannerController as ApiBannerController;
use App\Http\Controllers\Admin\UnitController as AdminUnitController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\IntroController as AdminIntroController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\VendorController as AdminVendorController;
use App\Http\Controllers\Admin\LanguageController as AdminLanguageController;

//  user api routes
Route::prefix('user')->group(function () {
    // Public Apis Routes
    Route::get('get-languages', [ApiLanguageController::class, 'getLanguages']);
    Route::get('get-language-labels', [ApiLanguageController::class, 'getLanguageLabels']);
    Route::get('get-intro', [ApiIntroController::class, 'getIntro']);
    Route::get('get-banner', [ApiBannerController::class, 'getBanner']);
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

Route::group(['prefix' => 'banners'], static function () {
    Route::get('/', [AdminBannerController::class, 'index'])->name('banners.index');
    Route::post('/create', [AdminBannerController::class, 'store'])->name('banners.store');
    Route::get('/show/{id}', [AdminBannerController::class, 'show'])->name('banners.show');
    Route::get('/edit/{id}', [AdminBannerController::class, 'edit'])->name('banners.edit');
    Route::post('/update/{id}', [AdminBannerController::class, 'update'])->name('banners.update');
    Route::delete('/delete/{id}', [AdminBannerController::class, 'destroy'])->name('banners.delete');
});


Route::group(['prefix' => 'users'], static function () {
    Route::get('/', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/create', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/show/{id}', [AdminUserController::class, 'show'])->name('users.show');
    Route::get('/edit/{id}', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::post('/update/{id}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/delete/{id}', [AdminUserController::class, 'destroy'])->name('users.delete');
});


Route::group(['prefix' => 'vendors'], static function () {
    Route::get('/', [AdminVendorController::class, 'index'])->name('vendors.index');
    Route::post('/create', [AdminVendorController::class, 'store'])->name('vendors.store');
    Route::get('/show/{id}', [AdminVendorController::class, 'show'])->name('vendors.show');
    Route::get('/edit/{id}', [AdminVendorController::class, 'edit'])->name('vendors.edit');
    Route::post('/update/{id}', [AdminVendorController::class, 'update'])->name('vendors.update');
    Route::delete('/delete/{id}', [AdminVendorController::class, 'destroy'])->name('vendors.delete');
});


Route::group(['prefix' => 'units'], static function () {
    Route::get('/', [AdminUnitController::class, 'index'])->name('units.index');
    Route::get('/user/{userId}', [AdminUnitController::class, 'userUnits'])->name('units.user');
    Route::get('/vendor/{vendorId}', [AdminUnitController::class, 'vendorUnits'])->name('units.vendor');
    Route::post('/create', [AdminUnitController::class, 'store'])->name('units.store');
    Route::get('/show/{id}', [AdminUnitController::class, 'show'])->name('units.show');
    Route::post('/update/{id}', [AdminUnitController::class, 'update'])->name('units.update');
    Route::delete('/delete/{id}', [AdminUnitController::class, 'destroy'])->name('units.delete');
});
