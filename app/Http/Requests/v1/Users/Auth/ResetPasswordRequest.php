<?php

namespace App\Http\Requests\v1\Users\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email:filter|exists:password_reset_tokens,email',
            'token' => 'required|exists:password_reset_tokens,token',
            'new_password' => 'required',
            'repeat_new_password' => 'required|same:new_password',
        ];
    }

    /**
     * Custom error messages
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.exists' => 'The selected email does not exist.',
            'token.required' => 'The token is required.',
            'token.exists' => 'The token does not exist.',
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
            'email' => [
                'description' => 'Required. The email address that you have registered with. Should be valid and should exist in the application.',
                'example' => 'johndoe@example.com',
            ],
            'token' => [
                'description' => 'Required. The token that was sent to the email address.',
                'example' => 'PZj7o5NBJmWhmaJ6mAH8zMSOck5vFTlPOuaT',
            ],
            'new_password' => [
                'description' => 'Required. The new password to log in to your account.',
                'example' => 'Secret',
            ],
            'repeat_new_password' => [
                'description' => 'Required. Repeat the same password that has been added in new_password field',
                'example' => 'Secret',
            ],
        ];
    }
}
