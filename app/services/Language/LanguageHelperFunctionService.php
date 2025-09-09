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
    public function createLanguage($request)
    {
        return DB::transaction(function () use ($request) {
            if (isset($request['icon'])) {
                $request['icon'] = FileService::upload($request['icon'], $this->uploadFolder);
            }

            $request['panel_file'] = FileService::generateJsonLanguageFile("{$request['code']}_panel");
            $request['app_file'] = FileService::generateJsonLanguageFile("{$request['code']}_app");

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
                return true;
            }
            return false;
        });
    }

public function getLanguageData($id = null, $type = null)
{
    return DB::transaction(function () use ($id, $type) {
        $data = [];
        if ($id != null) {
            $language = Language::findOrFail($id);
            $data['row'] = $language;

            if ($type != null) {
                $languageCode = $language->code ?? 'en';

                $panelDefault  = base_path('resources/lang/en_panel.json');
                $appDefault    = base_path('resources/lang/en_app.json');

                $panelName = $language->panel_file ?: "{$languageCode}_panel.json";
                $appName   = $language->app_file   ?: "{$languageCode}_app.json";

                $panelPath = base_path("resources/lang/{$panelName}");
                $appPath   = base_path("resources/lang/{$appName}");

                foreach ([[$panelPath,$panelDefault,'panel_file',$panelName], [$appPath,$appDefault,'app_file',$appName]] as [$p,$d,$col,$file]) {
                    if (!File::exists($p)) {
                        $defaultContent = File::exists($d) ? File::get($d) : json_encode([]);
                        File::put($p, $defaultContent);
                        if (empty($language->$col)) {
                            $language->$col = $file;
                        }
                    }
                }
                $language->save();

                if ($type === 'all') {
                    $panelArr = File::exists($panelPath) ? json_decode(File::get($panelPath), true) : [];
                    $appArr   = File::exists($appPath)   ? json_decode(File::get($appPath), true)   : [];

                    $mergedKeys = array_unique(array_merge(array_keys($panelArr), array_keys($appArr)));
                    $merged = [];
                    foreach ($mergedKeys as $k) {
                        $merged[$k] = array_key_exists($k, $appArr) ? $appArr[$k] : ($panelArr[$k] ?? '');
                    }

                    $data['enLabels'] = $merged;
                    $data['type'] = 'all';
                    return $data;
                }

                switch ($type) {
                    case 'panel':
                        $jsonFile   = $panelPath;
                        $defaultFile= $panelDefault;
                        break;
                    case 'app':
                        $jsonFile   = $appPath;
                        $defaultFile= $appDefault;
                        break;
                    default:
                        $jsonFile   = $panelPath;   
                        $defaultFile= $panelDefault;
                        break;
                }

                $defaultArr = File::exists($defaultFile) ? json_decode(File::get($defaultFile), true) : [];
                $targetArr  = File::exists($jsonFile)    ? json_decode(File::get($jsonFile), true)    : [];

                foreach ($defaultArr as $k => $v) {
                    if (!array_key_exists($k, $targetArr)) $targetArr[$k] = $v;
                }
                File::put($jsonFile, json_encode($targetArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                $data['enLabels'] = $targetArr;
                $data['type'] = $type;
            }
        }
        return $data;
    });
}

public function updatelanguage($id, $type, $updatedLabels)
{
    return DB::transaction(function () use ($id, $type, $updatedLabels) {
        $language = Language::findOrFail($id);

        $ensureAssoc = function ($path, $vals) {
            $arr = is_array($vals) ? $vals : [];
            if ($arr && array_values($arr) === $arr) {
                $current = File::exists($path) ? json_decode(File::get($path), true) : [];
                $keys = array_keys($current);
                $mapped = [];
                foreach ($keys as $i => $k) {
                    if (array_key_exists($i, $arr)) $mapped[$k] = $arr[$i];
                }
                return $mapped;
            }
            return $arr;
        };

        $panelPath = base_path("resources/lang/{$language->panel_file}");
        $appPath   = base_path("resources/lang/{$language->app_file}");

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
            $assocPanel = $ensureAssoc($panelPath, $updatedLabels);
            $assocApp   = $ensureAssoc($appPath,   $updatedLabels);
            $applyToFile($panelPath, $assocPanel);
            $applyToFile($appPath,   $assocApp);
            return true;
        }

        $jsonFile = match ($type) {
            'panel' => $panelPath,
            'app'   => $appPath,
            default => $panelPath,
        };
        $assoc = $ensureAssoc($jsonFile, $updatedLabels);
        $applyToFile($jsonFile, $assoc);
        return true;
    });
}

    public function getLanguageList()
    {
        return Language::getLanguageList();
    }

}
