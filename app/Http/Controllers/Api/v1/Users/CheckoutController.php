<?php

namespace App\Http\Controllers\Api\v1\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Users\CheckoutBillingRequest;
use App\Http\Requests\v1\Users\CheckoutShippingRequest;

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
}
