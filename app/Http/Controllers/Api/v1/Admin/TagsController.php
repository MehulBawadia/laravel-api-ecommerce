<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\Tags\AddTagRequest;
use App\Http\Requests\v1\Admin\Tags\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class TagsController extends Controller
{
    /**
     * Get and paginate the tags.
     *
     * @return void
     */
    public function index()
    {
        $tags = Tag::select([
            'id', 'name', 'slug', 'meta_title', 'meta_description', 'meta_keywords',
        ])->paginate(16);

        return $this->successResponse('', $tags);
    }

    /**
     * Store a new Tag.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AddTagRequest $request)
    {
        DB::beginTransaction();

        try {
            $tag = Tag::create($request->all());

            DB::commit();

            return $this->successResponse('Tag added successfully.', $tag, 201);
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse('Could not add tag.');
        }
    }

    /**
     * Fetch the details about the given tag id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $tag = Tag::select([
            'id', 'name', 'meta_title', 'meta_description', 'meta_keywords',
        ])->find($id);
        if (! $tag) {
            return $this->errorResponse('Tag not found.', [], 404);
        }

        return $this->successResponse('', $tag);
    }

    /**
     * Update the tag details of the given id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, UpdateTagRequest $request)
    {
        $tag = Tag::find($id);
        if (! $tag) {
            return $this->errorResponse('Tag not found.', [], 404);
        }

        DB::beginTransaction();

        try {
            $tag->update($request->all());

            DB::commit();

            return $this->successResponse('Tag updated successfully.', $tag->fresh());
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse('Could not update tag.');
        }
    }

    /**
     * Delete the tag details of the given id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $tag = Tag::find($id);
        if (! $tag) {
            return $this->errorResponse('Tag not found.', [], 404);
        }

        DB::beginTransaction();

        try {
            $tag->delete();

            DB::commit();

            return $this->successResponse('Tag deleted successfully.');
        } catch (\Exception $e) {
            info($e->getMessage());
            info($e->getTraceAsString());

            DB::rollBack();

            return $this->errorResponse('Could not delete tag.');
        }
    }
}
