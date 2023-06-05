<?php

namespace App\Http\Requests\v1\Users\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'password' => 'required|string',
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
                'description' => 'Required. The email to log in. Should be a valid email address.',
                'example' => 'johndoe@example.com',
            ],
            'password' => [
                'description' => 'Required. The password associated with the email',
                'example' => 'Secret',
            ],
        ];
    }
}
