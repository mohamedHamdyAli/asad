<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $defaultLang = (new Language())->getDefaultLanguage();
        $supportedLangs = (new Language())->getLanguageCode();
        // Check if the 'lang' header is set and is one of the supported languages
        $lang = $request->header('lang');
        if ($lang && in_array($lang, $supportedLangs)) {
            App::setLocale($lang);
            Carbon::setLocale($lang);
        } else {
            // Fall back to the default language
            App::setLocale($defaultLang);
            Carbon::setLocale($defaultLang);
        }

        return $next($request);
    }
}
