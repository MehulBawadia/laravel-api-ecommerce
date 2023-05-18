<?php

namespace App\Http\Requests\v1\Admin\Categories;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
            'name' => 'required|string|unique:categories,name,'.$this->id.'|max:100',
            'description' => 'required|string|max:255',
            'meta_title' => 'required|string|max:80',
            'meta_description' => 'required|string|max:180',
            'meta_keywords' => 'nullable|string|max:255',
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
                'description' => 'Required. The name of the category, should be unique.',
                'example' => 'T-shirt for Women',
            ],
            'description' => [
                'description' => 'Required. The description of the category.',
                'example' => 'T-shirt for Women made of the best quality materials sold all over the world.',
            ],
            'meta_title' => [
                'description' => 'Required. The title of the page. Refers to the text that is displayed on search engine result pages and browser tabs to indicate the topic of a webpage.',
                'example' => 'T-shirt for Women made of the best quality materials.',
            ],
            'meta_description' => [
                'description' => 'Required. This informs and interests users with a short, relevant summary of what a particular page is about.',
                'example' => 'T-shirt for Women made of the best quality materials sold all over the world.',
            ],
            'meta_keywords' => [
                'description' => 'A comma separated words or phrases related to the category.',
                'example' => 'T-shirt, t-shirt for women, best quality tshirt for women',
            ],
        ];
    }
}
