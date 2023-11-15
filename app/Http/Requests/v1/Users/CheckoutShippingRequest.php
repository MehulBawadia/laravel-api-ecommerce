<?php

namespace App\Http\Requests\v1\Users;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutShippingRequest extends FormRequest
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
            'shipping_address_id' => 'required|numeric|integer|exists:user_addresses,id',
        ];
    }

    /**
     * Custom error messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'shipping_address_id.required' => 'Select the shipping address.',
            'shipping_address_id.numeric' => 'Selected shipping address should be a valid.',
            'shipping_address_id.integer' => 'Selected shipping address should be a valid.',
            'shipping_address_id.exists' => 'Selected shipping address not found in our records.',
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
            'shipping_address_id' => [
                'description' => 'Required. The id of the shipping address that the invoice will be sent to.',
                'example' => 1,
            ],
        ];
    }
}
