<?php

namespace Tests\Feature\Admin\Brands;

use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowBrandTest extends TestCase
{
    use RefreshDatabase;

    public $getRoute = null;

    public $admin = null;

    public $brand = null;

    public function setup(): void
    {
        parent::setUp();

        $this->admin = $this->signInAdmin();
        $this->brand = Brand::factory()->create(
            $this->preparePayload()
        );

        $this->getRoute = route('v1_admin.brands.show', $this->brand->id);
    }

    public function test_admin_can_fetch_the_Brand_details()
    {
        $this->withoutExceptionHandling();

        $this->getJson($this->getRoute);
        $this->assertEquals(1, Brand::count());

        $brand = Brand::first();
        $this->assertEquals($brand->name, 'Random Brand');
        $this->assertEquals($brand->description, 'Random Brand description goes here');
        $this->assertEquals($brand->meta_title, 'Random Brand meta title goes here');
        $this->assertEquals($brand->meta_description, 'Random Brand meta description goes here');
        $this->assertEquals($brand->meta_keywords, 'random Brand, meta keywords, goes here');
    }

    public function test_admin_receives_404_error_if_Brand_not_found()
    {
        $this->withoutExceptionHandling();

        $response = $this->getJson(route('v1_admin.brands.show', 10));
        $response->assertStatus(404);
        $response->assertNotFound();
    }

    protected function preparePayload($data = [])
    {
        return array_merge([
            'name' => 'Random Brand',
            'description' => 'Random Brand description goes here',
            'meta_title' => 'Random Brand meta title goes here',
            'meta_description' => 'Random Brand meta description goes here',
            'meta_keywords' => 'random Brand, meta keywords, goes here',
        ], $data);
    }
}
