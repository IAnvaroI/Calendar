<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'errors' => [
                    'authentication' => ['There are no such credentials.'],
                ],
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'token' => $token,
        ]);

    }

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validatedRequest = $request->safe();

        $user = User::create([
            'firstname' => $validatedRequest['firstname'],
            'lastname' => $validatedRequest['lastname'],
            'birth_date' => $validatedRequest['birthDate'],
            'email' => $validatedRequest['email'],
            'password' => Hash::make($validatedRequest['password']),
        ]);
        $token = Auth::login($user);

        return response()->json([
            'status' => 'success',
            'token' => $token,
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }
}
