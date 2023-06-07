<?php

namespace App\Http\Controllers\Api\v1\Users\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Users\Auth\ForgotPasswordRequest;
use App\Http\Requests\v1\Users\Auth\ResetPasswordRequest;
use App\Mail\v1\User\ForgotPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * @group User Endpoints
 *
 * @subgroup Authentication
 */
class PasswordController extends Controller
{
    /**
     * Forgot Password
     *
     * Send the password reset link to the given email address.
     *
     * @responseFile status=201 storage/responses/users/auth/forgot-password/success.json
     * @responseFile status=422 storage/responses/users/auth/forgot-password/validation-errors.json
     *
     * @unauthenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLink(ForgotPasswordRequest $request)
    {
        DB::beginTransaction();

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

            DB::commit();

            return $this->successResponse('Password reset link sent successfully.', $data, 201);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse('Could not send password reset link.');
        }
    }

    /**
     * Reset password
     *
     * Reset the user's password with the new password.
     *
     * @responseFile status=201 storage/responses/users/auth/reset-password/success.json
     * @responseFile status=422 storage/responses/users/auth/reset-password/validation-errors.json
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
        if (! $resetToken) {
            return $this->errorResponse('Invalid email address or reset token.', [], 404);
        }

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return $this->errorResponse('User not found with the given email address.', [], 404);
        }

        $createdAt = Carbon::parse($resetToken->created_at);
        if ($createdAt->diffInMinutes() >= config('auth.passwords.users.expire')) {
            $this->deleteTokenRecord();

            return $this->errorResponse('Token expired. Generate a new token.', [], 403);
        }

        DB::beginTransaction();

        try {
            $user->update(['password' => bcrypt($request->new_password)]);

            $this->deleteTokenRecord();

            DB::commit();

            return $this->successResponse('Password reset successfully.', [], 201);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse('Could not reset password.');
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
