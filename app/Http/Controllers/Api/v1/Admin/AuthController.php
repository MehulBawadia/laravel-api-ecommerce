<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Auth the administrator
     *
     * @return void
     */
    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return $this->errorResponse('The provided credentials are incorrect.', [], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $data = [
                'access_token' => $token,
                'token_type' => 'Bearer',
            ];

            return $this->successResponse('Administrator logged in successfully.', $data, 200);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            return $this->errorResponse('Could not Auth administrator.');
        }
    }

    /**
     * Logout the administator.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->successResponse('Administrator logged out successfully.');
    }
}
