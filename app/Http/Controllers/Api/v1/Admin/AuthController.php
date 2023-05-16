<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @group Administrator Endpoints
 *
 * @subgroup Admin Authentication
 *
 * @subgroupDescription  The login and logout endpoints of the admin panel.
 */
class AuthController extends Controller
{
    /**
     * Login
     *
     * Login the administrator, and generate the token.
     * This token will be used for further requests in the admin panel.
     *
     * @unauthenticated
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
     * Logout
     *
     * Logout the administrator user. When they log out, all the tokens
     * related to them will also get deleted.
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->successResponse('Administrator logged out successfully.');
    }
}
