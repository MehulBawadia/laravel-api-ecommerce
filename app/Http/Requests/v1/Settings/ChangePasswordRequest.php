<?php

namespace App\Http\Requests\v1\Settings;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => 'required|current_password:sanctum',
            'new_password' => 'required|string|confirmed',
            'new_password_confirmation' => 'required',
        ];
    }

    /**
     * Custom error messages.
     *
     * @return void
     */
    public function messages()
    {
        return [
            'current_password.current_password' => 'The current password is incorrect.',
            'new_password.confirmed' => 'The confirm new password do not match new password.',
            'new_password_confirmation.required' => 'The confirm new password field is required.',
        ];
    }

    /**
     * The body parameters. Used in the documentation while generating.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [
            'current_password' => [
                'description' => 'Required. The current password of your account.',
                'example' => 'Password',
            ],
            'new_password' => [
                'description' => 'Required. The new password for your account.',
                'example' => 'Secret',
            ],
            'new_password_confirmation' => [
                'description' => 'Required. This should be same as new password.',
                'example' => 'Secret',
            ],
        ];
    }
}
