<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\Categories\AddCategoryRequest;
use App\Http\Requests\v1\Admin\Categories\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

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

    /**
     * Store a new category.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AddCategoryRequest $request)
    {
        DB::beginTransaction();

        try {
            $category = Category::create($request->all());

            DB::commit();

            return $this->successResponse('Category added successfully.', $category, 201);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse('Could not add category.');
        }
    }

    /**
     * Fetch the details about the given category id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $category = Category::select([
            'id', 'name', 'meta_title', 'meta_description', 'meta_keywords',
        ])->find($id);
        if (! $category) {
            return $this->errorResponse('Category not found.', [], 404);
        }

        return $this->successResponse('', $category);
    }

    /**
     * Update the category details of the given id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, UpdateCategoryRequest $request)
    {
        $category = Category::find($id);
        if (! $category) {
            return $this->errorResponse('Category not found.', [], 404);
        }

        DB::beginTransaction();

        try {
            $category->update($request->all());

            DB::commit();

            return $this->successResponse('Category updated successfully.', $category->fresh());
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse('Could not update category.');
        }
    }

    /**
     * Delete the category details of the given id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (! $category) {
            return $this->errorResponse('Category not found.', [], 404);
        }

        DB::beginTransaction();

        try {
            $category->delete();

            DB::commit();

            return $this->successResponse('Category deleted successfully.');
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse('Could not delete category.');
        }
    }
}
