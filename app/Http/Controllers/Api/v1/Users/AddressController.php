<?php

namespace App\Http\Controllers\Api\v1\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Users\BillingAddressRequest;
use App\Http\Requests\v1\Users\ShippingAddressRequest;
use Illuminate\Support\Facades\DB;

/**
 * @group User Endpoints
 *
 * @subgroup Addresses
 */
class AddressController extends Controller
{
    /**
     * Billing Addresses
     *
     * API to update the billing address of the user.
     *
     * @responseFile status=200 storage/responses/users/addresses/billing-success.json
     * @responseFile status=401 storage/responses/users/addresses/invalid-credentials.json
     * @responseFile status=422 storage/responses/users/addresses/billing-validation-errors.json
     *
     * @authenticated
     *
     * @return void
     */
    public function updateBilling(BillingAddressRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = auth('sanctum')->user();

            $user->address->update([
                'billing_first_name' => $request->billing_first_name,
                'billing_last_name' => $request->billing_last_name,
                'billing_email' => $request->billing_email,
                'billing_contact' => $request->billing_contact,
                'billing_address_line_1' => $request->billing_address_line_1,
                'billing_address_line_2' => $request->billing_address_line_2,
                'billing_area' => $request->billing_area,
                'billing_landmark' => $request->billing_landmark,
                'billing_city' => $request->billing_city,
                'billing_postal_code' => $request->billing_postal_code,
                'billing_state_province' => $request->billing_state_province,
                'billing_country' => $request->billing_country,
            ]);

            DB::commit();

            return $this->successResponse(__('response.user.address', ['addressType' => 'Billing']), $user->address->fresh());
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.user.address.failed', ['addressType' => 'billing']));
        }
    }

    /**
     * Shipping Addresses
     *
     * API to update the shipping address of the user.
     *
     * @responseFile status=200 storage/responses/users/addresses/shipping-success.json
     * @responseFile status=401 storage/responses/users/addresses/invalid-credentials.json
     * @responseFile status=422 storage/responses/users/addresses/shipping-validation-errors.json
     *
     * @authenticated
     *
     * @return void
     */
    public function updateShipping(ShippingAddressRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = auth('sanctum')->user();

            $user->address->update([
                'shipping_first_name' => $request->shipping_first_name,
                'shipping_last_name' => $request->shipping_last_name,
                'shipping_email' => $request->shipping_email,
                'shipping_contact' => $request->shipping_contact,
                'shipping_address_line_1' => $request->shipping_address_line_1,
                'shipping_address_line_2' => $request->shipping_address_line_2,
                'shipping_area' => $request->shipping_area,
                'shipping_landmark' => $request->shipping_landmark,
                'shipping_city' => $request->shipping_city,
                'shipping_postal_code' => $request->shipping_postal_code,
                'shipping_state_province' => $request->shipping_state_province,
                'shipping_country' => $request->shipping_country,
            ]);

            DB::commit();

            return $this->successResponse(__('response.user.address', ['addressType' => 'Shipping']), $user->address->fresh());
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.user.address.failed', ['addressType' => 'shipping']));
        }
    }
}
