<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\LanguageRequest;
use App\services\Language\LanguageHelperFunctionService;
use Illuminate\Http\Request;

class LanguageController extends Controller
{

    private LanguageHelperFunctionService $languageService;
    public function __construct(LanguageHelperFunctionService $languageService)
    {
        $this->languageService = $languageService;
    }
    public function store(LanguageRequest $request)
    {
        $this->languageService->createLanguage($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Language created successfully'
        ]);
    }
    public function show($id)
    {
        $data = $this->languageService->getLanguageData($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Language created successfully',
            'data' => $data
        ])->setStatusCode(200);
    }

    public function update(LanguageRequest $request, $id)
    {
        $this->languageService->updateLanguageData($request->validated(), $id);
        return response()->json([
            'status' => 'success',
            'message' => 'Language Updated successfully'
        ]);
    }

    public function setLanguage($languageCode)
    {
        $this->languageService->setLanguage($languageCode);
        return redirect()->back();
    }

    public function editlanguage($id, $type)
    {
        $data = $this->languageService->getLanguageData($id, $type);
        return response()->json([
            'status' => 'success',
            'message' => 'Language created successfully',
            'data' => $data
        ])->setStatusCode(200);
        // return view('settings.languageedit', compact('data'));
    }

    public function updatelanguageValues(Request $request, $id, $type)
    {
        $updatedLabels = $request->input('values');
        $this->languageService->updatelanguage($id, $type, $updatedLabels);
        return response()->json([
            'status' => 'success',
            'message' => 'Language updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $this->languageService->deleteLanguage($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Language Deleted successfully'
        ]);
    }
}
