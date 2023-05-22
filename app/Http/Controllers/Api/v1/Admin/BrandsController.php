<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\Brands\AddBrandRequest;
use App\Http\Requests\v1\Admin\Brands\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;

/**
 * @group Administrator Endpoints
 *
 * @subgroup Brands
 */
class BrandsController extends Controller
{
    /**
     * List All brands
     *
     * Display all the brands with pagination.
     * At a time, there are total of 16 records that will be displayed.
     *
     * @queryParam page integer The page number. Defaults to 1. Example: 1
     *
     * @responseFile storage/responses/admin/brands/list-all.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $brands = Brand::select([
            'id', 'name', 'slug', 'meta_title', 'meta_description', 'meta_keywords',
        ])->paginate(16);

        return $this->successResponse('', $brands);
    }

    /**
     * Add new Brand
     *
     * Create a new Brand and store it's details.
     *
     * @responseFile status=201 storage/responses/admin/brands/created.json
     * @responseFile status=422 storage/responses/admin/brands/validation-errors.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AddBrandRequest $request)
    {
        DB::beginTransaction();

        try {
            $brand = Brand::create($request->all());

            DB::commit();

            return $this->successResponse('Brand added successfully.', $brand, 201);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse('Could not add brand.');
        }
    }

    /**
     * Get single Brand
     *
     * Fetch the details about the given Brand id.
     *
     * @urlParam id integer required The id of the Brand. Example: 1
     *
     * @responseFile status=200 storage/responses/admin/brands/fetch-single.json
     * @responseFile status=404 storage/responses/admin/brands/not-found.json
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $brand = Brand::select([
            'id', 'name', 'meta_title', 'meta_description', 'meta_keywords',
        ])->find($id);
        if (! $brand) {
            return $this->errorResponse('Brand not found.', [], 404);
        }

        return $this->successResponse('', $brand);
    }

    /**
     * Update Brand
     *
     * Update the Brand details of the given id.
     *
     * @urlParam id integer required The id of the Brand. Example: 1
     *
     * @responseFile status=200 storage/responses/admin/brands/updated.json
     * @responseFile status=404 storage/responses/admin/brands/not-found.json
     * @responseFile status=422 storage/responses/admin/brands/validation-errors.json
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, UpdateBrandRequest $request)
    {
        $brand = Brand::find($id);
        if (! $brand) {
            return $this->errorResponse('Brand not found.', [], 404);
        }

        DB::beginTransaction();

        try {
            $brand->update($request->all());

            DB::commit();

            return $this->successResponse('Brand updated successfully.', $brand->fresh());
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse('Could not update brand.');
        }
    }

    /**
     * Delete a Brand
     *
     * Delete the Brand details of the given id.
     * This will soft delete the Brand.
     * Meaning the record will be present in the database, however,
     * it won't be available to access.
     *
     * @urlParam id integer required The id of the Brand. Example: 1
     *
     * @responseFile status=200 storage/responses/admin/brands/deleted.json
     * @responseFile status=404 storage/responses/admin/brands/not-found.json
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $brand = Brand::find($id);
        if (! $brand) {
            return $this->errorResponse('Brand not found.', [], 404);
        }

        DB::beginTransaction();

        try {
            $brand->delete();

            DB::commit();

            return $this->successResponse('Brand deleted successfully.');
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse('Could not delete brand.');
        }
    }
}
