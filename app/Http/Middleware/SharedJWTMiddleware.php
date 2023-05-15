<?php

namespace App\Http\Middleware;

use App\Contracts\JWT;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SharedJWTMiddleware
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
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $sharingToken = $request->query('sharing_token');
            $payload = $this->JWTService->decode($sharingToken);

            if ($payload['claims']['addClaims']['exp'] < Carbon::now()->timestamp) {
                throw new Exception('Sharing token is expired.');
            }

            $request->merge(['authorId' => $payload['claims']['sub']]);

            return $next($request);
        } catch (Throwable $e) {
            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'errors' => [
                    'sharing-token' => [$e->getMessage()],
                ],
            ], 422);
        }
    }
}
