<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\GeneralSettingsRequest;
use Illuminate\Support\Facades\DB;

class AccountSettingsController extends Controller
{
    /**
     * Update the general settings of the admin user.
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
}
