<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserController extends Controller
{

    /**
     * Return the authenticated user data for editing.
     *
     * @return JsonResponse
     */
    public function edit(): JsonResponse
    {
        $user = Auth::getUser();

        return response()->json([
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Update the authenticated user data in storage.
     *
     * @param UpdateUserRequest $request
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        try {
            $validatedRequest = $request->safe();
            $user = Auth::getUser();

            $user->update([
                'firstname' => $validatedRequest['firstname'],
                'lastname' => $validatedRequest['lastname'],
                'birth_date' => $validatedRequest['birthDate'],
                'email' => $validatedRequest['email'],
            ]);
        } catch (Throwable $e) {
            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'errors' => [
                    'userUpdate' => [$e->getMessage()],
                ],
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'The user update completed successfully.',
        ]);
    }

    /**
     * Update the authenticated user password in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'oldPassword' => 'required|string|min:6',
            'newPassword' => 'required|string|min:6',
        ]);

        try {
            $passwords = $request->only('oldPassword', 'newPassword');
            $user = Auth::getUser();

            if (Hash::check($passwords['oldPassword'], $user->password)) {
                $user->password = Hash::make($passwords['newPassword']);
                $user->save();
            } else {
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'passwordMatch' => ['Entered old password does not match saved in database one.'],
                    ],
                ], 422);
            }
        } catch (Throwable $e) {
            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'errors' => [
                    'userDelete' => [$e->getMessage()],
                ],
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'The password update completed successfully.',
        ]);
    }

    /**
     * Remove the authenticated user from storage.
     *
     * @return JsonResponse
     */
    public function destroy(): JsonResponse
    {
        try {
            $user = Auth::getUser();

            $user->delete();
        } catch (Throwable $e) {
            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'errors' => [
                    'userDelete' => [$e->getMessage()],
                ],
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'The user deletion completed successfully.',
        ]);
    }
}
