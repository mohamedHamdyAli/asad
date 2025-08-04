<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Schema::defaultStringLength(191);
        Model::preventLazyLoading(!app()->isProduction());
        // Model::preventSilentlyDiscardingAttributes(true);

        // app()->bind(\Illuminate\Contracts\Debug\ExceptionHandler::class, fn($app) => new class ($app) extends \Illuminate\Foundation\Exceptions\Handler {
        //     public function render($request, \Throwable $e)
        //         {
        //             logger()->error($e->getMessage(), [
        //                 'exception' => get_class($e),
        //                 'file' => $e->getFile(),
        //                 'line' => $e->getLine(),
        //                 'url' => $request->fullUrl(),
        //                 'method' => $request->method(),
        //             ]);

        //             if ($e instanceof MethodNotAllowedHttpException) {
        //                 return failReturnMsg('The method used is not supported for this path.', 405);
        //             }

        //             return failServerReturnMsg(
        //                 'Unexpected server error.',
        //                 app()->isLocal()
        //                     ? [
        //                         'message' => $e->getMessage(),
        //                         'exception' => get_class($e),
        //                         'file' => $e->getFile(),
        //                         'line' => $e->getLine(),
        //                         'trace' => $e->getTrace(),
        //                         'request' => $request->all(),
        //                     ]
        //                     : null,
        //                 500
        //             );
        //         }
        //     });
        // ;
    }
}
