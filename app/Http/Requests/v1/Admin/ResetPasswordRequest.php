<?php

namespace App\Http\Requests\v1\Admin;

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
}
