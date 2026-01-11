<?php

use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
/*
|--------------------------------------------------------------------------
| PUBLIC PAGES
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('home.home'));
Route::get('/about', fn() => view('about.about'));
Route::get('/contact', fn() => view('contact.contact'));
Route::get('/project', fn() => view('portfolio.portfolio'));
Route::get('/project-details/{id}', fn($id) => view('portfolio.project-details.project-details', ['id' => $id]));
Route::get('/our-services', fn() => view('services.services'));
Route::get('/qhse-policy', fn() => view('qhse_policy.qhsePolicy'));
Route::get('lang/{locale}', [LanguageController::class, 'setLanguage']);
/*
|--------------------------------------------------------------------------
| AUTH PAGES (guest only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => inertia('Auth/Login'))->name('login');
    Route::get('/register', fn () => inertia('Auth/Register'))->name('register');
});

/*
|--------------------------------------------------------------------------
| ALL APPLICATION PAGES (AUTH REQUIRED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    /* ================= Dashboard ================= */
    Route::get('/dashboard', fn () =>
        Inertia::render('MainDashboard')
    )->name('dashboard');

    /* ================= Profile ================= */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /* ================= Settings & Messages ================= */
    Route::middleware('role_or_permission:admin|user')->group(function () {
        Route::get('/settings', fn () => inertia('Settings'))->name('settings');
        Route::get('/messages', fn () => inertia('Messages'))->name('messages');
    });

    /* ================= ADMIN ONLY ================= */
    Route::middleware('role:admin')->group(function () {

        Route::get('/users-management', fn () => inertia('UsersManagement'))->name('users-management');
        Route::get('/roles-management', fn () => inertia('RolesManagement'))->name('roles-management');
        Route::get('/languages', fn () => inertia('LanguageManagement'))->name('language-management');
        Route::get('/finance-management', fn () => inertia('FinancialReports'))->name('finance-management');
        Route::get('/intro-management', fn () => inertia('IntroManagement'))->name('intro-management');
        Route::get('/banner-management', fn () => inertia('BannerManagement'))->name('banner-management');
        Route::get('/notifications-management', fn () => inertia('Notifications/Index'))->name('notifications-management');
        Route::get('/contactus-management', fn () => inertia('ContactUsPage'))->name('contactus-management');
        Route::get('/unit-quote-types', fn () => inertia('QuoteTypesPage'))->name('unit-quote-types');
    });

    /* ================= ADMIN + VENDOR ================= */
    Route::middleware('role_or_permission:admin|vendor')->group(function () {

        Route::get('/pm-management', fn () => inertia('VendorsManagement'))->name('pm-management');
        Route::get('/bids-management', fn () => inertia('BidsManagement'))->name('bids-management');
        Route::get('/contractors-management', fn () => inertia('Contractors/Index'))->name('contractors-management');
        Route::get('/Consultants-management', fn () => inertia('Consultants/Index'))->name('Consultants-management');
        Route::get('/unit-quotes', fn () => inertia('UnitQuotes'))->name('unit-quotes');
        Route::get('/unit-quote-responses', fn () => inertia('UnitQuoteResponses'))->name('unit-quotes-responses');
        Route::get('/unit-issues', fn () => inertia('UnitIssues'))->name('unit-issues');
    });

    /* ================= PROJECTS / UNITS ================= */
    Route::middleware('role_or_permission:admin|vendor')->group(function () {

        Route::get('/projects-management', fn () =>
            inertia('Units/Index')
        )->name('unit-management');

        Route::prefix('/projects-management/{unitId}')->group(function () {

            Route::get('/gallery', fn ($unitId) =>
                Inertia::render('Units/Gallery', ['unitId' => (int) $unitId])
            )->name('units.gallery');

            Route::get('/docs', fn ($unitId) =>
                Inertia::render('Units/Docs', ['unitId' => (int) $unitId])
            )->name('units.docs');

            Route::get('/contractors', fn ($unitId) =>
                Inertia::render('Units/UnitContractors', ['unitId' => (int) $unitId])
            );

            Route::get('/drawing', fn ($unitId) =>
                Inertia::render('Units/Drawings', ['unitId' => (int) $unitId])
            )->name('units.drawing');

            Route::get('/reports', fn ($unitId) =>
                Inertia::render('Units/Reports', ['unitId' => (int) $unitId])
            )->name('units.reports');

            Route::get('/timeline', fn ($unitId) =>
                Inertia::render('Units/Timeline', ['unitId' => (int) $unitId])
            )->name('units.timeline');

            Route::get('/phases', fn ($unitId) =>
                Inertia::render('Units/Phases', ['unitId' => (int) $unitId])
            )->name('units.phases');

            Route::get('/payments', fn ($unitId) =>
                Inertia::render('UnitPaymentsAndInstallments', ['unitId' => (int) $unitId])
            )->name('units.payments');

            Route::get('/payments/{unitPaymentId}/installments', fn ($unitId, $unitPaymentId) =>
                Inertia::render('Units/UnitInstallments', [
                    'unitId' => (int) $unitId,
                    'unitPaymentId' => (int) $unitPaymentId,
                ])
            )->name('units.installments');
        });

        Route::get('/projects-management/{id}/details', fn ($id) =>
            Inertia::render('Units/Details', ['id' => (int) $id])
        );
    });

    /* ================= LANGUAGE EDITOR ================= */
    Route::middleware('role:admin')->prefix('language')->group(function () {
        Route::get('/editor/{id}/{type}', [LanguageController::class, 'editorPage'])
            ->whereNumber('id')
            ->name('language-editor');
    });

});

/*
|--------------------------------------------------------------------------
| CSRF HELPER
|--------------------------------------------------------------------------
*/
Route::get('/get-csrf-token', fn () => response()->json([
    'csrf_token' => csrf_token()
]));

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
