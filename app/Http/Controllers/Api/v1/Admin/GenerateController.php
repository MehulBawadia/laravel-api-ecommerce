<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\GenerateRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * @group Generate Administrator
 */
class GenerateController extends Controller
{
    /**
     * Generate the admin user
     *
     * API to generate the super administrator. It is only a one-time call
     * in the entire lifetime of the application.
     *
     * @unauthenticated
     *
     * @return void
     */
    public function store(GenerateRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = User::create($request->formPayload());

            DB::commit();

            return $this->successResponse(__('response.admin.generate.success'), $user, 201);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse(__('response.admin.generate.failed'));
        }
    }
}
