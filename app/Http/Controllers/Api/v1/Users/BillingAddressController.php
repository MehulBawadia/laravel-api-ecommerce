<?php

namespace App\Http\Controllers\Api\v1\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Users\BillingAddressRequest;
use Illuminate\Support\Facades\DB;

/**
 * @group User Endpoints
 *
 * @subgroup Billing Address
 *
 * @subgroupDescription The billing addresses of the user.
 */
class BillingAddressController extends Controller
{
    /**
     * List all billing addresses
     *
     * Displays all the billing addresses that are added by the user.
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = auth('sanctum')->user();
        $addresses = $user->billingAddress()->get();

        return $this->successResponse('', $addresses);
    }

    /**
     * Add new billing address
     *
     * Store the new billing address with the provided details.
     *
     * @responseFile status=200 storage/responses/users/billing-address/created.json
     * @responseFile status=422 storage/responses/users/billing-address/validation-errors.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BillingAddressRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = auth('sanctum')->user();

            $address = $user->billingAddress()->create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contact' => $request->contact,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'area' => $request->area,
                'landmark' => $request->landmark,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'state_province' => $request->state_province,
                'country' => $request->country,
            ]);

            DB::commit();

            return $this->successResponse(__('response.user.address.success', ['type' => 'Billing', 'action' => 'created']), $address);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.user.address.failed', ['type' => 'billing', 'action' => 'create']));
        }
    }

    /**
     * Update billing address
     *
     * Updates the existing billing address of the provided id with the provided details.
     *
     * @urlParam id integer required The id of the billing address. Example: 1
     *
     * @responseFile status=200 storage/responses/users/billing-address/updated.json
     * @responseFile status=422 storage/responses/users/billing-address/validation-errors.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, BillingAddressRequest $request)
    {
        $user = auth('sanctum')->user();
        $address = $user->billingAddress()->where('id', $id)->first();
        if (! $address) {
            return $this->errorResponse(__('response.user.address.not_found', ['type' => 'Billing']), [], 404);
        }

        DB::beginTransaction();

        try {
            $address->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contact' => $request->contact,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'area' => $request->area,
                'landmark' => $request->landmark,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'state_province' => $request->state_province,
                'country' => $request->country,
            ]);

            DB::commit();

            return $this->successResponse(__('response.user.address.success', ['type' => 'Billing', 'action' => 'updated']), $address->fresh());
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.user.address.failed', ['type' => 'billing', 'action' => 'update']));
        }
    }

    /**
     * Delete billing address
     *
     * Deletes the existing billing address of the provided id.
     *
     * @urlParam id integer required The id of the billing address. Example: 1
     *
     * @responseFile status=200 storage/responses/users/billing-address/deleted.json
     * @responseFile status=404 storage/responses/users/billing-address/not-found.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = auth('sanctum')->user();
        $address = $user->billingAddress()->where('id', $id)->first();
        if (! $address) {
            return $this->errorResponse(__('response.user.address.not_found', ['type' => 'Billing']), [], 404);
        }

        DB::beginTransaction();

        try {
            $address->delete();

            DB::commit();

            return $this->successResponse(__('response.user.address.success', ['type' => 'Billing', 'action' => 'deleted']));
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.user.address.failed', ['type' => 'billing', 'action' => 'delete']));
        }
    }
}
