<?php

namespace Tests\Feature\Admin\Categories;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    public $getRoute = null;

    public $admin = null;

    public function setup(): void
    {
        parent::setUp();

        $this->admin = $this->signInAdmin(['first_name' => 'Super', 'last_name' => 'Administrator']);

        $this->getRoute = route('v1_admin.categories');
    }

    public function test_no_categories_exists()
    {
        $this->withoutExceptionHandling();

        $this->getJson($this->getRoute);

        $this->assertEquals(0, Category::count());
    }

    public function test_categories_exists()
    {
        $this->withoutExceptionHandling();

        Category::factory(10)->create();

        $this->assertEquals(10, Category::count());
    }
}
