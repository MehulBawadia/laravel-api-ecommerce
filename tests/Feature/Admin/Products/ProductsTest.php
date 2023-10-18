<?php

namespace Tests\Feature\Admin\Products;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $getRoute = null;

    public $admin = null;

    public function setup(): void
    {
        parent::setUp();

        $this->admin = $this->signInAdmin();

        $this->getRoute = route('v1_admin.products');
    }

    public function test_no_products_exists()
    {
        $this->withoutExceptionHandling();

        $this->getJson($this->getRoute);

        $this->assertEquals(0, Product::count());
    }

    public function test_products_exists()
    {
        $this->withoutExceptionHandling();

        Product::factory(10)->create();

        $this->assertEquals(10, Product::count());
    }

    public function test_returns_products_results()
    {
        $this->withoutExceptionHandling();

        Product::factory([
            'id' => 1,
            'name' => 'Sint Rerum Officiis',
            'description' => 'Repudiandae non quas minima odit molestiae id libero ab. Sit veritatis illo cum deleniti culpa officiis libero hic. Voluptates eius harum in et non quidem. Magni adipisci qui eum ab. Et dignissimos nemo adipisci dolores suscipit.',
            'meta_title' => 'Sint Rerum Officiis Laravel E-Commerce API',
            'meta_description' => 'Sint Rerum Officiis Laravel E-Commerce API',
            'meta_keywords' => 'Sint Rerum Officiis Laravel E-Commerce API',
            'rate' => 100,
            'quantity' => 10,
        ])
            ->for(Category::factory(['name' => 'Category 1'])->create())
            ->for(Brand::factory(['name' => 'Brand 1'])->create())
            ->create();

        $response = $this->getJson($this->getRoute);

        $response->assertJson([
            'status' => 'success',
            'message' => '',
            'data' => [
                'current_page' => 1,
                'data' => [
                    0 => [
                        'id' => 1,
                        'category_id' => 1,
                        'brand_id' => 1,
                        'name' => 'Sint Rerum Officiis',
                        'slug' => 'sint-rerum-officiis',
                        'rate' => 100,
                        'quantity' => 10,
                        'description' => 'Repudiandae non quas minima odit molestiae id libero ab. Sit veritatis illo cum deleniti culpa officiis libero hic. Voluptates eius harum in et non quidem. Magni adipisci qui eum ab. Et dignissimos nemo adipisci dolores suscipit.',
                        'meta_title' => 'Sint Rerum Officiis Laravel E-Commerce API',
                        'meta_description' => 'Sint Rerum Officiis Laravel E-Commerce API',
                        'meta_keywords' => 'Sint Rerum Officiis Laravel E-Commerce API',
                        'category' => [
                            'id' => 1,
                            'name' => 'Category 1',
                            'slug' => 'category-1',
                        ],
                        'brand' => [
                            'id' => 1,
                            'name' => 'Brand 1',
                            'slug' => 'brand-1',
                        ],
                    ],
                ],
                'first_page_url' => 'http://localhost:8000/api/v1/admin/products?page=1',
                'from' => 1,
                'last_page' => 1,
                'last_page_url' => 'http://localhost:8000/api/v1/admin/products?page=1',
                'links' => [
                    0 => [
                        'url' => null,
                        'label' => '&laquo; Previous',
                        'active' => false,
                    ],
                    1 => [
                        'url' => 'http://localhost:8000/api/v1/admin/products?page=1',
                        'label' => '1',
                        'active' => true,
                    ],
                    2 => [
                        'url' => null,
                        'label' => 'Next &raquo;',
                        'active' => false,
                    ],
                ],
                'next_page_url' => null,
                'path' => 'http://localhost:8000/api/v1/admin/products',
                'per_page' => 16,
                'prev_page_url' => null,
                'to' => 1,
                'total' => 1,
            ],
        ]);
    }
}
