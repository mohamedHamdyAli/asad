<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\QuoteRequest;
use App\Http\Requests\Api\UserIdRequest;
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
        if (!guestAuth()) {
            return failReturnMsg('Unauthorized, guest access only', 403);
        }
        return $this->quoteApiService->getBuildingType();
    }
    public function getPriceType()
    {
        if (!guestAuth()) {
            return failReturnMsg('Unauthorized, guest access only', 403);
        }
        return $this->quoteApiService->getPriceType();
    }
    public function quoteRequest(QuoteRequest $request){
        if (!guestAuth()) {
            return failReturnMsg('Unauthorized, guest access only', 403);
        }
        return $this->quoteApiService->quoteRequest($request->validated());
    }
    public function getPriceResponse(UserIdRequest $request){
        if (!guestAuth()) {
            return failReturnMsg('Unauthorized, guest access only', 403);
        }
        return $this->quoteApiService->priceResponse($request->validated());
    }
}
