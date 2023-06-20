<?php

namespace App\Http\Requests\v1\Users;

use Illuminate\Foundation\Http\FormRequest;

class ShippingAddressRequest extends FormRequest
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
            'shipping_first_name' => 'required|string|max:100',
            'shipping_last_name' => 'required|string|max:100',
            'shipping_email' => 'required|email:filter|max:100',
            'shipping_contact' => 'required|string|max:20',
            'shipping_address_line_1' => 'required|string|max:100',
            'shipping_address_line_2' => 'nullable|string|max:100',
            'shipping_area' => 'required|string|max:100',
            'shipping_landmark' => 'nullable|string|max:100',
            'shipping_city' => 'required|string|max:100',
            'shipping_postal_code' => 'required|alpha_num|max:20',
            'shipping_state_province' => 'required|string|max:100',
            'shipping_country' => 'required|string|max:100',
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
            'shipping_first_name' => [
                'description' => 'Required. The first name of the user on whom the invoice will be generated. Generally, it is the first name of the authenticated user.',
                'example' => 'John',
            ],
            'shipping_last_name' => [
                'description' => 'Required. The last name of the user on whom the invoice will be generated. Generally, it is the last name of the authenticated user.',
                'example' => 'Doe',
            ],
            'shipping_email' => [
                'description' => 'Required. The email of the user of whom the invoice will be generated. Generally, it is the email of the authenticated user.',
                'example' => 'johndoe@example.com',
            ],
            'shipping_contact' => [
                'description' => 'Required. The contact number of the user of whom the invoice will be generated. Generally, it is the contact number of the authenticated user.',
                'example' => '9876543210',
            ],
            'shipping_address_line_1' => [
                'description' => 'Required. The first line of the address. Generally, this is the room/flat/apartment number of the user.',
                'example' => '24, Building Name',
            ],
            'shipping_address_line_2' => [
                'description' => 'Optional. The second line of the address. Generally, this is the street name and/or number where the user lives.',
                'example' => 'Some street name',
            ],
            'shipping_area' => [
                'description' => 'Required. Generally, it is the nearest railway station name. But you can give it anything of your choice.',
                'example' => 'St. Peters Stn',
            ],
            'shipping_landmark' => [
                'description' => 'Optional. The famous landmark nearby to your house.',
                'example' => 'Chill StarBucks Cafe',
            ],
            'shipping_city' => [
                'description' => 'Required. The city in which the user resides.',
                'example' => 'Mumbai',
            ],
            'shipping_postal_code' => [
                'description' => 'Required. The postal or zip or ping code.',
                'example' => '123456',
            ],
            'shipping_state_province' => [
                'description' => 'Required. The state or province in which your city is.',
                'example' => 'Mahrashtra',
            ],
            'shipping_country' => [
                'description' => 'Required. The name of the country.',
                'example' => 'India',
            ],
        ];
    }
}
