<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\Tags\AddTagRequest;
use App\Http\Requests\v1\Admin\Tags\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

/**
 * @group Administrator Endpoints
 *
 * @subgroup Tags
 */
class TagsController extends Controller
{
    /**
     * List All tags
     *
     * Display all the tags with pagination.
     * At a time, there are total of 16 records that will be displayed.
     *
     * @queryParam page integer The page number. Defaults to 1. Example: 1
     *
     * @responseFile storage/responses/admin/tags/list-all.json
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $tags = Tag::select([
            'id', 'name', 'slug', 'description', 'meta_title', 'meta_description', 'meta_keywords',
        ])->paginate(16);

        return $this->successResponse('', $tags);
    }

    /**
     * Add new tag
     *
     * Create a new tag and store it's details.
     *
     * @responseFile status=201 storage/responses/admin/tags/created.json
     * @responseFile status=422 storage/responses/admin/tags/validation-errors.json
     *
     * @authenticated
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
     * Get single tag
     *
     * Fetch the details about the given tag id.
     *
     * @urlParam id integer required The id of the tag. Example: 1
     *
     * @responseFile status=200 storage/responses/admin/tags/fetch-single.json
     * @responseFile status=404 storage/responses/admin/tags/not-found.json
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $tag = Tag::select([
            'id', 'name', 'description', 'meta_title', 'meta_description', 'meta_keywords',
        ])->find($id);
        if (! $tag) {
            return $this->errorResponse('Tag not found.', [], 404);
        }

        return $this->successResponse('', $tag);
    }

    /**
     * Update tag
     *
     * Update the tag details of the given id.
     *
     * @urlParam id integer required The id of the tag. Example: 1
     *
     * @responseFile status=200 storage/responses/admin/tags/updated.json
     * @responseFile status=404 storage/responses/admin/tags/not-found.json
     * @responseFile status=422 storage/responses/admin/tags/validation-errors.json
     *
     * @authenticated
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
     * Delete a tag
     *
     * Delete the tag details of the given id.
     * This will soft delete the tag.
     * Meaning the record will be present in the database, however,
     * it won't be available to access.
     *
     * @urlParam id integer required The id of the tag. Example: 1
     *
     * @responseFile status=200 storage/responses/admin/tags/deleted.json
     * @responseFile status=404 storage/responses/admin/tags/not-found.json
     *
     * @authenticated
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
