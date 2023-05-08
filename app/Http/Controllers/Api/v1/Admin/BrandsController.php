<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\Brands\AddBrandRequest;
use App\Http\Requests\v1\Admin\Brands\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;

class BrandsController extends Controller
{
    /**
     * Get and paginate the brands.
     *
     * @return void
     */
    public function index()
    {
        $brands = Brand::select([
            'id', 'name', 'slug', 'meta_title', 'meta_description', 'meta_keywords',
        ])->paginate(16);

        return $this->successResponse('', $brands);
    }

    /**
     * Store a new Brand.
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
     * Fetch the details about the given brand id.
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
     * Update the brand details of the given id.
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
     * Delete the brand details of the given id.
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
