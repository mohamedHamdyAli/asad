<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\LanguageRequest;
use App\services\Language\LanguageHelperFunctionService;
use Illuminate\Http\Request;
use App\Models\Language;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class LanguageController extends Controller
{

    private LanguageHelperFunctionService $languageService;
    public function __construct(LanguageHelperFunctionService $languageService)
    {
        $this->languageService = $languageService;
    }
    public function index(Request $request)
    {
        $scope = $request->query('scope');

        $q = Language::query();
        if ($scope !== null && $scope !== '') {
            $q->where('app_scope', $scope);
        }

        $rows = $q->orderByDesc('id')->get()->map(function ($l) {
            return [
                'id'            => $l->id,
                'name'          => $l->name,
                'english_name'  => $l->name_en,
                'code'          => $l->code,
                'country_code'  => $l->country_code,
                'scope'         => $l->app_scope,
                'rtl'           => (bool) $l->is_rtl,
                'icon_url'      => $l->getRawOriginal('icon') ? Storage::url($l->getRawOriginal('icon')) : null,
            ];
        });

        return response()->json(['status' => 'success', 'data' => $rows]);
    }

    // App\Http\Controllers\Admin\LanguageController.php

public function one($id)
{
    $l = \App\Models\Language::findOrFail($id);

    $row = [
        'id'           => $l->id,
        'name'         => $l->name,
        'english_name' => $l->name_en,
        'code'         => $l->code,
        'country_code' => $l->country_code,
        'scope'        => $l->app_scope,
        'rtl'          => (bool) $l->is_rtl,
        'icon_url'     => $l->getRawOriginal('icon') ? Storage::url($l->getRawOriginal('icon')) : null,
    ];

    return response()->json([
        'status'  => 'success',
        'message' => 'Language fetched successfully',
        'data'    => $row,
    ], 200);
}


    public function store(LanguageRequest $request)
    {
        $this->languageService->createLanguage($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Language created successfully'
        ]);
    }
    public function show($id, $type)
    {
        $data = $this->languageService->getLanguageData($id, $type);
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
    public function editLanguage($id, $type)
    {
        $data = $this->languageService->getLanguageData($id, $type);
        return response()->json([
            'status' => 'success',
            'message' => 'Language fetched successfully',
            'data' => $data
        ], 200);
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

    public function editorPage($id, $type)
    {
        return Inertia::render('LanguageEditor', [
            'id'   => (int) $id,
            'type' => (string) $type,
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
