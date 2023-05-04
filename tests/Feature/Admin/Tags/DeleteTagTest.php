<?php

namespace Tests\Feature\Admin\Tags;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTagTest extends TestCase
{
    use RefreshDatabase;

    public $deleteRoute = null;

    public $admin = null;

    public $tag = null;

    public function setup(): void
    {
        parent::setUp();

        $this->admin = $this->signInAdmin();
        $this->tag = Tag::factory()->create(
            $this->preparePayload()
        );

        $this->deleteRoute = route('v1_admin.tags.destroy', $this->tag->id);
    }

    public function test_Tag_exists()
    {
        $this->withoutExceptionHandling();
        $this->assertEquals(1, Tag::count());
    }

    public function test_admin_can_delete_the_Tag()
    {
        $this->withoutExceptionHandling();

        $this->assertNull($this->tag->deleted_at);

        $response = $this->deleteJson($this->deleteRoute);

        $response->assertSeeText('Tag deleted successfully.');
        $this->assertCount(0, Tag::all());
        $this->assertEquals(1, Tag::withTrashed()->count());
        $this->assertNotNull(Tag::onlyTrashed()->first()->deleted_at);
    }

    public function test_admin_receives_404_error_if_Tag_not_found()
    {
        $this->withoutExceptionHandling();

        $this->assertNull($this->tag->deleted_at);

        $response = $this->deleteJson(route('v1_admin.tags.destroy', 10));
        $response->assertStatus(404);
        $response->assertNotFound();

        $this->assertCount(1, Tag::all());
        $this->assertEquals(0, Tag::onlyTrashed()->count());
        $this->assertNull($this->tag->deleted_at);
    }

    protected function preparePayload($data = [])
    {
        return array_merge([
            'name' => 'Tag 1',
            'description' => 'Deserunt sint proident cillum aute est exercitation commodo duis minim commodo magna.',
            'meta_title' => 'Non nostrud fugiat magna magna dolore minim sint pariatur eu proident laborum.',
            'meta_description' => 'Laborum veniam culpa quis in exercitation officia fugiat sit id deserunt sunt.',
            'meta_keywords' => 'Id mollit aliquip reprehenderit culpa aliquip amet nisi consequat mollit ullamco.',
        ], $data);
    }
}
