<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\MobileDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileDataController extends Controller
{
    public function __invoke(Request $request, MobileDataService $mobileData): JsonResponse
    {
        return response()->json(['data' => $mobileData->snapshot($request->user())]);
    }
}
