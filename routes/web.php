<?php

use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn() => view('home.home'));
Route::get('/about', fn() => view('about.about'));
Route::get('/contact', fn() => view('contact.contact'));
Route::get('/project', fn() => view('portfolio.portfolio'));
Route::get('/project-details/{id}', fn($id) => view('portfolio.project-details.project-details', ['id' => $id]));
Route::get('/our-services', fn() => view('services.services'));
Route::get('/qhse-policy', fn() => view('qhse_policy.qhsePolicy'));
Route::get('lang/{locale}', [LanguageController::class, 'setLanguage']);


Route::get('/login', fn() => inertia('Auth/Login'))->name('login');


Route::get('/settings', fn() => inertia('Settings'))->name('settings');
Route::get('/messages', fn() => inertia('Messages'))->name('messages');
Route::get('/Vendors-management', fn() => inertia('VendorsManagement'))->name('vendors-management');
Route::get('/bids-management', fn() => inertia('BidsManagement'))->name('bids-management');
Route::get('/languages', fn() => inertia('LanguageManagement'))->name('language-management');
// Route::get('/language-editor', fn() => inertia('LanguageEditor'))->name('language-editor');
Route::get('/finance-management', fn() => inertia('FinancialReports'))->name('finance-management');
Route::get('/users-management', fn() => inertia('UserManagement'))->name('users-management');
Route::get('/intro-management', fn() => inertia('IntroManagement'))->name('intro-management');
Route::get('/banner-management', fn() => inertia('BannerManagement'))->name('banner-management');
Route::get('/contractors-management', fn() => inertia('Contractors/Index'))->name('contractors-management');
Route::get('/Consultants-management', fn() => inertia('Consultants/Index'))->name('Consultants-management');
Route::get('/contactus-management', fn() => inertia('ContactUsPage'))->name('contactus-management');
Route::get('/unit-quotes', fn() => inertia('UnitQuotes'))->name('unit-quotes');
Route::get('/unit-quote-responses', fn() => inertia('UnitQuoteResponses'))->name('unit-quotes-responses');
// Route::get('/unit-contractors', fn() => inertia('Unit/UnitContractors'))->name('unit-contractors');

// ** unit routes **
Route::get('/units-management', fn() => inertia('Units/Index'))->name('unit-management');
Route::get('/units-management/{unitId}/gallery', function ($unitId) {
    return Inertia::render('Units/Gallery', [
        'unitId' => (int) $unitId,
    ]);
})->name('units.gallery');

Route::get('/units-management/{unitId}/docs', function ($unitId) {
    return Inertia::render('Units/Docs', [
        'unitId' => (int) $unitId,
    ]);
})->name('units.docs');

Route::get('/units-management/{unitId}/contractors', function ($unitId) {
    return Inertia::render('Units/UnitContractors', [
        'unitId' => (int) $unitId,
    ]);
})->name('units.docs');

Route::get('/units-management/{unitId}/drawing', function ($unitId) {
    return Inertia::render('Units/Drawings', [
        'unitId' => (int) $unitId,
    ]);
})->name('units.drawing');

Route::get('/units-management/{unitId}/reports', function ($unitId) {
    return Inertia::render('Units/Reports', [
        'unitId' => (int) $unitId,
    ]);
})->name('units.reports');

Route::get('/units-management/{unitId}/timeline', function ($unitId) {
    return Inertia::render('Units/Timeline', [
        'unitId' => (int) $unitId,
    ]);
})->name('units.timeline');

Route::get('/units-management/{unitId}/phases', function ($unitId) {
    return Inertia::render('Units/Phases', [
        'unitId' => (int) $unitId,
    ]);
})->name('units.phases');

Route::get('/units-management/{id}/details', function ($id) {
    return Inertia::render('Units/Details', ['id' => (int) $id]);
});



Route::get('/dashboard', function () {
    return Inertia::render('MainDashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('language')->group(function () {
    Route::get('/editor/{id}/{type}', [LanguageController::class, 'editorPage'])
        ->whereNumber('id')
        ->name('language-editor');

    // Route::get('/languageedit/{id}/{type}', [LanguageController::class, 'editlanguage'])
    //     ->whereNumber('id')
    //     ->name('languages.file.get');

    // Route::post('/updatelanguageValues/{id}/{type}', [LanguageController::class, 'updatelanguageValues'])
    //     ->whereNumber('id')
    //     ->name('languages.file.update');
});


Route::get('/units-management/{unitId}/payments', function ($unitId) {
    return Inertia::render('UnitPaymentsAndInstallments', [
        'unitId' => (int) $unitId,
    ]);
})->name('units.payments');


Route::get('/units-management/{unitId}/payments/{unitPaymentId}/installments', function ($unitId, $unitPaymentId) {
    return Inertia::render('Units/UnitInstallments', [
        'unitId' => (int) $unitId,
        'unitPaymentId' => (int) $unitPaymentId,
    ]);
})->name('units.installments');


// Route::put('installments/{installment}/status', [\App\Http\Controllers\Admin\UnitPaymentInstallmentController::class, 'updateStatus']);
// Route::post('invoices/{invoice}/confirm', [\App\Http\Controllers\Admin\UnitPaymentInstallmentController::class, 'confirmInvoice']);


Route::get('/get-csrf-token', fn() => response()->json([
    'csrf_token' => csrf_token()
]));

require __DIR__ . '/auth.php';
