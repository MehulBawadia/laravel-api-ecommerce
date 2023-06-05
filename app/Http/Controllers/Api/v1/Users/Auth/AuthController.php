<?php

namespace App\Http\Controllers\Api\v1\Users\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Users\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * @group User Endpoints
 *
 * @subgroup Authentication
 */
class AuthController extends Controller
{
    /**
     * Login
     *
     * API to login a user in the application.
     *
     * @responseFile status=200 storage/responses/users/auth/login/success.json
     * @responseFile status=401 storage/responses/users/auth/login/invalid-credentials.json
     * @responseFile status=422 storage/responses/users/auth/login/validation-errors.json
     *
     * @unauthenticated
     *
     * @return void
     */
    public function login(LoginRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return $this->errorResponse('The provided credentials are incorrect.', [], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $data = [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ];

            DB::commit();

            return $this->successResponse('You have logged in successfully.', $data);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse('Could not login.');
        }
    }

    /**
     * Logout
     *
     * Logout the user. When they log out, all the tokens related to them
     * will also get deleted.
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->successResponse('You have logged out succesfully.');
    }
}
