<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\Categories\AddCategoryRequest;
use App\Http\Requests\v1\Admin\Categories\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

/**
 * @group Administrator Endpoints
 *
 * @subgroup Categories
 */
class CategoriesController extends Controller
{
    /**
     * List All categories
     *
     * Display all the categories with pagination.
     * At a time, there are total of 16 records that will be displayed.
     *
     * @queryParam page integer The page number. Defaults to 1. Example: 1
     *
     * @responseFile storage/responses/admin/categories/list-all.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Category::select([
            'id', 'name', 'slug', 'meta_title', 'meta_description', 'meta_keywords',
        ])->paginate(16)->withQueryString();

        return $this->successResponse('', $categories);
    }

    /**
     * Add new category
     *
     * Create a new category and store it's details.
     *
     * @responseFile status=201 storage/responses/admin/categories/created.json
     * @responseFile status=422 storage/responses/admin/categories/validation-errors.json
     *
     * @authenticated
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
     * Get single category
     *
     * Fetch the details about the given category id.
     *
     * @urlParam id integer required The id of the category. Example: 1
     *
     * @responseFile status=200 storage/responses/admin/categories/fetch-single.json
     * @responseFile status=404 storage/responses/admin/categories/not-found.json
     *
     * @authenticated
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
     * Update category
     *
     * Update the category details of the given id.
     *
     * @urlParam id integer required The id of the category. Example: 1
     *
     * @responseFile status=200 storage/responses/admin/categories/updated.json
     * @responseFile status=404 storage/responses/admin/categories/not-found.json
     * @responseFile status=422 storage/responses/admin/categories/validation-errors.json
     *
     * @authenticated
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
     * Delete a category
     *
     * Delete the category details of the given id.
     * This will soft delete the category.
     * Meaning the record will be present in the database, however,
     * it won't be available to access.
     *
     * @urlParam id integer required The id of the category. Example: 1
     *
     * @responseFile status=200 storage/responses/admin/categories/deleted.json
     * @responseFile status=404 storage/responses/admin/categories/not-found.json
     *
     * @authenticated
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
