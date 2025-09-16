<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TypeOfPriceRequest;
use App\services\TypeOfPrice\TypeOfPriceService;

class TypeOfPriceController extends Controller
{
    private TypeOfPriceService $typeOfPriceService;

    public function __construct(TypeOfPriceService $typeOfPriceService)
    {
        $this->typeOfPriceService = $typeOfPriceService;
    }

    public function index()
    {
        $data = $this->typeOfPriceService->getTypeOfPriceData();
        return response()->json([
            'status' => 'success',
            'message' => 'Type of prices fetched successfully',
            'data' => $data
        ], 200);
    }

    public function store(TypeOfPriceRequest $request)
    {
        $this->typeOfPriceService->createTypeOfPrice($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Type of price created successfully',
        ], 201);
    }

    public function show($id)
    {
        $data = $this->typeOfPriceService->getTypeOfPriceData($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Type of price not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Type of price fetched successfully',
            'data' => $data
        ], 200);
    }

    public function edit($id)
    {
        return $this->show($id);
    }

    public function update(TypeOfPriceRequest $request, $id)
    {
        $updated = $this->typeOfPriceService->updateTypeOfPriceData($request->validated(), (int) $id);
        if (!$updated) {
            return response()->json([
                'status' => 'error',
                'message' => 'Type of price not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Type of price updated successfully'
        ], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->typeOfPriceService->deleteTypeOfPrice((int) $id);
        if (!$deleted) {
            return response()->json([
                'status' => 'error',
                'message' => 'Type of price not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Type of price deleted successfully'
        ], 200);
    }
}
