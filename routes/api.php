<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ContactsController;
use App\Http\Middleware\CheckUserAuthentication;
use App\Http\Middleware\CheckUserAndGuestAuthentication;
use App\Http\Controllers\Admin\AdminUnitPaymentController;
use App\Http\Controllers\Admin\AdminUnitPaymentLogController;
use App\Http\Controllers\Admin\UnitPaymentInstallmentController;
use App\Http\Controllers\Api\UnitController as ApiUnitController;
use App\Http\Controllers\Api\IntroController as ApiIntroController;
use App\Http\Controllers\Api\QuoteController as ApiQuoteController;
use App\Http\Controllers\Admin\AdminUnitPaymentInstallmentController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\BannerController as ApiBannerController;
use App\Http\Controllers\Admin\IntroController as AdminIntroController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\VendorController as AdminVendorController;
use App\Http\Controllers\Api\LanguageController as ApiLanguageController;
use App\Http\Controllers\Admin\Unit\DocsController as AdminDocsController;
use App\Http\Controllers\Admin\Unit\UnitController as AdminUnitController;
use App\Http\Controllers\Admin\Unit\PhaseController as AdminPhaseController;
use App\Http\Controllers\Admin\LanguageController as AdminLanguageController;
use App\Http\Controllers\Admin\Unit\FolderController as AdminFolderController;
use App\Http\Controllers\Admin\Unit\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ContactUsController as AdminContactUsController;
use App\Http\Controllers\Admin\UnitQuoteController as AdminUnitQuoteController;
use App\Http\Controllers\Admin\Unit\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\ConsultantController as AdminConsultantController;
use App\Http\Controllers\Admin\ContractorController as AdminContractorController;
use App\Http\Controllers\Admin\Unit\DrawingsController as AdminDrawingsController;
use App\Http\Controllers\Admin\Unit\TimeLineController as AdminTimeLineController;
use App\Http\Controllers\Admin\TypeOfPriceController as AdminTypeOfPriceController;
use App\Http\Controllers\Admin\TypeOfBuildingController as AdminTypeOfBuildingController;
use App\Http\Controllers\Admin\Unit\UnitConsultantController as AdminUnitConsultantController;
use App\Http\Controllers\Admin\Unit\UnitContractorController as AdminUnitContractorController;
use App\Http\Controllers\Admin\Unit\UnitLiveCameraController as AdminUnitLiveCameraController;
use App\Http\Controllers\Admin\UnitQuoteResponseController as AdminUnitQuoteResponseController;






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
        // Unit
        Route::get('get-user-unit', [ApiUnitController::class, 'getUserUnit']);
        Route::get('get-unit-details', [ApiUnitController::class, 'getUnitDetails']);
        Route::get('get-unit-docs', [ApiUnitController::class, 'getUnitDocs']);
        Route::get('get-unit-gallery', [ApiUnitController::class, 'getUnitGallery']);
        Route::get('get-unit-drawing', [ApiUnitController::class, 'getUnitDrawing']);
        Route::get('get-unit-report', [ApiUnitController::class, 'getUnitReport']);
        Route::get('get-unit-phase', [ApiUnitController::class, 'getUnitPhase']);
        Route::post('store-unit-phase-note', [ApiUnitController::class, 'storeUnitPhaseNote']);
        Route::get('get-unit-timeline', [ApiUnitController::class, 'getUnitTimeline']);
        Route::get('get-unit-contractors', [ApiUnitController::class, 'getUnitContractors']);
        Route::get('get-unit-consultants', [ApiUnitController::class, 'getUnitConsultants']);
        Route::post('installments/{installment}/invoices', [PaymentController::class, 'uploadInvoice']);
        Route::get('get-all-installments', [PaymentController::class, 'allInstallments']);
        Route::get('get-all-completed-installments', [PaymentController::class, 'allCompletedInstallments']);
        Route::get('get-active-installments', [PaymentController::class, 'activeInstallments']);

    });

    Route::middleware([CheckUserAndGuestAuthentication::class])->group(function () {
        // Contacts
        Route::get('/contact-infos', [ContactsController::class, 'getByCountry'])->name('contact-infos.byCountry');

        // UnitQuote
        Route::get('get-building-type', [ApiQuoteController::class, 'getBuildingType']);
        Route::get('get-price-type', [ApiQuoteController::class, 'getPriceType']);
        Route::get('get-price-response', [ApiQuoteController::class, 'getPriceResponse']);
        Route::post('quote-request', [ApiQuoteController::class, 'quoteRequest']);
    });
});


