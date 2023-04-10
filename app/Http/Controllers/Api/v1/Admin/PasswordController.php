<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\ForgotPasswordRequest;
use App\Mail\v1\Admin\ForgotPasswordMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PasswordController extends Controller
{
    /**
     * Auth the administrator
     *
     * @return void
     */
    public function forgot(ForgotPasswordRequest $request)
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
                'reset_password_link', env('APP_FRONTEND_BASE_URL') . '/reset-password/'. $randomString . '?email='. $request->email,
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
}
