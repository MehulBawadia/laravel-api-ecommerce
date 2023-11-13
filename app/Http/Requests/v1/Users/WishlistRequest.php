<?php

namespace App\Http\Requests\v1\Users;

use Illuminate\Foundation\Http\FormRequest;

class WishlistRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
        ];
    }

    /**
     * Custom error messages.
     *
     * @return arrray
     */
    public function messages()
    {
        return [
            'product_id.required' => 'The product is required.',
            'product_id.exists' => 'The product must does not exist in our records.',
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
            'product_id' => [
                'description' => 'Required. The id of the product.',
                'example' => 1,
            ],
        ];
    }
}