Route::group(['prefix' => 'language'], static function () {
    Route::get('/list', [AdminLanguageController::class, 'index'])->name('language.index');
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
    Route::delete('/{id}/cover', [AdminUnitController::class, 'deleteCover']);
    Route::delete('/{unitId}/gallery/{imageId}', [AdminUnitController::class, 'deleteGalleryImage']);
});

Route::group(['prefix' => 'folder'], static function () {
    Route::get('/{type}', [AdminFolderController::class, 'index'])->name('folder.index');
    Route::post('/create', [AdminFolderController::class, 'store'])->name('folder.store');
    Route::post('/update/{id}', [AdminFolderController::class, 'update'])->name('folder.update');
    Route::delete('/delete/{id}', [AdminFolderController::class, 'destroy'])->name('folder.delete');
});

Route::group(['prefix' => 'unit-docs'], static function () {
    Route::get('/{unitId}', [AdminDocsController::class, 'index'])->name('docs.index');
    Route::post('/create', [AdminDocsController::class, 'store'])->name('docs.store');
    Route::post('/update', [AdminDocsController::class, 'update'])->name('docs.update');
    Route::delete('/delete/{id}', [AdminDocsController::class, 'destroy'])->name('docs.delete');
});

Route::group(['prefix' => 'unit-gallery'], static function () {
    Route::get('/{unitId}', [AdminGalleryController::class, 'index'])->name('gallery.index');
    Route::post('/create', [AdminGalleryController::class, 'store'])->name('gallery.store');
    Route::post('/update', [AdminGalleryController::class, 'update'])->name('gallery.update');
    Route::delete('/delete/{id}', [AdminGalleryController::class, 'destroy'])->name('gallery.delete');
});


Route::group(['prefix' => 'unit-drawing'], static function () {
    Route::get('/{unitId}', [AdminDrawingsController::class, 'index'])->name('drawing.index');
    Route::post('/create', [AdminDrawingsController::class, 'store'])->name('drawing.store');
    Route::post('/update', [AdminDrawingsController::class, 'update'])->name('drawing.update');
    Route::delete('/delete/{id}', [AdminDrawingsController::class, 'destroy'])->name('drawing.delete');
});


Route::group(['prefix' => 'unit-report'], static function () {
    Route::get('/{unitId}', [AdminReportController::class, 'index'])->name('report.index');
    Route::post('/create', [AdminReportController::class, 'store'])->name('report.store');
    Route::post('/update', [AdminReportController::class, 'update'])->name('report.update');
    Route::delete('/delete/{id}', [AdminReportController::class, 'destroy'])->name('report.delete');
});



Route::group(['prefix' => 'unit-timeline'], static function () {
    Route::get('/{unitId}', [AdminTimeLineController::class, 'index'])->name('timeline.index');
    Route::post('/create', [AdminTimeLineController::class, 'store'])->name('timeline.store');
    Route::post('/update', [AdminTimeLineController::class, 'update'])->name('timeline.update');
    Route::delete('/delete/{id}', [AdminTimeLineController::class, 'destroy'])->name('timeline.delete');
});

Route::group(['prefix' => 'unit-phase'], static function () {
    Route::get('/{unitId}', [AdminPhaseController::class, 'index'])->name('phases.index');
    Route::post('/create', [AdminPhaseController::class, 'store'])->name('phases.store');
    Route::post('/update', [AdminPhaseController::class, 'update'])->name('phases.update');
    Route::delete('/delete/{id}', [AdminPhaseController::class, 'destroy'])->name('phases.delete');
});

