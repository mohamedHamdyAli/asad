<?php

namespace App\Http\Controllers\Admin;

use App\Models\Contractor;
use Illuminate\Http\Request;
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
            'message' => 'Contractors Data Fetched successfully',
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
        ]);
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
            'message' => 'Contractor data fetched successfully',
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

    public function update(ContractorRequest $request)
    {
        $data = $this->contractorService->updateContractorData($request->validated(), $request->id);
        return response()->json([
            'status' => 'success',
            'message' => 'Contractor updated successfully',
            'data' => $data
        ], 200);
    }


    public function destroy($id)
    {
        $data = $this->contractorService->deleteContractor($id);
        if (!$data) {
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
