<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\ChangePasswordRequest;
use App\Http\Requests\v1\Admin\GeneralSettingsRequest;
use Illuminate\Support\Facades\DB;

/**
 * @group Administrator Endpoints
 *
 * @subgroup Account Settings
 *
 * @subgroupDescription The endpoints related to the administrator account.
 */
class AccountSettingsController extends Controller
{
    /**
     * General Settings
     *
     * Update the general settings of the admin user.
     *
     * @bodyParam first_name string required The first name of the admin user. Example: John
     * @bodyParam last_name string required The last name of the admin user. Example: Doe
     * @bodyParam email string required The email of the admin user. Example: johndoe@example.com
     *
     * @responseFile status=201 storage/responses/admin/general-settings-success.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function general(GeneralSettingsRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = auth('sanctum')->user();

            $user->update($request->all());

            DB::commit();

            return $this->successResponse('General Settings updated successfully.', $user->fresh(), 201);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse('Could not update general settings.');
        }
    }

    /**
     * Change Password
     *
     * Update the password of the currently authenticated admin user.
     * It will be updated only when the current password is matched.
     *
     * @bodyParam current_password string required The admin user's current password. Example: Pa$$w0rd
     * @bodyParam new_password string required The new password. Example: Secret
     * @bodyParam new_password_confirmation string required Confirm the new password, should match new_password. Example: Secret
     *
     * @responseFile status=201 storage/responses/admin/change-password-success.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = auth('sanctum')->user();

            $user->update([
                'password' => bcrypt($request->new_password),
            ]);

            DB::commit();

            return $this->successResponse('Password updated successfully.', [], 201);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse('Could not update password.');
        }
    }
}