Route::group(['prefix' => 'unit-contractors'], static function () {
    Route::get('/{unitId}', [AdminUnitContractorController::class, 'index'])->name('unit.contractors.index');
    Route::post('/create', [AdminUnitContractorController::class, 'store'])->name('unit.contractors.store');
    Route::post('/update', [AdminUnitContractorController::class, 'update'])->name('unit.contractors.update');
    Route::delete('/delete/{id}', [AdminUnitContractorController::class, 'destroy'])->name('unit.contractors.delete');
});

Route::group(['prefix' => 'unit-live-cameras'], static function () {
    Route::get('/{unitId}', [AdminUnitLiveCameraController::class, 'index'])->name('unit.live_cameras.index');
    Route::post('/create', [AdminUnitLiveCameraController::class, 'store'])->name('unit.live_cameras.store');
    Route::post('/update', [AdminUnitLiveCameraController::class, 'update'])->name('unit.live_cameras.update');
    Route::delete('/delete/{id}', [AdminUnitLiveCameraController::class, 'destroy'])->name('unit.live_cameras.delete');
});

Route::group(['prefix' => 'contractors'], static function () {
    Route::get('/', [AdminContractorController::class, 'index'])->name('contractors.index');
    Route::post('/create', [AdminContractorController::class, 'store'])->name('contractors.store');
    Route::get('/show/{id}', [AdminContractorController::class, 'show'])->name('contractors.show');
    Route::get('/edit/{id}', [AdminContractorController::class, 'edit'])->name('contractors.edit');
    Route::post('/update/{id}', [AdminContractorController::class, 'update'])->name('contractors.update');
    Route::delete('/delete/{id}', [AdminContractorController::class, 'destroy'])->name('contractors.delete');
});

Route::group(['prefix' => 'contact-infos'], static function () {
    Route::get('/', [AdminContactUsController::class, 'index'])->name('contact-infos.index');
    Route::post('/create', [AdminContactUsController::class, 'store'])->name('contact-infos.store');
    Route::get('/show/{id}', [AdminContactUsController::class, 'show'])->name('contact-infos.show');
    Route::get('/edit/{id}', [AdminContactUsController::class, 'edit'])->name('contact-infos.edit');
    Route::post('/update/{id}', [AdminContactUsController::class, 'update'])->name('contact-infos.update');
    Route::delete('/delete/{id}', [AdminContactUsController::class, 'destroy'])->name('contact-infos.delete');
});


Route::group(['prefix' => 'consultants'], static function () {
    Route::get('/', [AdminConsultantController::class, 'index'])->name('consultants.index');
    Route::post('/create', [AdminConsultantController::class, 'store'])->name('consultants.store');
    Route::get('/show/{id}', [AdminConsultantController::class, 'show'])->name('consultants.show');
    Route::get('/edit/{id}', [AdminConsultantController::class, 'edit'])->name('consultants.edit');
    Route::post('/update/{id}', [AdminConsultantController::class, 'update'])->name('consultants.update');
    Route::delete('/delete/{id}', [AdminConsultantController::class, 'destroy'])->name('consultants.delete');
});

Route::group(['prefix' => 'unit-consultants'], static function () {
    Route::get('/{unitId}', [AdminUnitConsultantController::class, 'index'])->name('unit.consultants.index');
    Route::post('/create', [AdminUnitConsultantController::class, 'store'])->name('unit.consultants.store');
    Route::post('/update', [AdminUnitConsultantController::class, 'update'])->name('unit.consultants.update');
    Route::delete('/delete/{id}', [AdminUnitConsultantController::class, 'destroy'])->name('unit.consultants.delete');
});

Route::group(['prefix' => 'type-of-buildings'], static function () {
    Route::get('/', [AdminTypeOfBuildingController::class, 'index'])->name('type-of-buildings.index');
    Route::post('/create', [AdminTypeOfBuildingController::class, 'store'])->name('type-of-buildings.store');
    Route::get('/show/{id}', [AdminTypeOfBuildingController::class, 'show'])->name('type-of-buildings.show');
    Route::get('/edit/{id}', [AdminTypeOfBuildingController::class, 'edit'])->name('type-of-buildings.edit');
    Route::post('/update/{id}', [AdminTypeOfBuildingController::class, 'update'])->name('type-of-buildings.update');
    Route::delete('/delete/{id}', [AdminTypeOfBuildingController::class, 'destroy'])->name('type-of-buildings.delete');
});



