<?php

namespace App\Http\Controllers\Api\v1\Admin;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\v1\Admin\ForgotPasswordMail;
use App\Http\Requests\v1\Admin\ResetPasswordRequest;
use App\Http\Requests\v1\Admin\ForgotPasswordRequest;

class PasswordController extends Controller
{
    /**
     * Send the password reset link to the given email address.
     *
     *
     * @return void
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

            return $this->successResponse('Password reset link sent successfully.', $data, 201);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            return $this->errorResponse('Could not Auth administrator.');
        }
    }

    /**
     * Reset the user's password with the new password.
     *
     * @param \App\Http\Requests\v1\Admin\ResetPasswordRequest $request
     * @return void
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
