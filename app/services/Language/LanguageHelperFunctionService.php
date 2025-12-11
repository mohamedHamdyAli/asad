<?php

namespace App\services\Language;

use App\Models\Language;
use App\services\FileService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LanguageHelperFunctionService
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "language";
    }
    // public function createLanguage($request)
    // {
    //     return DB::transaction(function () use ($request) {
    //         if (isset($request['icon'])) {
    //             $request['icon'] = FileService::upload($request['icon'], $this->uploadFolder);
    //         }
    //         $request['panel_file'] = FileService::generateJsonLanguageFile("{$request['code']}_panel", 'adminDashboardFile');
    //         $request['app_file'] = FileService::generateJsonLanguageFile("{$request['code']}_app", 'userAppFile');
    //         $request['vendor_file'] = FileService::generateJsonLanguageFile("{$request['code']}_vendor", 'vendorDashboardFile');
    //         $request['web_file'] = FileService::generateJsonLanguageFile("{$request['code']}_web", 'webFile');

    //         Language::create($request);
    //         return true;
    //     });
    // }

    public function createLanguage($request)
    {
        return DB::transaction(function () use ($request) {

            if (!empty($request['icon'])) {
                $request['icon'] = FileService::upload($request['icon'], $this->uploadFolder);
            }

            $files = FileService::generateJsonLanguageFile($request['code']);
            $request = array_merge($request, $files);
            Language::create($request);
            return true;
        });
    }

    public function updateLanguageData($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $language = Language::findOrFail($id);
            if (isset($request['icon'])) {
                $request['icon'] = FileService::replace($request['icon'], $this->uploadFolder, $language->getRawOriginal('icon'));
            }
            $language->update($request);
            return true;
        });
    }
    public function deleteLanguage($id)
    {
        return DB::transaction(function () use ($id) {
            $language = Language::findOrFail($id);
            $language->delete();
            FileService::deleteLanguageFile($language->app_file);
            FileService::deleteLanguageFile($language->panel_file);
            FileService::deleteLanguageFile($language->vendor_file);
            FileService::deleteLanguageFile($language->web_file);
            FileService::delete($language->getRawOriginal('icon'));
            return true;
        });
    }
    public function setLanguage($languageCode)
    {
        return DB::transaction(function () use ($languageCode) {
            $language = Language::where('code', $languageCode)->first();
            if (!empty($language)) {
                session(['locale' => $language->code]);
                session(['locale_name' => $language->name]);
                session(['locale_flag' => $language->icon]);
                return true;
            }
            return false;
        });
    }

    // public function getLanguageData($id = null, $type = null)
    // {
    //     return DB::transaction(function () use ($id, $type) {
    //         $data = [];
    //         if ($id != null) {
    //             $language = Language::findOrFail($id);
    //             $data['row'] = $language;

    //             if ($type != null) {
    //                 $languageCode = $language->code ?? 'en';

    //                 // Default paths
    //                 $defaults = [
    //                     'panel' => base_path('resources/lang/en_panel.json'),
    //                     'app' => base_path('resources/lang/en_app.json'),
    //                     'vendor' => base_path('resources/lang/en_vendor.json'),
    //                     'web' => base_path('resources/lang/en_web.json'),
    //                 ];

    //                 // Target file names (custom or default)
    //                 $files = [
    //                     'panel' => $language->panel_file ?: "{$languageCode}_panel.json",
    //                     'app' => $language->app_file ?: "{$languageCode}_app.json",
    //                     'vendor' => $language->vendor_file ?: "{$languageCode}_vendor.json",
    //                     'web' => $language->web_file ?: "{$languageCode}_web.json",
    //                 ];

    //                 // Target paths
    //                 $paths = [];
    //                 foreach ($files as $key => $file) {
    //                     $paths[$key] = base_path("resources/lang/{$file}");
    //                 }

    //                 foreach ($defaults as $key => $defaultPath) {
    //                     $targetPath = $paths[$key];
    //                     if (!File::exists($targetPath)) {
    //                         $defaultContent = File::exists($defaultPath) ? File::get($defaultPath) : json_encode([]);
    //                         File::put($targetPath, $defaultContent);
    //                         $column = "{$key}_file";
    //                         if (empty($language->$column)) {
    //                             $language->$column = $files[$key];
    //                         }
    //                     }
    //                 }
    //                 $language->save();

    //                 if ($type === 'all') {
    //                     $allArrays = [];
    //                     foreach ($paths as $key => $path) {
    //                         $allArrays[$key] = File::exists($path) ? json_decode(File::get($path), true) : [];
    //                     }

    //                     $mergedKeys = array_unique(array_merge(...array_map('array_keys', $allArrays)));
    //                     $merged = [];
    //                     foreach ($mergedKeys as $k) {
    //                         $merged[$k] =
    //                             $allArrays['app'][$k] ??
    //                             $allArrays['panel'][$k] ??
    //                             $allArrays['vendor'][$k] ??
    //                             $allArrays['web'][$k] ??
    //                             '';
    //                     }

    //                     $data['enLabels'] = $merged;
    //                     $data['type'] = 'all';
    //                     return $data;
    //                 }

    //                 $defaultFile = $defaults[$type] ?? $defaults['panel'];
    //                 $jsonFile = $paths[$type] ?? $paths['panel'];

    //                 $defaultArr = File::exists($defaultFile) ? json_decode(File::get($defaultFile), true) : [];
    //                 $targetArr = File::exists($jsonFile) ? json_decode(File::get($jsonFile), true) : [];

    //                 foreach ($defaultArr as $k => $v) {
    //                     if (!array_key_exists($k, $targetArr))
    //                         $targetArr[$k] = $v;
    //                 }

    //                 File::put($jsonFile, json_encode($targetArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    //                 $data['enLabels'] = $targetArr;
    //                 $data['type'] = $type;
    //             }
    //         }
    //         return $data;
    //     });
    // }

    public function getLanguageData($id = null, $type = null)
    {
        $data = [];
        if (!$id) return $data;

        $language = Language::findOrFail($id);
        $data['row'] = $language;

        if (!$type) return $data;

        $languageCode = $language->code ?? 'en';

        $defaults = [
            'panel'  => base_path('resources/lang/en_panel.json'),
            'app'    => base_path('resources/lang/en_app.json'),
            'vendor' => base_path('resources/lang/en_vendor.json'),
            'web'    => base_path('resources/lang/en_web.json'),
        ];

        $files = [
            'panel'  => $language->panel_file ?: "{$languageCode}_panel.json",
            'app'    => $language->app_file ?: "{$languageCode}_app.json",
            'vendor' => $language->vendor_file ?: "{$languageCode}_vendor.json",
            'web'    => $language->web_file ?: "{$languageCode}_web.json",
        ];

        $paths = [];
        foreach ($files as $key => $file) {
            $paths[$key] = base_path("resources/lang/{$file}");
        }

        // Ensure files exist ONLY (write once if missing)
        foreach ($defaults as $key => $defaultPath) {
            $targetPath = $paths[$key];
            if (!File::exists($targetPath)) {
                $defaultContent = File::exists($defaultPath) ? File::get($defaultPath) : json_encode([]);
                File::put($targetPath, $defaultContent);
                // DO NOT $language->save() here — GET should be read-only
            }
        }

        if ($type === 'all') {
            $all = [];
            foreach ($paths as $key => $path) {
                $all[$key] = File::exists($path) ? json_decode(File::get($path), true) : [];
            }
            $mergedKeys = array_unique(array_merge(...array_map('array_keys', $all)));
            $merged = [];
            foreach ($mergedKeys as $k) {
                $merged[$k] = $all['app'][$k] ?? $all['panel'][$k] ?? $all['vendor'][$k] ?? $all['web'][$k] ?? '';
            }
            $data['enLabels'] = $merged;
            $data['type'] = 'all';
            return $data;
        }

        $defaultFile = $defaults[$type] ?? $defaults['panel'];
        $jsonFile    = $paths[$type] ?? $paths['panel'];

        $defaultArr = File::exists($defaultFile) ? json_decode(File::get($defaultFile), true) : [];
        $targetArr  = File::exists($jsonFile) ? json_decode(File::get($jsonFile), true) : [];

        // Build the response IN MEMORY. DO NOT write on GET.
        foreach ($defaultArr as $k => $v) {
            if (!array_key_exists($k, $targetArr)) {
                $targetArr[$k] = $v;
            }
        }

        $data['enLabels'] = $targetArr;
        $data['type'] = $type;
        return $data;
    }


    // public function updatelanguage($id, $type, $updatedLabels)
    // {
    //     return DB::transaction(function () use ($id, $type, $updatedLabels) {
    //         $language = Language::findOrFail($id);

    //         $ensureAssoc = function ($path, $vals) {
    //             $arr = is_array($vals) ? $vals : [];
    //             if ($arr && array_values($arr) === $arr) {
    //                 $current = File::exists($path) ? json_decode(File::get($path), true) : [];
    //                 $keys = array_keys($current);
    //                 $mapped = [];
    //                 foreach ($keys as $i => $k) {
    //                     if (array_key_exists($i, $arr))
    //                         $mapped[$k] = $arr[$i];
    //                 }
    //                 return $mapped;
    //             }
    //             return $arr;
    //         };

    //         $paths = [
    //             'panel' => base_path("resources/lang/{$language->panel_file}"),
    //             'app' => base_path("resources/lang/{$language->app_file}"),
    //             'vendor' => base_path("resources/lang/{$language->vendor_file}"),
    //             'web' => base_path("resources/lang/{$language->web_file}"),
    //         ];

    //         $applyToFile = function ($path, $vals) {
    //             $existing = File::exists($path) ? json_decode(File::get($path), true) : [];
    //             foreach ($vals as $k => $v) {
    //                 if (array_key_exists($k, $existing)) {
    //                     $existing[$k] = $v;
    //                 }
    //             }
    //             File::put($path, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    //         };

    //         if ($type === 'all') {
    //             foreach ($paths as $path) {
    //                 $assoc = $ensureAssoc($path, $updatedLabels);
    //                 $applyToFile($path, $assoc);
    //             }
    //             return true;
    //         }

    //         if (!array_key_exists($type, $paths)) {
    //             throw new \Exception("Invalid type: {$type}");
    //         }

    //         $assoc = $ensureAssoc($paths[$type], $updatedLabels);
    //         $applyToFile($paths[$type], $assoc);
    //         return true;
    //     });
    // }

    public function updatelanguage($id, $type, $updatedLabels)
    {
        return DB::transaction(function () use ($id, $type, $updatedLabels) {
            $language = Language::findOrFail($id);

            $paths = [
                'panel' => base_path("resources/lang/{$language->panel_file}"),
                'app' => base_path("resources/lang/{$language->app_file}"),
                'vendor' => base_path("resources/lang/{$language->vendor_file}"),
                'web' => base_path("resources/lang/{$language->web_file}"),
            ];

            $mainPath = base_path("resources/lang/{$language->code}.json");

            $ensureAssoc = function ($path, $vals) {
                $arr = is_array($vals) ? $vals : [];
                if ($arr && array_values($arr) === $arr) {
                    $current = File::exists($path) ? json_decode(File::get($path), true) : [];
                    $keys = array_keys($current);
                    $mapped = [];
                    foreach ($keys as $i => $k) {
                        if (array_key_exists($i, $arr))
                            $mapped[$k] = $arr[$i];
                    }
                    return $mapped;
                }
                return $arr;
            };

            $applyToFile = function ($path, $vals) {
                $existing = File::exists($path) ? json_decode(File::get($path), true) : [];
                foreach ($vals as $k => $v) {
                    if (array_key_exists($k, $existing)) {
                        $existing[$k] = $v;
                    }
                }
                File::put($path, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            };

            if ($type === 'all') {
                foreach ($paths as $path) {
                    $assoc = $ensureAssoc($path, $updatedLabels);
                    $applyToFile($path, $assoc);
                }
            } else {
                if (!array_key_exists($type, $paths)) {
                    throw new \Exception("Invalid type: {$type}");
                }
                $assoc = $ensureAssoc($paths[$type], $updatedLabels);
                $applyToFile($paths[$type], $assoc);
            }

            $allLabels = [];
            foreach ($paths as $p) {
                $data = File::exists($p) ? json_decode(File::get($p), true) : [];
                $allLabels = array_merge($allLabels, $data);
            }
            File::put($mainPath, json_encode($allLabels, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return true;
        });
    }



    public function getLanguageList()
    {
        return Language::getLanguageList();
    }
}
