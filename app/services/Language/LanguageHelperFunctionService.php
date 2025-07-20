<?php

namespace App\services\Language;

use App\Models\Language;
use App\Services\FileService;
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

                    switch ($type) {
                        case 'panel':
                            $fileName = $language->panel_file ?: "{$languageCode}_panel.json";
                            $defaultFile = base_path('resources/lang/en_panel.json');
                            break;
                        case 'app':
                            $fileName = $language->app_file ?: "{$languageCode}_app.json";
                            $defaultFile = base_path('resources/lang/en_app.json');
                            break;
                        default:
                            $fileName = 'en.json';
                            $defaultFile = base_path('resources/lang/en_panel.json');
                            break;
                    }

                    $jsonFile = base_path("resources/lang/{$fileName}");

                    if (!File::exists($jsonFile)) {
                        $defaultContent = (File::exists($defaultFile)) ? File::get($defaultFile) : json_encode([]);

                        File::put($jsonFile, $defaultContent);

                        switch ($type) {
                            case 'panel':
                                $language->panel_file = $fileName;
                                break;
                            case 'app':
                                $language->app_file = $fileName;
                                break;
                        }
                        $language->save();
                    }

                    $jsonContent = File::get($jsonFile);

                    $enContent = File::exists($defaultFile) ? json_decode(File::get($defaultFile), true) : [];
                    $targetContent = File::exists($jsonFile) ? json_decode(File::get($jsonFile), true) : [];

                    foreach ($enContent as $key => $value) {
                        if (!array_key_exists($key, $targetContent)) {
                            $targetContent[$key] = $value;
                        }
                    }
                    File::put($jsonFile, json_encode($targetContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    $enLabels = json_decode($jsonContent, true);

                    $data['enLabels'] = $enLabels;
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
            $jsonFile = match ($type) {
                'panel' => base_path("resources/lang/{$language->panel_file}"),
                'app' => base_path("resources/lang/{$language->app_file}"),
                default => base_path('resources/lang/ar.json'),
            };


            $directory = dirname($jsonFile);

            if (!File::exists($directory)) {

                File::makeDirectory($directory, 0755, true);
            }


            if (!File::exists($jsonFile)) {
                $defaultContent = [];
                File::put($jsonFile, json_encode($defaultContent, JSON_PRETTY_PRINT));
            }
            $jsonContent = File::get($jsonFile);
            $enLabels = json_decode($jsonContent, true);


            $keys = array_keys($enLabels);
            foreach ($keys as $index => $key) {
                if (isset($updatedLabels[$index])) {
                    $enLabels[$key] = $updatedLabels[$index];
                }
            }
            File::put($jsonFile, json_encode($enLabels, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return true;
        });
    }
    public function getLanguageList()
    {
        return Language::getLanguageList();
    }

}
