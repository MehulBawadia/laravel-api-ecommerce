<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\GenerateRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GenerateController extends Controller
{
    /**
     * Genreate the administrator
     *
     * @return void
     */
    public function store(GenerateRequest $request)
    {
        DB::beginTransaction();

        try {
            $request['password'] = bcrypt($request->password);
            $user = User::create($request->all());

            DB::commit();

            return $this->successResponse('Administrator generated successfully.', $user, 201);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse('Could not create administrator.');
        }
    }
}
