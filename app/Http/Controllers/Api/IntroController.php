<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IntroRequest;
use App\Http\Resources\IntroResource;
use App\services\Intro\IntroHelperFunctionService;

class IntroController extends Controller
{
    private IntroHelperFunctionService $introService;
    public function __construct(IntroHelperFunctionService $introService)
    {
        $this->introService = $introService;
    }
    public function getIntro(IntroRequest $request)
    {
        $data = $this->introService->getIntro($request['type']);
        return successReturnData( IntroResource::collection($data),  'Data Fetched Successfully');

    }
}
