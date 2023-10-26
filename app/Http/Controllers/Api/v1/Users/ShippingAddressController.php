<?php

namespace App\Http\Controllers\Api\v1\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Users\ShippingAddressRequest;
use Illuminate\Support\Facades\DB;

/**
 * @group User Endpoints
 *
 * @subgroup Shipping Address
 *
 * @subgroupDescription The shipping addresses of the user.
 */
class ShippingAddressController extends Controller
{
    /**
     * List all shipping addresses
     *
     * Displays all the shipping addresses that are added by the user.
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = auth('sanctum')->user();
        $addresses = $user->shippingAddress()->get();

        return $this->successResponse('', $addresses);
    }

    /**
     * Add new shipping address
     *
     * Store the new shipping address with the provided details.
     *
     * @responseFile status=200 storage/responses/users/shipping-address/created.json
     * @responseFile status=422 storage/responses/users/shipping-address/validation-errors.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ShippingAddressRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = auth('sanctum')->user();

            $address = $user->shippingAddress()->create([
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

            return $this->successResponse(__('response.user.address.success', ['type' => 'Shipping', 'action' => 'created']), $address);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.user.address.failed', ['type' => 'shipping', 'action' => 'create']));
        }
    }

    /**
     * Update shipping address
     *
     * Updates the existing shipping address of the provided id with the provided details.
     *
     * @urlParam id integer required The id of the shipping address. Example: 1
     *
     * @responseFile status=200 storage/responses/users/shipping-address/updated.json
     * @responseFile status=422 storage/responses/users/shipping-address/validation-errors.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, ShippingAddressRequest $request)
    {
        $user = auth('sanctum')->user();
        $address = $user->shippingAddress()->where('id', $id)->first();
        if (! $address) {
            return $this->errorResponse(__('response.user.address.not_found', ['type' => 'Shipping']), [], 404);
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

            return $this->successResponse(__('response.user.address.success', ['type' => 'Shipping', 'action' => 'updated']), $address->fresh());
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.user.address.failed', ['type' => 'shipping', 'action' => 'update']));
        }
    }

    /**
     * Delete shipping address
     *
     * Deletes the existing shipping address of the provided id.
     *
     * @urlParam id integer required The id of the shipping address. Example: 1
     *
     * @responseFile status=200 storage/responses/users/shipping-address/deleted.json
     * @responseFile status=404 storage/responses/users/shipping-address/not-found.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = auth('sanctum')->user();
        $address = $user->shippingAddress()->where('id', $id)->first();
        if (! $address) {
            return $this->errorResponse(__('response.user.address.not_found', ['type' => 'Shipping']), [], 404);
        }

        DB::beginTransaction();

        try {
            $address->delete();

            DB::commit();

            return $this->successResponse(__('response.user.address.success', ['type' => 'Shipping', 'action' => 'deleted']));
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.user.address.failed', ['type' => 'shipping', 'action' => 'delete']));
        }
    }
}
