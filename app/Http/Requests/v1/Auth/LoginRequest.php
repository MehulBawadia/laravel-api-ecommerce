<?php

namespace App\Http\Requests\v1\Auth;

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
     * @return array
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
                'description' => 'Requred. The email address of the user that you are trying to authenticate. Should be a valid email address.',
                'example' => 'userone@example.com',
            ],
            'password' => [
                'description' => 'Required. The password associated with the above email address.',
                'example' => 'Secret',
            ],
        ];
    }
}
