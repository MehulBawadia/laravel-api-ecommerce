<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\ForgotPasswordRequest;
use App\Http\Requests\v1\Auth\ResetPasswordRequest;
use App\Mail\v1\Auth\ForgotPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * @group Common Endpoints
 *
 * @subgroup Password Resetting
 *
 * @subgroupDescription The endpoints URL for request password change and resetting the same.
 */
class PasswordController extends Controller
{
    /**
     * Send Password Reset Link
     *
     * Send the password reset link to the provided email address via an E-Mail.
     *
     * @unauthenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLink(ForgotPasswordRequest $request)
    {
        try {
            $randomString = \Illuminate\Support\Str::random(36);

            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $randomString,
                'created_at' => now(),
            ]);

            $data = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();
            $data = collect($data)->put(
                'reset_password_link', env('APP_FRONTEND_BASE_URL').'/reset-password/'.$randomString.'?email='.$request->email,
            )->toArray();

            Mail::to($request->email)
                ->send(new ForgotPasswordMail($data));

            return $this->successResponse(__('response.auth.password_reset_link_sent'), $data, 201);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            return $this->errorResponse(__('response.auth.failed_sending_password_reset_link'));
        }
    }

    /**
     * Reset Password
     *
     * Reset the admin's or the user's password with the new password.
     * Deletes the record from the database table after resetting.
     *
     * @unauthenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(ResetPasswordRequest $request)
    {
        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return $this->errorResponse(__('response.auth.reset_password_user_not_found'), [], 404);
        }

        $createdAt = Carbon::parse($resetToken->created_at);
        if ($createdAt->diffInMinutes() >= config('auth.passwords.users.expire')) {
            $this->deleteTokenRecord();

            return $this->errorResponse(__('response.auth.reset_password_token_expired'), [], 403);
        }

        DB::beginTransaction();

        try {
            $user->update(['password' => bcrypt($request->new_password)]);

            $this->deleteTokenRecord();

            DB::commit();

            return $this->successResponse(__('response.auth.reset_password'), [], 201);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.auth.could_not_reset_password'));
        }
    }

    /**
     * Delete the token record as it is no longer required.
     *
     * @return void
     */
    protected function deleteTokenRecord()
    {
        DB::table('password_reset_tokens')
            ->where('email', request('email'))
            ->where('token', request('token'))
            ->delete();
    }
}
