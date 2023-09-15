<?php

namespace App\Http\Requests\v1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GenerateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->guest();
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
            'email' => 'required|email:filter',
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

    /**
     * Prepare the payload so that the data can be saved.
     *
     * @return array
     */
    public function formPayload()
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'is_admin' => true,
        ];
    }
}
