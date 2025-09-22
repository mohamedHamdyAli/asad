<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UnitQuoteResponseRequest;
use App\services\UnitQuoteResponse\UnitQuoteResponseService;


class UnitQuoteResponseController extends Controller
{
    protected $service;

    public function __construct(UnitQuoteResponseService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->service->getAll()
        ]);
    }

    public function show($id)
    {
        $response = $this->service->getById($id);

        if (!$response) {
            return response()->json(['status' => 'error', 'message' => 'Quote Response not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $response
        ]);
    }

    public function store(UnitQuoteResponseRequest $request)
    {
        $created = $this->service->create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Response created successfully',
            'data' => $created
        ]);
    }

    public function update(UnitQuoteResponseRequest $request, $id)
    {
        $response = $this->service->getById($id);

        if (!$response) {
            return response()->json(['status' => 'error', 'message' => 'Quote Response not found'], 404);
        }

        $updated = $this->service->update($response, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Response updated successfully',
            'data' => $updated
        ]);
    }

    public function destroy($id)
    {
        $response = $this->service->getById($id);

        if (!$response) {
            return response()->json(['status' => 'error', 'message' => 'Quote Response not found'], 404);
        }

        $this->service->delete($response);

        return response()->json(['status' => 'success', 'message' => 'Response deleted']);
    }
}
