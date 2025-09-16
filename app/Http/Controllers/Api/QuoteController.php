<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\QuoteRequest;
use App\services\Quote\QuoteApiService;

class QuoteController extends Controller
{
    private QuoteApiService $quoteApiService;

    public function __construct(QuoteApiService $quoteApiService)
    {
        $this->quoteApiService = $quoteApiService;
    }
    public function getBuildingType()
    {
        return $this->quoteApiService->getBuildingType();
    }
    public function getPriceType()
    {
        return $this->quoteApiService->getPriceType();
    }
    public function quoteRequest(QuoteRequest $request){
        return $this->quoteApiService->quoteRequest($request->validated());
    }
}
