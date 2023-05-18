<?php

namespace App\Http\Controllers;

use App\Contracts\JWT;
use App\Http\Requests\IndexEventsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class JWTController extends Controller
{
    /**
     * @var JWT
     */
    private JWT $JWTService;

    /**
     * @param JWT $JWTService
     */
    public function __construct(JWT $JWTService)
    {
        $this->JWTService = $JWTService;
    }

    /**
     * @param IndexEventsRequest $request
     * @return JsonResponse
     */
    public function generateToken(IndexEventsRequest $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'token' => $this->JWTService->encode([
                'exp' => Carbon::now()->addDays(7)->timestamp,
                'filters' => $request->safe()->toArray(),
            ]),
        ]);
    }
}
