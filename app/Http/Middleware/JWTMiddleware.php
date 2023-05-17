<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Throwable;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            return $next($request);
        } catch (Throwable $e) {
            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'errors' => [
                    'authentication' => [$e->getMessage()],
                ],
            ], 422);
        }
    }
}
