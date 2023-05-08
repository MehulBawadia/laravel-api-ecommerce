<?php

namespace Tests\Feature\Admin\Brands;

use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandsTest extends TestCase
{
    use RefreshDatabase;

    public $getRoute = null;

    public $admin = null;

    public function setup(): void
    {
        parent::setUp();

        $this->admin = $this->signInAdmin();

        $this->getRoute = route('v1_admin.brands');
    }

    public function test_no_brands_exists()
    {
        $this->withoutExceptionHandling();

        $this->getJson($this->getRoute);

        $this->assertEquals(0, Brand::count());
    }

    public function test_brands_exists()
    {
        $this->withoutExceptionHandling();

        Brand::factory(10)->create();

        $this->assertEquals(10, Brand::count());
    }
}
