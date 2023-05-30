<?php

namespace App\Http\Requests\v1\Admin\Products;

use Illuminate\Foundation\Http\FormRequest;

class AddProductRequest extends FormRequest
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
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
            'brand_id' => 'required|integer|exists:brands,id',
            'rate' => 'required|numeric|min:0.0',
            'quantity' => 'required|numeric|min:0',
            'image' => 'required|file|mimes:jpg,jpeg,png,JPG,JPEG,PNG|max:1024',
            'meta_title' => 'required|string|max:80',
            'meta_description' => 'required|string|max:180',
            'meta_keywords' => 'nullable|string|max:255',
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
            'category_id.required' => 'Please select the category.',
            'category_id.integer' => 'Selected category is invalid.',
            'category_id.exists' => 'Selected category does not exist.',

            'brand_id.required' => 'Please select the brand.',
            'brand_id.integer' => 'Selected brand is invalid.',
            'brand_id.exists' => 'Selected brand does not exist.',
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
            'name' => [
                'description' => 'Required. The name of the product.',
                'example' => 'Gucci',
            ],
            'category_id' => [
                'description' => 'Required. The id of category that this product is being added in. Should be positive integer, and should exist in categories table.',
                'example' => 1,
            ],
            'brand_id' => [
                'description' => 'Required. The id of brand that this product is being added in. Should be positive integer, and should exist in brands table.',
                'example' => 1,
            ],
            'rate' => [
                'description' => 'Required. The amount of the product that it will be sold to the customers. Should be positive integer, greater than 0.',
                'example' => 119,
            ],
            'quantity' => [
                'description' => 'Required. The quantity of the product. Should be positive integer, greater than 0.',
                'example' => 10,
            ],
            'image' => [
                'description' => 'Required. The image of the product. Should be a valid file with a valid extension of either jpg, jpeg, or png. No other image file types are supported at the moment.',
            ],
            'description' => [
                'description' => 'Required. The description of the product.',
                'example' => 'Gucci clothes made of the best quality materials sold all over the world.',
            ],
            'meta_title' => [
                'description' => 'Required. The title of the page. Refers to the text that is displayed on search engine result pages and browser tabs to indicate the topic of a webpage.',
                'example' => 'Gucci clothes made of the best quality materials.',
            ],
            'meta_description' => [
                'description' => 'Required. This informs and interests users with a short, relevant summary of what a particular page is about.',
                'example' => 'Gucci clothes made of the best quality materials sold all over the world.',
            ],
            'meta_keywords' => [
                'description' => 'A comma separated words or phrases related to the product.',
                'example' => 'T-shirt, Gucci clothes, best quality tshirt for women',
            ],
        ];
    }
}
