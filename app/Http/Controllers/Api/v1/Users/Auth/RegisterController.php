<?php

namespace App\Http\Controllers\Api\v1\Users\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Users\Auth\RegistrationRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * @group User Endpoints
 *
 * @subgroup Authentication
 */
class RegisterController extends Controller
{
    /**
     * Registration
     *
     * API to register a user in the application.
     *
     * @responseFile status=201 storage/responses/users/auth/registration/success.json
     * @responseFile status=422 storage/responses/users/auth/registration/validation-errors.json
     *
     * @unauthenticated
     *
     * @return void
     */
    public function store(RegistrationRequest $request)
    {
        DB::beginTransaction();

        try {
            $request['password'] = bcrypt($request->password);
            $user = User::create($request->all());

            DB::commit();

            return $this->successResponse('You have registered successfully.', $user, 201);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse('Could not register.');
        }
    }
}
