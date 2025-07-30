<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\services\Intro\UnitCrudService;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    private UnitCrudService $unitService;
    public function __construct(UnitCrudService $unitService)
    {
        $this->unitService = $unitService;
    }
    // public function index()
    // {
    //     $data = $this->introService->getIntroData();
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Intro Data Featch successfully',
    //         'data' => $data
    //     ])->setStatusCode(200);
    // }

    // public function store(IntroRequest $request)
    // {
    //     $data = $this->introService->createIntro($request->validated());
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Intro created successfully',
    //         'data' => $data

    //     ]);
    // }
    // public function show($id)
    // {
    //     $data = $this->introService->getIntroData($id);
    //     if (!$data) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Intro not found'
    //         ], 404);
    //     }
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Intro show successfully',
    //         'data' => $data
    //     ])->setStatusCode(200);
    // }
    // public function edit($id)
    // {
    //     $data = $this->introService->getIntroData($id);
    //     if (!$data) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Intro not found'
    //         ], 404);
    //     }
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Intro edit successfully',
    //         'data' => $data
    //     ])->setStatusCode(200);
    // }

    // public function update(IntroRequest $request, $id)
    // {
    //     $intro = $this->introService->updateIntroData($request->validated(), $id);
    //     if (!$intro) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Intro not found'
    //         ], 404);
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Intro Updated successfully'
    //     ]);
    // }
    // public function destroy($id)
    // {
    //     $data = $this->introService->deleteIntro($id);
    //     if (!$data) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Intro not found'
    //         ], 404);
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Intro Deleted successfully'
    //     ]);
    // }
}
