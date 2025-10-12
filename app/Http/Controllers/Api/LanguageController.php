<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LanguageRequest;
use App\Http\Resources\LanguageResource;
use App\services\Language\LanguageHelperFunctionService;
use Throwable;

class LanguageController extends Controller
{
    private LanguageHelperFunctionService $languageService;
    public function __construct(LanguageHelperFunctionService $languageService)
    {
        $this->languageService = $languageService;
    }
    public function getLanguages()
    {
        $data = $this->languageService->getLanguageList();
        return successReturnData( LanguageResource::collection($data),  'Data Fetched Successfully');
    }
    public function getLanguageLabels(LanguageRequest $request)
    {
        $data = $this->languageService->getLanguageData($request['lang_id'],  $request['type']);
        return successReturnData($data['enLabels'],  'Data Fetched Successfully');
    }
}
