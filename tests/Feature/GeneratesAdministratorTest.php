<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GeneratesAdministratorTest extends TestCase
{
    use RefreshDatabase;

    public $postRoute = null;

    public function setup(): void
    {
        parent::setUp();

        $this->postRoute = route('v1_admin.generate');
    }

    public function test_generates_an_administrator()
    {
        $this->withoutExceptionHandling();

        $payload = $this->preparePayload();

        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(201);
        $this->assertEquals(1, User::count());

        $data = $response->json();
        $this->assertEquals($data['status'], 'success');
        $this->assertEquals($data['message'], 'Administrator generated successfully.');
    }

    public function test_first_name_field_is_required()
    {
        $payload = $this->preparePayload(['first_name' => '']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['first_name'][0], 'The first name field is required.');
    }

    public function test_last_name_field_is_required()
    {
        $payload = $this->preparePayload(['last_name' => '']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['last_name'][0], 'The last name field is required.');
    }

    public function test_email_field_is_required()
    {
        $payload = $this->preparePayload(['email' => '']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['email'][0], 'The email field is required.');
    }

    public function test_email_must_be_valid_email_address()
    {
        $payload = $this->preparePayload(['email' => 'admin@$%^&.com']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['email'][0], 'The email field must be a valid email address.');
    }

    public function test_password_field_is_required()
    {
        $payload = $this->preparePayload(['password' => '']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['password'][0], 'The password field is required.');
    }

    public function test_confirm_password_field_is_required()
    {
        $payload = $this->preparePayload(['confirm_password' => '']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['confirm_password'][0], 'The confirm password field is required.');
    }

    public function test_confirm_password_and_password_must_match()
    {
        $payload = $this->preparePayload(['password' => 'secret', 'confirm_password' => 'password']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['confirm_password'][0], 'The confirm password field must match password.');
    }

    protected function preparePayload($data = [])
    {
        return array_merge([
            'first_name' => 'Super',
            'last_name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => 'Password',
            'confirm_password' => 'Password',
        ], $data);
    }
}
