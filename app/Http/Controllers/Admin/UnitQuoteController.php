<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\services\UnitQuote\UnitQuoteSerivce;
use App\Http\Requests\Admin\UnitQuoteRequest;

class UnitQuoteController extends Controller
{
    protected $unitQuoteService;



    public function __construct(UnitQuoteSerivce $unitQuoteService)
    {
        $this->unitQuoteService = $unitQuoteService;
    }

    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->unitQuoteService->getAll()
        ]);
    }

    public function show($id)
    {
        $unitQuote = $this->unitQuoteService->getById($id);

        if (!$unitQuote) {
            return response()->json(['status' => 'error', 'message' => 'Unit quote not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $unitQuote
        ]);
    }

    public function store(UnitQuoteRequest $request)
    {
        $unitQuote = $this->unitQuoteService->create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Unit Quote created successfully',
            'data' => $unitQuote
        ]);
    }


    public function update(UnitQuoteRequest $request, $id)
    {
        $unitQuote = $this->unitQuoteService->getById($id);

        if (!$unitQuote) {
            return response()->json(['status' => 'error', 'message' => 'Unit quote not found'], 404);
        }

        $updated = $this->unitQuoteService->update($unitQuote, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Unit Quote updated successfully',
            'data' => $updated
        ]);
    }

    public function destroy($id)
    {
        $unitQuote = $this->unitQuoteService->getById($id);

        if (!$unitQuote) {
            return response()->json(['status' => 'error', 'message' => 'Unit quote not found'], 404);
        }

        $this->unitQuoteService->delete($unitQuote);

        return response()->json(['status' => 'success', 'message' => 'Unit Quote deleted successfully']);
    }

}
