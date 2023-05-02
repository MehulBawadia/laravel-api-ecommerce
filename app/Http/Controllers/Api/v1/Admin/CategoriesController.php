<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoriesController extends Controller
{
    /**
     * Get and paginate the categories.
     *
     * @return void
     */
    public function index()
    {
        $categories = Category::select([
            'id', 'name', 'slug', 'meta_title', 'meta_description', 'meta_keywords',
        ])->paginate(16);

        return $this->successResponse('', $categories);
    }
}
