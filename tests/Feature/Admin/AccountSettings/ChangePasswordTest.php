<?php

namespace Tests\Feature\Admin\AccountSettings;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    public $postRoute = null;

    public $admin = null;

    public function setup(): void
    {
        parent::setUp();

        $this->admin = $this->signInAdmin(['first_name' => 'Super', 'last_name' => 'Administrator']);

        $this->postRoute = route('v1_admin.accountSettings.changePassword');
    }

    public function test_admin_may_change_their_password()
    {
        $this->withoutExceptionHandling();

        $payload = $this->preparePayload();
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(201);
        $response->assertSeeText('Password updated successfully.');
    }

    public function test_current_password_field_is_required()
    {
        $payload = $this->preparePayload(['current_password' => '']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['current_password'][0], 'The current password field is required.');
    }

    public function test_current_password_must_be_a_valid_password()
    {
        $payload = $this->preparePayload(['current_password' => 'Secret']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['current_password'][0], 'The current password is incorrect.');
    }

    public function test_new_password_field_is_required()
    {
        $payload = $this->preparePayload(['new_password' => '']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['new_password'][0], 'The new password field is required.');
    }

    public function test_confirm_new_password_field_is_required()
    {
        $payload = $this->preparePayload(['new_password_confirmation' => '']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['new_password_confirmation'][0], 'The confirm new password field is required.');
    }

    protected function preparePayload($data = [])
    {
        return array_merge([
            'current_password' => 'Password',
            'new_password' => 'Secret',
            'new_password_confirmation' => 'Secret',
        ], $data);
    }
}
