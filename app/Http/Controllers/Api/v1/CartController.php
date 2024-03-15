<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\CartProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group User Endpoints
 *
 * @subgroup Cart
 *
 * @subgroupDescription  The endpoints related to Cart
 */
class CartController extends Controller
{
    /**
     * All products
     *
     * Lists all the products that are added in the cart.
     * Also, it will calculate the total amount of the cart.
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->successResponse('', auth('sanctum')->user()->cartProducts);
    }

    /**
     * Add a product
     *
     * Adds the product of the given id in to the cart.
     * The product is then stored in the database.
     * If the product is not found, 404 error will be returned.
     *
     * @responseFile status=201 storage/responses/users/cart/product-added.json
     * @responseFile status=404 storage/responses/users/cart/product-not-found.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $product = Product::find($request->product_id);
        if (! $product) {
            return $this->errorResponse(__('response.cart.product_not_found'), null, 404);
        }

        DB::beginTransaction();

        try {
            $quantity = $request->quantity ?? 1;

            $user = auth('sanctum')->user();
            $cart = $user->addProductInCart($product, $quantity);

            DB::commit();

            return $this->successResponse(__('response.cart.success', ['actionType' => 'added']), $cart->fresh(), 201);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.cart.failed', ['actionType' => 'add']));
        }
    }

    /**
     * Update product quantity
     *
     * Update the product's quantity of the given cartProduct id.
     * If the record is not found, 404 error will be returned.
     *
     * @urlParam $cartProductId integer required The id of the cartProduct that you want to update the quantity of. Example: 1
     *
     * @responseFile status=200 storage/responses/users/cart/product-updated.json
     * @responseFile status=404 storage/responses/users/cart/product-not-found.json
     *
     * @authenticated
     *
     * @param  int  $cartProductId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($cartProductId, Request $request)
    {
        $cart = auth('sanctum')->user()->cartProducts()->find($cartProductId);
        if (! $cart) {
            return $this->errorResponse(__('response.cart.product_not_found'), null, 404);
        }

        DB::beginTransaction();

        try {
            $cart->update([
                'quantity' => $request->quantity ?? 1,
            ]);

            DB::commit();

            return $this->successResponse(__('response.cart.success', ['actionType' => 'updated']), $cart->fresh());
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.cart.failed', ['actionType' => 'update']));
        }
    }

    /**
     * Remove product
     *
     * Removes the product of the given id from the cart.
     * If the product is not found, 404 error will be returned.
     *
     * @urlParam $cartProductId integer required The id of the product that you want to update the quantity of. Example: 1
     *
     * @responseFile status=200 storage/responses/users/cart/product-removed.json
     * @responseFile status=404 storage/responses/users/cart/product-not-found.json
     *
     * @authenticated
     *
     * @param  int  $cartProductId
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($cartProductId)
    {
        $cart = auth('sanctum')->user()->cartProducts()->find($cartProductId);
        if (! $cart) {
            return $this->errorResponse(__('response.cart.product_not_found'), null, 404);
        }

        DB::beginTransaction();

        try {
            $cart->delete();

            DB::commit();

            return $this->successResponse(__('response.cart.success', ['actionType' => 'removed']), $cart->fresh());
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.cart.failed', ['actionType' => 'remove']));
        }
    }

    /**
     * Empty cart
     *
     * Empties the cart entirely.
     *
     * @responseFile status=200 storage/responses/users/cart/emptied.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function empty()
    {
        DB::beginTransaction();

        try {
            auth('sanctum')->user()->cartProducts()->delete();

            DB::commit();

            return $this->successResponse(__('response.cart.empty'), null);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.cart.failed', ['actionType' => 'empty']));
        }
    }
}
