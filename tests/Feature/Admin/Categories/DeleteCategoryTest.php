<?php

namespace Tests\Feature\Admin\Categories;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteCategoryTest extends TestCase
{
    use RefreshDatabase;

    public $deleteRoute = null;

    public $admin = null;

    public $category = null;

    public function setup(): void
    {
        parent::setUp();

        $this->admin = $this->signInAdmin(['first_name' => 'Super', 'last_name' => 'Administrator']);
        $this->category = Category::factory()->create(
            $this->preparePayload()
        );

        $this->deleteRoute = route('v1_admin.categories.destroy', $this->category->id);
    }

    public function test_category_exists()
    {
        $this->withoutExceptionHandling();
        $this->assertEquals(1, Category::count());
    }

    public function test_admin_can_delete_the_category()
    {
        $this->withoutExceptionHandling();

        $this->assertNull($this->category->deleted_at);

        $response = $this->deleteJson($this->deleteRoute);

        $response->assertSeeText('Category deleted successfully.');
        $this->assertCount(0, Category::all());
        $this->assertEquals(1, Category::withTrashed()->count());
        $this->assertNotNull(Category::onlyTrashed()->first()->deleted_at);
    }

    public function test_admin_receives_404_error_if_category_not_found()
    {
        $this->withoutExceptionHandling();

        $this->assertNull($this->category->deleted_at);

        $response = $this->deleteJson(route('v1_admin.categories.destroy', 10));
        $response->assertStatus(404);
        $response->assertNotFound();

        $this->assertCount(1, Category::all());
        $this->assertEquals(0, Category::onlyTrashed()->count());
        $this->assertNull($this->category->deleted_at);
    }

    protected function preparePayload($data = [])
    {
        return array_merge([
            'name' => 'Category 1',
            'description' => 'Deserunt sint proident cillum aute est exercitation commodo duis minim commodo magna.',
            'meta_title' => 'Non nostrud fugiat magna magna dolore minim sint pariatur eu proident laborum.',
            'meta_description' => 'Laborum veniam culpa quis in exercitation officia fugiat sit id deserunt sunt.',
            'meta_keywords' => 'Id mollit aliquip reprehenderit culpa aliquip amet nisi consequat mollit ullamco.',
        ], $data);
    }
}
