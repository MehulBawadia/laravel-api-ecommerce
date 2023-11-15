<?php

namespace App\Http\Requests\v1\Users;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutBillingRequest extends FormRequest
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
            'billing_address_id' => 'required|numeric|integer|exists:user_addresses,id',
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
            'billing_address_id.required' => 'Select the billing address.',
            'billing_address_id.numeric' => 'Selected billing address should be a valid.',
            'billing_address_id.integer' => 'Selected billing address should be a valid.',
            'billing_address_id.exists' => 'Selected billing address not found in our records.',
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
            'billing_address_id' => [
                'description' => 'Required. The id of the billing address that the invoice will be sent to.',
                'example' => 1,
            ],
        ];
    }
}
