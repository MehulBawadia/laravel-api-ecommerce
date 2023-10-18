<?php

namespace Tests\Feature\AccountSettings;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class GeneralSettingsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $putRoute = null;

    public $admin = null;

    public function setup(): void
    {
        parent::setUp();

        $this->user = $this->signInAdmin();

        $this->putRoute = route('auth.accountSettings.general');
    }

    public function test_admin_can_update_general_account_settings()
    {
        $this->withoutExceptionHandling();

        $this->assertEquals($this->user->first_name, 'Super');
        $this->assertEquals($this->user->last_name, 'Administrator');

        $payload = $this->preparePayload([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(201);
        $response->assertSeeText(__('response.account_settings.general'));
        $this->assertEquals($this->user->first_name, 'John');
        $this->assertEquals($this->user->last_name, 'Doe');
    }

    public function test_first_name_field_is_required()
    {
        $payload = $this->preparePayload(['first_name' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['first_name'][0], 'The first name field is required.');
    }

    public function test_first_name_cannot_be_more_100_characters()
    {
        $payload = $this->preparePayload(['first_name' => fake()->paragraphs(3, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['first_name'][0], 'The first name field must not be greater than 100 characters.');
    }

    public function test_last_name_field_is_required()
    {
        $payload = $this->preparePayload(['last_name' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['last_name'][0], 'The last name field is required.');
    }

    public function test_last_name_field_cannot_be_more_than_100_characters()
    {
        $payload = $this->preparePayload(['last_name' => fake()->paragraphs(3, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['last_name'][0], 'The last name field must not be greater than 100 characters.');
    }

    public function test_email_field_is_required()
    {
        $payload = $this->preparePayload(['email' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['email'][0], 'The email field is required.');
    }

    public function test_email_must_be_valid_email_address()
    {
        $payload = $this->preparePayload(['email' => 'admin@$%^&.com']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['email'][0], 'The email field must be a valid email address.');
    }

    public function test_email_should_be_unique()
    {
        $this->createUser(['email' => 'usertaken@example.com']);
        $payload = $this->preparePayload(['email' => 'usertaken@example.com']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['email'][0], 'The email has already been taken.');
    }

    protected function preparePayload($data = [])
    {
        return array_merge([
            'first_name' => 'Super',
            'last_name' => 'Administrator',
            'email' => 'admin@example.com',
        ], $data);
    }
}
