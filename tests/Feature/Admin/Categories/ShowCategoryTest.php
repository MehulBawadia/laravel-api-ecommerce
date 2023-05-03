<?php

namespace Tests\Feature\Admin\Categories;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowCategoryTest extends TestCase
{
    use RefreshDatabase;

    public $getRoute = null;

    public $admin = null;

    public $category = null;

    public function setup(): void
    {
        parent::setUp();

        $this->admin = $this->signInAdmin(['first_name' => 'Super', 'last_name' => 'Administrator']);
        $this->category = Category::factory()->create(
            $this->preparePayload()
        );

        $this->getRoute = route('v1_admin.categories.show', $this->category->id);
    }

    public function test_admin_can_fetch_the_category_details()
    {
        $this->withoutExceptionHandling();

        $this->getJson($this->getRoute);
        $this->assertEquals(1, Category::count());

        $category = Category::first();
        $this->assertEquals($category->name, 'Random category');
        $this->assertEquals($category->description, 'Random category description goes here');
        $this->assertEquals($category->meta_title, 'Random category meta title goes here');
        $this->assertEquals($category->meta_description, 'Random category meta description goes here');
        $this->assertEquals($category->meta_keywords, 'random category, meta keywords, goes here');
    }

    public function test_admin_receives_404_error_if_category_not_found()
    {
        $this->withoutExceptionHandling();

        $response = $this->getJson(route('v1_admin.categories.show', 10));
        $response->assertStatus(404);
        $response->assertNotFound();
    }

    protected function preparePayload($data = [])
    {
        return array_merge([
            'name' => 'Random category',
            'description' => 'Random category description goes here',
            'meta_title' => 'Random category meta title goes here',
            'meta_description' => 'Random category meta description goes here',
            'meta_keywords' => 'random category, meta keywords, goes here',
        ], $data);
    }
}
