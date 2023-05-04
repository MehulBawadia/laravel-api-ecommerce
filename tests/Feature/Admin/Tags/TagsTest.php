<?php

namespace Tests\Feature\Admin\Tags;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagsTest extends TestCase
{
    use RefreshDatabase;

    public $getRoute = null;

    public $admin = null;

    public function setup(): void
    {
        parent::setUp();

        $this->admin = $this->signInAdmin();

        $this->getRoute = route('v1_admin.tags');
    }

    public function test_no_tags_exists()
    {
        $this->withoutExceptionHandling();

        $this->getJson($this->getRoute);

        $this->assertEquals(0, Tag::count());
    }

    public function test_tags_exists()
    {
        $this->withoutExceptionHandling();

        Tag::factory(10)->create();

        $this->assertEquals(10, Tag::count());
    }
}
