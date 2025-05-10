<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * @group Auth
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try
        {
            $user = User::create([
                'name'     => Arr::get($request, 'name'),
                'email'    => Arr::get($request, 'email'),
                'password' => Hash::make(Arr::get($request, 'password')),
            ]);

            return response()->json([
                'token' => $user->createToken('default', ['*'])->plainTextToken,
            ], 201);
        }
        catch (\Exception $exception)
        {
            Log::error($exception->getMessage());
        }

        return response()->json(['error' => __('Unexpected error, try register account later')], 500);
    }

    /**
     * @group Auth
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password')))
            return response()->json(['message' => __('Bad credentials')], 401);

        $token = $request->user()->createToken('default', ['*'])->plainTextToken;

        return response()->json(['token' => $token]);
    }

    /**
     * @group Auth
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success']);
    }
}
