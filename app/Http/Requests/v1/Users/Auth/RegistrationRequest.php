<?php

namespace App\Http\Requests\v1\Users\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email:filter|unique:users,email',
            'password' => 'required|string',
            'confirm_password' => 'required|same:password',
        ];
    }

    /**
     * The form body parameters.
     * This content will be used whenever generate the docuemtation.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [
            'first_name' => [
                'description' => 'Required. Your first name.',
                'example' => 'John',
            ],
            'last_name' => [
                'description' => 'Required. Your last name.',
                'example' => 'Doe',
            ],
            'email' => [
                'description' => 'Required. Should be a valid and unique email address. This will be used at the time of signing in to the application.',
                'example' => 'johndoe@example.com',
            ],
            'password' => [
                'description' => 'Required. Choose a strong password.',
                'example' => 'Secret',
            ],
            'confirm_password' => [
                'description' => 'Required. Should be same as password field.',
                'example' => 'Secret',
            ],
        ];
    }
}
