<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VendorRequest;
use App\Services\Vendor\VendorHelperFunctionService;

class VendorController extends Controller
{
    private VendorHelperFunctionService $vendorService;

    public function __construct(VendorHelperFunctionService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    public function index()
    {
        $data = $this->vendorService->getVendorData();
        return response()->json([
            'status' => 'success',
            'message' => 'Vendors Data Fetched successfully',
            'data' => $data
        ], 200);
    }

    public function store(VendorRequest $request)
    {
        $data = $this->vendorService->createVendor($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Vendor created successfully',
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $data = $this->vendorService->getVendorData($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vendor not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Vendor data fetched successfully',
            'data' => $data
        ], 200);
    }

    public function edit($id)
    {
        $data = $this->vendorService->getVendorData($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vendor not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Vendor data for editing fetched successfully',
            'data' => $data
        ], 200);
    }

    public function update(VendorRequest $request, $id)
    {
        $data = $this->vendorService->updateVendorData($request->validated(), $id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vendor not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Vendor updated successfully',
            'data' => $data
        ], 200);
    }

    public function destroy($id)
    {
        $data = $this->vendorService->deleteVendor($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vendor not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Vendor deleted successfully'
        ], 200);
    }
}
