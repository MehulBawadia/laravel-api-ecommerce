<?php

namespace Tests\Feature\Admin\Brands;

use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteBrandTest extends TestCase
{
    use RefreshDatabase;

    public $deleteRoute = null;

    public $admin = null;

    public $brand = null;

    public function setup(): void
    {
        parent::setUp();

        $this->admin = $this->signInAdmin();
        $this->brand = Brand::factory()->create(
            $this->preparePayload()
        );

        $this->deleteRoute = route('v1_admin.brands.destroy', $this->brand->id);
    }

    public function test_Brand_exists()
    {
        $this->withoutExceptionHandling();
        $this->assertEquals(1, Brand::count());
    }

    public function test_admin_can_delete_the_Brand()
    {
        $this->withoutExceptionHandling();

        $this->assertNull($this->brand->deleted_at);

        $response = $this->deleteJson($this->deleteRoute);

        $response->assertSeeText('Brand deleted successfully.');
        $this->assertCount(0, Brand::all());
        $this->assertEquals(1, Brand::withTrashed()->count());
        $this->assertNotNull(Brand::onlyTrashed()->first()->deleted_at);
    }

    public function test_admin_receives_404_error_if_Brand_not_found()
    {
        $this->withoutExceptionHandling();

        $this->assertNull($this->brand->deleted_at);

        $response = $this->deleteJson(route('v1_admin.brands.destroy', 10));
        $response->assertStatus(404);
        $response->assertNotFound();

        $this->assertCount(1, Brand::all());
        $this->assertEquals(0, Brand::onlyTrashed()->count());
        $this->assertNull($this->brand->deleted_at);
    }

    protected function preparePayload($data = [])
    {
        return array_merge([
            'name' => 'Brand 1',
            'description' => 'Deserunt sint proident cillum aute est exercitation commodo duis minim commodo magna.',
            'meta_title' => 'Non nostrud fugiat magna magna dolore minim sint pariatur eu proident laborum.',
            'meta_description' => 'Laborum veniam culpa quis in exercitation officia fugiat sit id deserunt sunt.',
            'meta_keywords' => 'Id mollit aliquip reprehenderit culpa aliquip amet nisi consequat mollit ullamco.',
        ], $data);
    }
}
