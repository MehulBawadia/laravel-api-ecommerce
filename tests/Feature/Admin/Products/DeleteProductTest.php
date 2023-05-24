<?php

namespace Tests\Feature\Admin\Products;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteProductTest extends TestCase
{
    use RefreshDatabase;

    public $deleteRoute = null;

    public $admin = null;

    public $product = null;

    public function setup(): void
    {
        parent::setUp();

        $this->admin = $this->signInAdmin();
        $this->product = Product::factory()->create(
            $this->preparePayload()
        );

        $this->deleteRoute = route('v1_admin.products.destroy', $this->product->id);
    }

    public function test_Product_exists()
    {
        $this->withoutExceptionHandling();
        $this->assertEquals(1, Product::count());
    }

    public function test_admin_can_delete_the_Product()
    {
        $this->withoutExceptionHandling();

        $this->assertNull($this->product->deleted_at);

        $response = $this->deleteJson($this->deleteRoute);

        $response->assertSeeText('Product deleted successfully.');
        $this->assertCount(0, Product::all());
        $this->assertEquals(1, Product::withTrashed()->count());
        $this->assertNotNull(Product::onlyTrashed()->first()->deleted_at);
    }

    public function test_admin_receives_404_error_if_Product_not_found()
    {
        $this->withoutExceptionHandling();

        $this->assertNull($this->product->deleted_at);

        $response = $this->deleteJson(route('v1_admin.products.destroy', 10));
        $response->assertStatus(404);
        $response->assertNotFound();

        $this->assertCount(1, Product::all());
        $this->assertEquals(0, Product::onlyTrashed()->count());
        $this->assertNull($this->product->deleted_at);
    }

    protected function preparePayload($data = [])
    {
        return array_merge([
            'name' => 'Product 1',
            'description' => 'Deserunt sint proident cillum aute est exercitation commodo duis minim commodo magna.',
            'meta_title' => 'Non nostrud fugiat magna magna dolore minim sint pariatur eu proident laborum.',
            'meta_description' => 'Laborum veniam culpa quis in exercitation officia fugiat sit id deserunt sunt.',
            'meta_keywords' => 'Id mollit aliquip reprehenderit culpa aliquip amet nisi consequat mollit ullamco.',
        ], $data);
    }
}
