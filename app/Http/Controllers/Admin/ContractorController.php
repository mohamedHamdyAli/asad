<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContractorRequest;
use App\services\Contractor\ContractorService;

class ContractorController extends Controller
{
    private ContractorService $contractorService;

    public function __construct(ContractorService $contractorService)
    {
        $this->contractorService = $contractorService;
    }

    public function index()
    {
        $data = $this->contractorService->getContractorData();
        return response()->json([
            'status' => 'success',
            'message' => 'Contractors fetched successfully',
            'data' => $data
        ], 200);
    }

    public function store(ContractorRequest $request)
    {
        $data = $this->contractorService->createContractor($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Contractor created successfully',
            'data' => $data
        ], 201);
    }

    public function show($id)
    {
        $data = $this->contractorService->getContractorData($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contractor not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Contractor fetched successfully',
            'data' => $data
        ], 200);
    }

    public function edit($id)
    {
        $data = $this->contractorService->getContractorData($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contractor not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Contractor data for editing fetched successfully',
            'data' => $data
        ], 200);
    }

    public function update(ContractorRequest $request, $id)
    {
        $updated = $this->contractorService->updateContractorData($request->validated(), (int)$id);
        if (!$updated) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contractor not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Contractor updated successfully'
        ], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->contractorService->deleteContractor((int)$id);
        if (!$deleted) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contractor not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Contractor deleted successfully'
        ], 200);
    }
}
