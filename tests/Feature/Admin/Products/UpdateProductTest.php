<?php

namespace Tests\Feature\Admin\Products;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UpdateProductTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $putRoute = null;

    public $admin = null;

    public $product = null;

    public function setup(): void
    {
        parent::setUp();

        $this->admin = $this->signInAdmin();

        Category::factory()->create();
        Brand::factory()->create();
        $this->product = Product::factory()->create(
            $this->preparePayload()
        );

        $this->putRoute = route('v1_admin.products.update', $this->product->id);
    }

    public function test_product_exists()
    {
        $this->withoutExceptionHandling();
        $this->assertEquals(1, Product::count());
    }

    public function test_admin_can_update_the_product()
    {
        Http::fake();

        $this->withoutExceptionHandling();

        $this->assertEquals($this->product->name, 'Product 1');

        $payload = $this->preparePayload(['name' => 'New Product']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertSeeText(__('response.admin.products.success', ['actionType' => 'updated']));
        $this->assertCount(1, Product::all());
        $this->assertEquals($this->product->fresh()->name, 'New Product');
    }

    public function test_admin_may_update_product_image()
    {
        Http::fake();

        $this->withoutExceptionHandling();

        $payload = $this->preparePayload([
            'image' => UploadedFile::fake()->image('my_image_file.jpg'),
        ]);
        $this->putJsonPayload($this->putRoute, $payload);

        $this->assertNotNull($this->product->getMedia('product-images')[0]);
    }

    public function test_admin_receives_404_error_if_product_not_found()
    {
        Http::fake();

        $this->withoutExceptionHandling();

        $payload = $this->preparePayload(['name' => 'Product 2']);
        $response = $this->putJson(route('v1_admin.products.update', 10), $payload);
        $response->assertStatus(404);
        $response->assertNotFound();
    }

    public function test_name_field_is_required()
    {
        $payload = $this->preparePayload(['name' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['name'][0], 'The name field is required.');
    }

    public function test_name_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload([
            'name' => 'Sint voluptate nulla nisi nostrud qui veniam id.Commodo id dolore commodo excepteur ullamco ullamco qui proident cupidatat anim. Sit exercitation culpa est ea ad.',
        ]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['name'][0], 'The name field must not be greater than 100 characters.');
    }

    public function test_description_field_is_required()
    {
        $payload = $this->preparePayload(['description' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['description'][0], 'The description field is required.');
    }

    public function test_category_id_field_is_required()
    {
        $payload = $this->preparePayload(['category_id' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['category_id'][0], 'Please select the category.');
    }

    public function test_category_id_must_be_integer()
    {
        $payload = $this->preparePayload(['category_id' => 'random-category-id']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['category_id'][0], 'Selected category is invalid.');
    }

    public function test_category_id_must_exist()
    {
        $payload = $this->preparePayload(['category_id' => 100]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['category_id'][0], 'Selected category does not exist.');
    }

    public function test_brand_id_field_is_required()
    {
        $payload = $this->preparePayload(['brand_id' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['brand_id'][0], 'Please select the brand.');
    }

    public function test_brand_id_must_be_integer()
    {
        $payload = $this->preparePayload(['brand_id' => 'random-brand-id']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['brand_id'][0], 'Selected brand is invalid.');
    }

    public function test_brand_id_must_exist()
    {
        $payload = $this->preparePayload(['brand_id' => 100]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['brand_id'][0], 'Selected brand does not exist.');
    }

    public function test_rate_field_is_required()
    {
        $payload = $this->preparePayload(['rate' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['rate'][0], 'The rate field is required.');
    }

    public function test_rate_must_contain_only_numeric_characters()
    {
        $payload = $this->preparePayload(['rate' => 'some-random-values']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['rate'][0], 'The rate field must be a number.');
    }

    public function test_rate_must_be_greater_than_equal_to_0()
    {
        $payload = $this->preparePayload(['rate' => -50]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['rate'][0], 'The rate field must be at least 0.0.');
    }

    public function test_quantity_field_is_required()
    {
        $payload = $this->preparePayload(['quantity' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['quantity'][0], 'The quantity field is required.');
    }

    public function test_quantity_must_contain_only_numeric_characters()
    {
        $payload = $this->preparePayload(['quantity' => 'some-random-values']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['quantity'][0], 'The quantity field must be a number.');
    }

    public function test_quantity_must_be_greater_than_equal_to_0()
    {
        $payload = $this->preparePayload(['quantity' => -50]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['quantity'][0], 'The quantity field must be at least 0.');
    }

    public function test_image_must_be_a_file()
    {
        $payload = $this->preparePayload(['image' => 'Some Random Values that is a string']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['image'][0], 'The image field must be a file.');
    }

    public function test_image_must_be_of_type_jpg_jpeg_or_png()
    {
        $payload = $this->preparePayload(['image' => UploadedFile::fake()->image('random.txt')]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['image'][0], 'The image field must be a file of type: jpg, jpeg, png, JPG, JPEG, PNG.');
    }

    public function test_meta_title_field_is_required()
    {
        $payload = $this->preparePayload(['meta_title' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['meta_title'][0], 'The meta title field is required.');
    }

    public function test_meta_title_must_not_be_greater_than_80_characters()
    {
        $payload = $this->preparePayload([
            'meta_title' => 'Ipsum occaecat enim incididunt duis quis excepteur aliqua nostrud voluptate amet cillum magna.',
        ]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['meta_title'][0], 'The meta title field must not be greater than 80 characters.');
    }

    public function test_meta_description_field_is_required()
    {
        $payload = $this->preparePayload([
            'meta_description' => '',
        ]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['meta_description'][0], 'The meta description field is required.');
    }

    public function test_meta_description_must_not_be_greater_than_180_characters()
    {
        $payload = $this->preparePayload([
            'meta_description' => 'Enim do labore ullamco veniam in magna nulla incididunt id excepteur veniam. Commodo veniam minim elit eu anim laborum ad culpa id enim tempor occaecat do elit. Sunt aute eiusmod fugiat id elit duis in voluptate fugiat culpa amet.',
        ]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['meta_description'][0], 'The meta description field must not be greater than 180 characters.');
    }

    public function test_meta_keywords_must_not_be_greater_than_255_characters()
    {
        $payload = $this->preparePayload([
            'meta_keywords' => 'Exercitation ea ea pariatur tempor non commodo officia deserunt esse officia consectetur. Reprehenderit labore fugiat duis exercitation labore cupidatat in excepteur. Duis tempor dolor commodo et laborum fugiat nostrud exercitation irure non dolor voluptate irure qui. Nulla velit laborum ex duis.',
        ]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['meta_keywords'][0], 'The meta keywords field must not be greater than 255 characters.');
    }

    protected function preparePayload($data = [])
    {
        return array_merge([
            'name' => 'Product 1',
            'category_id' => Category::first()->id,
            'brand_id' => Brand::first()->id,
            'quantity' => mt_rand(1, 9),
            'rate' => mt_rand(100, 999),
            'description' => 'Deserunt sint proident cillum aute est exercitation commodo duis minim commodo magna.',
            'meta_title' => 'Non nostrud fugiat magna magna dolore minim sint pariatur eu proident laborum.',
            'meta_description' => 'Laborum veniam culpa quis in exercitation officia fugiat sit id deserunt sunt.',
            'meta_keywords' => 'Id mollit aliquip reprehenderit culpa aliquip amet nisi consequat mollit ullamco.',
        ], $data);
    }
}