Route::group(['prefix' => 'type-of-prices'], static function () {
    Route::get('/', [AdminTypeOfPriceController::class, 'index'])->name('type-of-prices.index');
    Route::post('/create', [AdminTypeOfPriceController::class, 'store'])->name('type-of-prices.store');
    Route::get('/show/{id}', [AdminTypeOfPriceController::class, 'show'])->name('type-of-prices.show');
    Route::get('/edit/{id}', [AdminTypeOfPriceController::class, 'edit'])->name('type-of-prices.edit');
    Route::post('/update/{id}', [AdminTypeOfPriceController::class, 'update'])->name('type-of-prices.update');
    Route::delete('/delete/{id}', [AdminTypeOfPriceController::class, 'destroy'])->name('type-of-prices.delete');
});


Route::prefix('unit-quotes')->group(function () {
    Route::get('/', [AdminUnitQuoteController::class, 'index'])->name('unit-quotes.index');
    Route::post('/create', [AdminUnitQuoteController::class, 'store'])->name('unit-quotes.store');
    Route::get('/show/{id}', [AdminUnitQuoteController::class, 'show'])->name('unit-quotes.show');
    Route::post('/update/{id}', [AdminUnitQuoteController::class, 'update'])->name('unit-quotes.update');
    Route::delete('/delete/{id}', [AdminUnitQuoteController::class, 'destroy'])->name('unit-quotes.delete');
});


Route::prefix('unit-quote-responses')->group(function () {
    Route::get('/', [AdminUnitQuoteResponseController::class, 'index'])->name('unit-quote-responses.index');
    Route::post('/create', [AdminUnitQuoteResponseController::class, 'store'])->name('unit-quote-responses.store');
    Route::get('/show/{id}', [AdminUnitQuoteResponseController::class, 'show'])->name('unit-quote-responses.show');
    Route::post('/update/{id}', [AdminUnitQuoteResponseController::class, 'update'])->name('unit-quote-responses.update');
    Route::delete('/delete/{id}', [AdminUnitQuoteResponseController::class, 'destroy'])->name('unit-quote-responses.delete');
});



Route::prefix('unit-payments')->group(function () {
    // Unit Payments
    Route::get('/{unitId}', [AdminUnitPaymentController::class, 'index'])->name('unit-payments.index');
    Route::post('/create', [AdminUnitPaymentController::class, 'store'])->name('unit-payments.store');
    Route::post('/update/{id}', [AdminUnitPaymentController::class, 'update'])->name('unit-payments.update');
    Route::delete('/delete/{id}', [AdminUnitPaymentController::class, 'destroy'])->name('unit-payments.delete');

    // Installments
    Route::post('installments/create', [AdminUnitPaymentInstallmentController::class, 'store'])
        ->name('unit-payment-installments.store');

    Route::post('installments/update/{id}', [AdminUnitPaymentInstallmentController::class, 'update'])
        ->name('unit-payment-installments.update');


    Route::delete('installments/delete/{id}', [AdminUnitPaymentInstallmentController::class, 'destroy'])
        ->name('unit-payment-installments.delete');

    Route::get('installments/show/{unitPaymentId}', [AdminUnitPaymentInstallmentController::class, 'show'])
        ->name('unit-payment-installments.show');


    // Invoices
    Route::get('installments/{installment}/invoices', [UnitPaymentInstallmentController::class, 'getInvoices'])
        ->name('unit-payment-installments.invoices');

    Route::post('installments/{installment}/status', [UnitPaymentInstallmentController::class, 'updateStatus'])
        ->name('unit-payment-installments.status');

    Route::post('invoices/confirm', [UnitPaymentInstallmentController::class, 'confirmInvoice'])
        ->name('unit-payment-installments.confirm-invoice');


    // Logs
    Route::get('/logs/{unitId}', [AdminUnitPaymentLogController::class, 'getUnitLogs'])
        ->name('unit-payment.logs.unit');

    Route::get('/installment/{installmentId}/logs', [AdminUnitPaymentLogController::class, 'getInstallmentLogs'])
        ->name('unit-payment.logs.installment');
});
