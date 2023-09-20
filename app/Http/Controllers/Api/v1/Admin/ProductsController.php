<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\Products\AddProductRequest;
use App\Http\Requests\v1\Admin\Products\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * @group Administrator Endpoints
 *
 * @subgroup Products
 */
class ProductsController extends Controller
{
    /**
     * List All products
     *
     * Display all the products with pagination.
     * At a time, there are total of 16 records that will be displayed.
     *
     * @queryParam page integer The page number. Defaults to 1. Example: 1
     *
     * @responseFile storage/responses/admin/products/list-all.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $columns = $this->getSelectColumns();

        $products = Product::select($columns)
            ->with([
                'category:id,name,slug',
                'brand:id,name,slug',
            ])
            ->paginate(16);

        return $this->successResponse('', $products);
    }

    /**
     * Add new Product
     *
     * Create a new Product and store it's details.
     *
     * @responseFile status=201 storage/responses/admin/products/created.json
     * @responseFile status=422 storage/responses/admin/products/validation-errors.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AddProductRequest $request)
    {
        DB::beginTransaction();

        try {
            $stripeProduct = Http::withHeaders([
                'Authorization' => 'Bearer '.config('stripe.keys.secret'),
            ])
                ->asForm()
                ->post('https://api.stripe.com/v1/products', [
                    'name' => $request->name,
                    'description' => $request->description,
                    'default_price_data' => [
                        'currency' => 'inr',
                        'unit_amount' => $request->rate * 100,
                    ],
                ])
                ->json();

            $request['stripe_product_id'] = is_array($stripeProduct) && array_key_exists('id', $stripeProduct) ? $stripeProduct['id'] : null;
            $request['stripe_price_id'] = is_array($stripeProduct) && array_key_exists('default_price', $stripeProduct) ? $stripeProduct['default_price'] : null;
            $product = Product::create($request->all());

            if ($request->has('image')) {
                $product->addMediaFromRequest('image')->toMediaCollection('product-images');
            }

            DB::commit();

            return $this->successResponse(__('response.admin.products.success', ['actionType' => 'added']), $product, 201);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.admin.products.failed', ['actionType' => 'add']));
        }
    }

    /**
     * Get single Product
     *
     * Fetch the details about the given Product id.
     *
     * @urlParam id integer required The id of the Product. Example: 1
     *
     * @responseFile status=200 storage/responses/admin/products/fetch-single.json
     * @responseFile status=404 storage/responses/admin/products/not-found.json
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $columns = $this->getSelectColumns();
        $product = Product::select($columns)->find($id);
        if (! $product) {
            return $this->errorResponse(__('response.admin.products.not_found'), [], 404);
        }

        return $this->successResponse('', $product);
    }

    /**
     * Update Product
     *
     * Update the Product details of the given id.
     *
     * @urlParam id integer required The id of the Product. Example: 1
     *
     * @responseFile status=200 storage/responses/admin/products/updated.json
     * @responseFile status=404 storage/responses/admin/products/not-found.json
     * @responseFile status=422 storage/responses/admin/products/validation-errors.json
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, UpdateProductRequest $request)
    {
        $product = Product::find($id);
        if (! $product) {
            return $this->errorResponse(__('response.admin.products.not_found'), [], 404);
        }

        $existingRate = $product->rate;
        $newRate = (float) $request->rate;

        if ($newRate !== $existingRate) {
            $stripePrice = Http::withHeaders([
                'Authorization' => 'Bearer '.config('stripe.keys.secret'),
            ])
                ->asForm()
                ->post('https://api.stripe.com/v1/prices', [
                    'currency' => 'inr',
                    'product' => $product->stripe_product_id,
                    'unit_amount' => $request->rate * 100,
                ])
                ->json();

            $request['stripe_price_id'] = is_array($stripePrice) && array_key_exists('id', $stripePrice) ? $stripePrice['id'] : null;
        }

        DB::beginTransaction();

        try {
            $product->update($request->all());

            $product = $product->fresh();

            if ($request->has('image')) {
                $product->clearMediaCollection('product-images');
                $product->addMediaFromRequest('image')->toMediaCollection('product-images');
            }

            DB::commit();

            return $this->successResponse(__('response.admin.products.success', ['actionType' => 'updated']), $product);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.admin.products.failed', ['actionType' => 'update']));
        }
    }

    /**
     * Delete a Product
     *
     * Delete the Product details of the given id.
     * This will soft delete the Product.
     * Meaning the record will be present in the database, however,
     * it won't be available to access.
     *
     * @urlParam id integer required The id of the Product. Example: 1
     *
     * @responseFile status=200 storage/responses/admin/products/deleted.json
     * @responseFile status=404 storage/responses/admin/products/not-found.json
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if (! $product) {
            return $this->errorResponse(__('response.admin.products.not_found'), [], 404);
        }

        DB::beginTransaction();

        try {
            $product->delete();

            DB::commit();

            return $this->successResponse(__('response.admin.products.success', ['actionType' => 'deleted']));
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.admin.products.failed', ['actionType' => 'delete']));
        }
    }

    /**
     * Select the columns to be queried when fetching the products.
     *
     * @return array
     */
    public function getSelectColumns()
    {
        return [
            'id', 'category_id', 'brand_id', 'name', 'slug',
            'rate', 'quantity', 'description',
            'meta_title', 'meta_description', 'meta_keywords',
            'stripe_product_id', 'stripe_price_id',
        ];
    }
}
