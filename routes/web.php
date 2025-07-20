<?php

use App\Http\Controllers\Admin\IntroController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/settings', fn() => inertia('Settings'))->name('settings');
Route::get('/messages', fn() => inertia('Messages'))->name('messages');
Route::get('/spc-management', fn() => inertia('SPCsManagement'))->name('spc-management');
Route::get('/bids-management', fn() => inertia('BidsManagement'))->name('bids-management');
Route::get('/languages', fn() => inertia('LanguageManagement'))->name('language-management');
Route::get('/language-editor', fn() => inertia('LanguageEditor'))->name('language-editor');
Route::get('/finance-management', fn() => inertia('FinancialReports'))->name('finance-management');
Route::get('/users-management', fn() => inertia('UserManagement'))->name('users-management');
Route::get('/intro-management', fn() => inertia('IntroManagement'))->name('intro-management');

Route::get('/dashboard', function () {
    return Inertia::render('MainSDashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::group(['prefix' => 'language'], static function () {
//     Route::post('/create', [LanguageController::class, 'store'])->name('language.store');
//     Route::post('/update/{id}', [LanguageController::class, 'update'])->name('language.update');
//     Route::get('/show/{id}', [LanguageController::class, 'show'])->name('language.show');
//     Route::get('/set-language/{lang}', [LanguageController::class, 'setLanguage'])->name('language.set-current');
//     Route::get('/languageedit/{id}/{type}', [LanguageController::class, 'editLanguage'])->name('languageedit');
//     Route::post('/updatelanguageValues/{id}/{type}', [LanguageController::class, 'updatelanguageValues'])->name('updatelanguageValues');
//     Route::delete('/delete/{id}', [LanguageController::class, 'destroy'])->name('language.delete');

// });

// Route::group(['prefix' => 'intro'], static function () {
//     Route::get('/', [IntroController::class, 'index'])->name('intro.index');
//     Route::post('/create', [IntroController::class, 'store'])->name('intro.store');
//     Route::get('/show/{id}', [IntroController::class, 'show'])->name('intro.show');
//     Route::get('/edit/{id}', [IntroController::class, 'edit'])->name('intro.edit');
//     Route::post('/update/{id}', [IntroController::class, 'update'])->name('intro.update');
//     Route::delete('/delete/{id}', [IntroController::class, 'destroy'])->name('intro.delete');
// });

Route::get('/get-csrf-token', fn() => response()->json([
    'csrf_token' => csrf_token()
]));

require __DIR__ . '/auth.php';
