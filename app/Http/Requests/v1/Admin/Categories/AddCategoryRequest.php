<?php

namespace App\Http\Requests\v1\Admin\Categories;

use Illuminate\Foundation\Http\FormRequest;

class AddCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
            'name' => 'required|string|unique:categories,name|max:100',
            'description' => 'required|string|max:255',
            'meta_title' => 'required|string|max:80',
            'meta_description' => 'required|string|max:180',
            'meta_keywords' => 'nullable|string|max:255',
        ];
    }
}
