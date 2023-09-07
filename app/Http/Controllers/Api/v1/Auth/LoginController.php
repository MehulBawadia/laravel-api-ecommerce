<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @group Common Endpoints
 *
 * @subgroup Authentication
 *
 * @subgroupDescription  The login and logout endpoints for both admin user and non-admin user
 */
class LoginController extends Controller
{
    /**
     * Login
     *
     * Login the user (admin and non-admin), and generate the bearer token.
     * This token will be used for further requests in the admin user panel or in the non-admin user panel.
     *
     * @unauthenticated
     *
     * @return void
     */
    public function check(LoginRequest $request)
    {
        try {
            $user = User::select([
                'id', 'first_name', 'last_name', 'email', 'password', 'is_admin',
            ])
            ->where('email', $request->email)
            ->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return $this->errorResponse('The provided credentials are incorrect.', [], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $data = [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'is_admin' => $user->is_admin,
                'user' => $user,
            ];

            $userType = $user->is_admin ? 'Administrator' : 'User';

            return $this->successResponse("$userType logged in successfully.", $data, 200);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            return $this->errorResponse(`Could not login the $userType.`);
        }
    }

    /**
     * Logout
     *
     * Logout the admin user or non-admin user. When they log out, all the tokens
     * related to their account will also get deleted.
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        $userType = $request->user()->is_admin ? 'Administrator' : 'User';

        return $this->successResponse("$userType logged out successfully.");
    }
}
