<?php

namespace App\Http\Controllers\Api\v1\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Users\CheckoutBillingRequest;
use App\Http\Requests\v1\Users\CheckoutShippingRequest;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

/**
 * @group User Endpoints
 *
 * @subgroup Checkout
 *
 * @subgroupDescription The checkout order process. The values are stored in the session.
 */
class CheckoutController extends Controller
{
    /**
     * Both addresses
     *
     * Fetch all the billing and shipping addresses.
     *
     * @authenticated
     */
    public function addresses()
    {
        $user = auth('sanctum')->user();

        return $this->successResponse('', [
            'billing_address' => $user->billingAddress,
            'shipping_address' => $user->shippingAddress,
        ]);
    }

    /**
     * Billing Address
     *
     * Store the billing address in the session.
     *
     * @responseFile status=200 storage/responses/users/checkout/billing-created.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function billingAddress(CheckoutBillingRequest $request)
    {
        session()->forget('user_checkout_billing');

        $user = auth('sanctum')->user();
        $address = $user->billingAddress()->where('id', $request->billing_address_id)->first();
        if (! $address) {
            return $this->errorResponse(__('response.user.checkout_address.not_found', ['type' => 'Billing']), [], 404);
        }

        session(['user_checkout_billing' => $address]);

        return $this->successResponse(__('response.user.checkout_address.success', ['type' => 'Billing']));
    }

    /**
     * Shipping Address
     *
     * Store the shipping address in the session.
     *
     * @responseFile status=200 storage/responses/users/checkout/shipping-created.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function shippingAddress(CheckoutShippingRequest $request)
    {
        session()->forget('user_checkout_shipping');

        $user = auth('sanctum')->user();
        $address = $user->shippingAddress()->where('id', $request->shipping_address_id)->first();
        if (! $address) {
            return $this->errorResponse(__('response.user.checkout_address.not_found', ['type' => 'Shipping']), [], 404);
        }

        session(['user_checkout_shipping' => $address]);

        return $this->successResponse(__('response.user.checkout_address.success', ['type' => 'Shipping']));
    }

    /**
     * Place Order
     *
     * Create a new order
     *
     * @responseFile status=200 storage/responses/users/checkout/order-created.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function placeOrder()
    {
        if (auth('sanctum')->user()->cartProducts->isEmpty()) {
            return $this->errorResponse('Add products in the cart.', [], 403);
        }

        $user = auth('sanctum')->user();

        $products = auth('sanctum')->user()->cartProducts;
        $code = \Illuminate\Support\Str::random(5);

        DB::beginTransaction();

        try {
            $order = Order::create([
                'code' => "ORD-{$code}",
                'stripe_customer_id' => $user->stripe_customer_id,
                'user_id' => $user->id,
                'user_details' => json_encode([
                    'full_name' => "$user->first_name $user->last_name",
                    'email' => $user->email,
                ]),
                'billing_address' => json_encode(session('user_checkout_billing')),
                'shipping_address' => json_encode(session('user_checkout_shipping')),
                'total_amount' => $products->sum('amount'),
            ]);

            foreach ($products as $product) {
                $order->products()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'name' => $product->product_name,
                    'slug' => $product->product_slug,
                    'quantity' => $product->quantity,
                    'rate' => $product->rate,
                    'total' => (float) ($product->rate * (int) $product->quantity),
                ]);
            }

            DB::commit();

            return $this->successResponse(__('response.user.order_placed.success'), $order, 201);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.user.order_placed.failed'));
        }
    }
}
