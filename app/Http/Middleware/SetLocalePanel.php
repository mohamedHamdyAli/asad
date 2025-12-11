<?php

namespace App\Http\Middleware;
use App\Models\Language;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;

class SetLocalePanel
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $defaultLanguage = (new Language())->getDefaultLanguage();

        $currentLanguage = Session::get('locale', $defaultLanguage);

        App::setLocale($currentLanguage);

        $file = $request->is('admin/*') ? 'panel' : 'web';
        $path = resource_path("lang/{$currentLanguage}_{$file}.json");

        if (File::exists($path)) {
            $translations = json_decode(File::get($path), true);
            app('translator')->setLoaded([$currentLanguage => $translations]);
        }

        return $next($request);
    }
}
