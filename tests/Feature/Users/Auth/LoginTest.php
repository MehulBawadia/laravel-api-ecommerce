<?php

namespace Tests\Feature\Users\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public $postRoute = null;

    public function setup(): void
    {
        parent::setUp();

        // Create the administrator for the application
        $this->createUser();

        // Create a non-admin user
        $this->createUser([
            'first_name' => 'User',
            'last_name' => 'One',
            'email' => 'userone@example.com',
            'password' => bcrypt('Password'),
        ]);

        $this->postRoute = route('v1_user.login');
    }

    public function test_user_can_login_with_proper_credentials()
    {
        $this->withoutExceptionHandling();

        $payload = $this->preparePayload();
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(200);
        $response->assertSeeText('status');
        $response->assertSeeText('You have logged in successfully.');
        $response->assertSeeText('access_token');
        $response->assertSeeText('token_type');
        $response->assertSeeText('Bearer');
    }

    public function test_user_may_logout()
    {
        $payload = $this->preparePayload();
        $response = $this->postJsonPayload($this->postRoute, $payload);
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$response->json()['data']['access_token'],
        ];

        $response = $this->postJsonPayload(route('v1_user.logout'), [], $headers);
        $response->assertJsonFragment([
            'status' => 'success',
            'message' => 'You have logged out succesfully.',
        ]);
    }

    public function test_user_cannot_login_with_incorrect_credentials()
    {
        $this->withoutExceptionHandling();

        $payload = $this->preparePayload(['password' => 'secret']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(401);
        $response->assertJsonFragment([
            'status' => 'failed',
            'message' => 'The provided credentials are incorrect.',
        ]);
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
        $payload = $this->preparePayload(['email' => 'user@$%^&.com']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['email'][0], 'The email field must be a valid email address.');
    }

    public function test_email_must_exist_in_the_system()
    {
        $payload = $this->preparePayload(['email' => 'user@example.com']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['email'][0], 'The selected email does not exist.');
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

    protected function preparePayload($data = [])
    {
        return array_merge([
            'email' => 'userone@example.com',
            'password' => 'Password',
        ], $data);
    }
}
