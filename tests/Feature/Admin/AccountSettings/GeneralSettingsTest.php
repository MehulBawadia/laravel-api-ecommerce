<?php

namespace Tests\Feature\Admin\AccountSettings;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeneralSettingsTest extends TestCase
{
    use RefreshDatabase;

    public $postRoute = null;

    public $admin = null;

    public function setup(): void
    {
        parent::setUp();

        $this->admin = $this->signInAdmin(['first_name' => 'Super', 'last_name' => 'Administrator']);

        $this->postRoute = route('v1_admin.accountSettings.general');
    }

    public function test_admin_can_update_general_account_settings()
    {
        $this->withoutExceptionHandling();

        $this->assertEquals($this->admin->first_name, 'Super');
        $this->assertEquals($this->admin->last_name, 'Administrator');

        $payload = $this->preparePayload([
            'first_name' => 'Administrator',
            'last_name' => 'Super',
        ]);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(201);
        $response->assertSeeText('General Settings updated successfully.');
        $this->assertEquals($this->admin->first_name, 'Administrator');
        $this->assertEquals($this->admin->last_name, 'Super');
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

    protected function preparePayload($data = [])
    {
        return array_merge([
            'first_name' => 'Super',
            'last_name' => 'Administrator',
            'email' => 'admin@example.com',
        ], $data);
    }
}
