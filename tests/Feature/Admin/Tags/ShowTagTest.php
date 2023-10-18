<?php

namespace Tests\Feature\Admin\Tags;

use App\Models\Tag;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ShowTagTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $getRoute = null;

    public $admin = null;

    public $tag = null;

    public function setup(): void
    {
        parent::setUp();

        $this->admin = $this->signInAdmin();
        $this->tag = Tag::factory()->create(
            $this->preparePayload()
        );

        $this->getRoute = route('v1_admin.tags.show', $this->tag->id);
    }

    public function test_admin_can_fetch_the_Tag_details()
    {
        $this->withoutExceptionHandling();

        $this->getJson($this->getRoute);
        $this->assertEquals(1, Tag::count());

        $tag = Tag::first();
        $this->assertEquals($tag->name, 'Random Tag');
        $this->assertEquals($tag->description, 'Random Tag description goes here');
        $this->assertEquals($tag->meta_title, 'Random Tag meta title goes here');
        $this->assertEquals($tag->meta_description, 'Random Tag meta description goes here');
        $this->assertEquals($tag->meta_keywords, 'random Tag, meta keywords, goes here');
    }

    public function test_admin_receives_404_error_if_Tag_not_found()
    {
        $this->withoutExceptionHandling();

        $response = $this->getJson(route('v1_admin.tags.show', 10));
        $response->assertStatus(404);
        $response->assertNotFound();
    }

    protected function preparePayload($data = [])
    {
        return array_merge([
            'name' => 'Random Tag',
            'description' => 'Random Tag description goes here',
            'meta_title' => 'Random Tag meta title goes here',
            'meta_description' => 'Random Tag meta description goes here',
            'meta_keywords' => 'random Tag, meta keywords, goes here',
        ], $data);
    }
}
