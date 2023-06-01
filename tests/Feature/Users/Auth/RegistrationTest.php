<?php

namespace Tests\Feature\Users\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public $postRoute = null;

    public function setup(): void
    {
        parent::setUp();

        // Create the administrator for the application
        $this->createUser();

        $this->postRoute = route('v1_user.register');
    }

    /**
     * A basic feature test example.
     */
    public function test_user_can_register(): void
    {
        $this->withoutExceptionHandling();

        $payload = $this->preparePayload();

        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(201);
        $this->assertEquals(2, User::count());

        $data = $response->json();
        $this->assertEquals($data['status'], 'success');
        $this->assertEquals($data['message'], 'You have registered successfully.');
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

    public function test_email_must_be_unique()
    {
        User::factory(['email' => 'user1@example.com'])->create();
        $payload = $this->preparePayload(['email' => 'user1@example.com']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['email'][0], 'The email has already been taken.');
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
            'first_name' => 'User',
            'last_name' => 'One',
            'email' => 'userone@example.com',
            'password' => 'Password',
            'confirm_password' => 'Password',
        ], $data);
    }
}
