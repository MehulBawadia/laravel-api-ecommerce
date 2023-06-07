<?php

namespace App\Http\Requests\v1\Users\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
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
            'email' => 'required|email:filter|exists:users,email',
        ];
    }

    /**
     * Custom error messages
     *
     * @return  array
     */
    public function messages()
    {
        return [
            'email.exists' => 'The selected email does not exist.',
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
        ];
    }
}
