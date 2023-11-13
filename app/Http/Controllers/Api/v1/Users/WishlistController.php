<?php

namespace App\Http\Controllers\Api\v1\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Users\WishlistRequest;
use Illuminate\Support\Facades\DB;

/**
 * @group User Endpoints
 *
 * @subgroup Wishlist
 *
 * @subgroupDescription The wishlist of the user. They can add or remove the product to and from wishlist.
 */
class WishlistController extends Controller
{
    /**
     * List all products
     *
     * Displays all the products that are added by the user in their wishlist.
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = auth('sanctum')->user();
        $products = $user->productWishlist;

        return $this->successResponse('', $products);
    }

    /**
     * Add product
     *
     * Store an existing product in their wishlist.
     *
     * @responseFile status=200 storage/responses/users/wishlist/created.json
     * @responseFile status=422 storage/responses/users/wishlist/validation-errors.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(WishlistRequest $request)
    {
        $user = auth('sanctum')->user();

        if ($user->productWishlist()->where('product_id', $request->product_id)->exists()) {
            return $this->errorResponse(__('response.user.wishlist.product_exists'), [], 422);
        }

        DB::beginTransaction();

        try {
            $user->productWishlist()->create([
                'product_id' => $request->product_id,
            ]);

            DB::commit();

            return $this->successResponse(__('response.user.wishlist.success', ['action' => 'added']));
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.user.wishlist.failed', ['action' => 'add']));
        }
    }

    /**
     * Remove a product
     *
     * Removes the product of the provided id from the user's wishlist.
     *
     * @urlParam productId integer required The id of the product. Example: 1
     *
     * @responseFile status=200 storage/responses/users/wishlist/deleted.json
     * @responseFile status=404 storage/responses/users/wishlist/not-found.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($productId)
    {
        $user = auth('sanctum')->user();
        $productWishlist = $user->productWishlist()->where('product_id', $productId)->first();
        if (! $productWishlist) {
            return $this->errorResponse(__('response.user.wishlist.not_found', ['type' => 'Shipping']), [], 404);
        }

        DB::beginTransaction();

        try {
            $productWishlist->delete();

            DB::commit();

            return $this->successResponse(__('response.user.wishlist.success', ['action' => 'removed']));
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.user.wishlist.failed', ['action' => 'remove']));
        }
    }
}
