<?php

namespace App\Http\Requests\v1\Settings;

use Illuminate\Foundation\Http\FormRequest;

class GeneralSettingsRequest extends FormRequest
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
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email:filter|unique:users,email,'. auth()->id(),
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
            'first_name' => [
                'description' => 'Required. Your first name. Should not be more than 100 characters.',
                'example' => 'John',
            ],
            'last_name' => [
                'description' => 'Required. Your last name. Should not be more than 100 characters.',
                'example' => 'Doe',
            ],
            'email' => [
                'description' => 'Required. Your valid email address. Should not be taken by any other user.',
                'example' => 'johndoe@example.com',
            ],
        ];
    }
}
