<?php

namespace Tests\Feature\Admin\Products;

use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ShowProductTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $getRoute = null;

    public $admin = null;

    public $product = null;

    public function setup(): void
    {
        parent::setUp();

        $this->admin = $this->signInAdmin();
        $this->product = Product::factory()->create(
            $this->preparePayload()
        );

        $this->getRoute = route('v1_admin.products.show', $this->product->id);
    }

    public function test_admin_can_fetch_the_Product_details()
    {
        $this->withoutExceptionHandling();

        $this->getJson($this->getRoute);
        $this->assertEquals(1, Product::count());

        $product = Product::first();
        $this->assertEquals($product->name, 'Random Product');
        $this->assertEquals($product->description, 'Random Product description goes here');
        $this->assertEquals($product->meta_title, 'Random Product meta title goes here');
        $this->assertEquals($product->meta_description, 'Random Product meta description goes here');
        $this->assertEquals($product->meta_keywords, 'random Product, meta keywords, goes here');
    }

    public function test_admin_receives_404_error_if_Product_not_found()
    {
        $this->withoutExceptionHandling();

        $response = $this->getJson(route('v1_admin.products.show', 10));
        $response->assertStatus(404);
        $response->assertNotFound();
    }

    protected function preparePayload($data = [])
    {
        return array_merge([
            'name' => 'Random Product',
            'description' => 'Random Product description goes here',
            'meta_title' => 'Random Product meta title goes here',
            'meta_description' => 'Random Product meta description goes here',
            'meta_keywords' => 'random Product, meta keywords, goes here',
        ], $data);
    }
}
