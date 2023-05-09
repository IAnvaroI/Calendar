<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    /**
     * Return the user data for editing.
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
     * Update the user data in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request)
    {

    }

    /**
     * Remove the user from storage.
     *
     * @return RedirectResponse
     */
    public function destroy()
    {

    }
}
