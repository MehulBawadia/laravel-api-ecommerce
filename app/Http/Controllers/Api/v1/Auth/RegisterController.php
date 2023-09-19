<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\RegistrationRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * @group Common Endpoints
 *
 * @subgroup Authentication
 *
 * @subgroupDescription  The endpoints that are common for all the users in the whole application.
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
            $stripeCustomer = Http::withHeaders([
                'Authorization' => 'Bearer '.config('stripe.keys.secret'),
            ])->asForm()->post('https://api.stripe.com/v1/customers', [
                'name' => "$request->first_name $request->last_name",
                'email' => $request->email,
            ])->json();

            $request['stripe_user_id'] = is_array($stripeCustomer) && array_key_exists('id', $stripeCustomer) ? $stripeCustomer['id'] : null;
            $request['password'] = bcrypt($request->password);
            $user = User::create($request->all());

            DB::commit();

            return $this->successResponse(__('response.auth.register'), $user, 201);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.auth.could_not_register'));
        }
    }
}
