<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConsultantRequest;
use App\services\Consultant\ConsultantService;

class ConsultantController extends Controller
{
       private ConsultantService $consultantService;

    public function __construct(ConsultantService $consultantService)
    {
        $this->consultantService = $consultantService;
    }

    public function index()
    {
        $data = $this->consultantService->getConsultantData();
        return response()->json([
            'status' => 'success',
            'message' => 'Consultants fetched successfully',
            'data' => $data
        ], 200);
    }

    public function store(ConsultantRequest $request)
    {
        $data = $this->consultantService->createConsultant($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Consultant created successfully',
            'data' => $data
        ], 201);
    }

    public function show($id)
    {
        $data = $this->consultantService->getConsultantData($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Consultant not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Consultant fetched successfully',
            'data' => $data
        ], 200);
    }

    public function edit($id)
    {
        $data = $this->consultantService->getConsultantData($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Consultant not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Consultant data for editing fetched successfully',
            'data' => $data
        ], 200);
    }

    public function update(ConsultantRequest $request, $id)
    {
        $updated = $this->consultantService->updateConsultantData($request->validated(), (int)$id);
        if (!$updated) {
            return response()->json([
                'status' => 'error',
                'message' => 'Consultant not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Consultant updated successfully'
        ], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->consultantService->deleteConsultant((int)$id);
        if (!$deleted) {
            return response()->json([
                'status' => 'error',
                'message' => 'Consultant not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Consultant deleted successfully'
        ], 200);
    }
}
