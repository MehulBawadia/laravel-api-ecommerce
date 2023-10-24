<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * @group Common Endpoints
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
     * @unauthenticated
     *
     * @return  \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->successResponse('', session('cart'));
    }

    /**
     * Add a product
     *
     * Adds the product of the given id in to the cart.
     * The product is then stored in the session.
     * If the product is not found, 404 error will be returned.
     *
     * @urlParam $productId integer required The id of the product that you want to add to the cart. Example: 1
     *
     * @unauthenticated
     *
     * @param  int  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($productId, Request $request)
    {
        $product = Product::find($productId);
        if (! $product) {
            return $this->errorResponse(__('response.cart.product_not_found'), [], 404);
        }

        $quantity = $request->quantity ?? 1;
        $cart = session('cart') ?? [];

        $cart['products'][$product->slug] = [
            'id' => $product->id,
            'quantity' => $quantity,
            'rate' => $product->rate,
            'total' => (float) ($product->rate * (int) $quantity),
            'name' => $product->name,
        ];

        session(['cart' => $cart]);

        $this->recalculateCartTotal();

        return $this->successResponse('Product added in the cart successfully.');
    }

    /**
     * Update product quantity
     *
     * Update the product's quantity of the given id in to the cart.
     * This will also update the cart total amount after updating.
     * If the product is not found, 404 error will be returned.
     *
     * @urlParam $productId integer required The id of the product that you want to update the quantity of. Example: 1
     *
     * @unauthenticated
     *
     * @param  int  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($productId, Request $request)
    {
        $product = Product::find($productId);
        if (! $product) {
            return $this->errorResponse(__('response.cart.product_not_found'), [], 404);
        }

        $quantity = $request->quantity ?? 1;

        session()->put("cart.products.$product->slug.quantity", (int) $quantity);
        session()->put("cart.products.$product->slug.total", (float) ($product->rate * $quantity));

        $this->recalculateCartTotal();

        return $this->successResponse(__('response.cart.product_updated'));
    }

    /**
     * Remove product
     *
     * Removes the product's of the given id from the cart.
     * This will also update the cart total amount after removal.
     * If the product is not found, 404 error will be returned.
     *
     * @urlParam $productId integer required The id of the product that you want to update the quantity of. Example: 1
     *
     * @unauthenticated
     *
     * @param  int  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($productId)
    {
        $product = Product::find($productId);
        if (! $product) {
            return $this->errorResponse(__('response.cart.product_not_found'), [], 404);
        }

        session()->forget("cart.products.$product->slug");

        $this->recalculateCartTotal();

        return $this->successResponse(__('response.cart.product_removed'));
    }

    /**
     * Empty cart
     *
     * Empties the cart entirely.
     *
     * @unauthenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function empty()
    {
        session()->forget('cart');

        return $this->successResponse(__('response.cart.empty'));
    }

    /**
     * Recalculate the cart total amount.
     *
     * @return void
     */
    private function recalculateCartTotal()
    {
        $allProductsTotal = 0.0;
        $cartTotal = 0.0;
        $cartProducts = session('cart.products');
        foreach ($cartProducts as $data) {
            $allProductsTotal += $data['total'];
        }
        $cartTotal += $allProductsTotal;

        $cart['total_products_amount'] = $allProductsTotal;
        $cart['total_cart_amount'] = $cartTotal;

        session()->put('cart.total_products_amount', $allProductsTotal);
        session()->put('cart.total_cart_amount', $cartTotal);
    }
}
