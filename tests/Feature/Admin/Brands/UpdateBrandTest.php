<?php

namespace Tests\Feature\Admin\Brands;

use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateBrandTest extends TestCase
{
    use RefreshDatabase;

    public $putRoute = null;

    public $admin = null;

    public $brand = null;

    public function setup(): void
    {
        parent::setUp();

        $this->admin = $this->signInAdmin();
        $this->brand = Brand::factory()->create(
            $this->preparePayload()
        );

        $this->putRoute = route('v1_admin.brands.update', $this->brand->id);
    }

    public function test_Brand_exists()
    {
        $this->withoutExceptionHandling();
        $this->assertEquals(1, Brand::count());
    }

    public function test_admin_can_update_the_Brand()
    {
        $this->withoutExceptionHandling();

        $this->assertEquals($this->brand->name, 'Brand 1');

        $payload = $this->preparePayload(['name' => 'New Brand']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertSeeText('Brand updated successfully.');
        $this->assertCount(1, Brand::all());
        $this->assertEquals($this->brand->fresh()->name, 'New Brand');
    }

    public function test_admin_receives_404_error_if_Brand_not_found()
    {
        $this->withoutExceptionHandling();

        $payload = $this->preparePayload(['name' => 'Brand 2']);
        $response = $this->putJson(route('v1_admin.brands.update', 10), $payload);
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

    public function test_name_must_be_unique()
    {
        Brand::factory()->create(['name' => 'Random Brand']);
        $payload = $this->preparePayload(['name' => 'Random Brand']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['name'][0], 'The name has already been taken.');
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

    public function test_description_must_not_be_greater_than_255_characters()
    {
        $payload = $this->preparePayload([
            'description' => 'Ipsum occaecat enim incididunt duis quis excepteur aliqua nostrud voluptate amet cillum magna. Cupidatat cupidatat fugiat consequat labore aliquip fugiat ad ea sint. Non eu quis voluptate sint tempor sint cillum occaecat occaecat aliquip duis eu sint exercitation. Cupidatat aliqua ipsum exercitation veniam nostrud.',
        ]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['description'][0], 'The description field must not be greater than 255 characters.');
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
            'name' => 'Brand 1',
            'description' => 'Deserunt sint proident cillum aute est exercitation commodo duis minim commodo magna.',
            'meta_title' => 'Non nostrud fugiat magna magna dolore minim sint pariatur eu proident laborum.',
            'meta_description' => 'Laborum veniam culpa quis in exercitation officia fugiat sit id deserunt sunt.',
            'meta_keywords' => 'Id mollit aliquip reprehenderit culpa aliquip amet nisi consequat mollit ullamco.',
        ], $data);
    }
}
