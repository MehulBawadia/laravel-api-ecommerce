<?php

namespace Tests\Feature\AccountSettings;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $postRoute = null;

    public function setup(): void
    {
        parent::setUp();

        $this->signInAdmin();

        $this->putRoute = route('auth.accountSettings.changePassword');
    }

    public function test_admin_or_user_may_change_their_password()
    {
        $this->withoutExceptionHandling();

        $payload = $this->preparePayload();
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(201);
        $response->assertSeeText(__('response.account_settings.change_password'));
    }

    public function test_current_password_field_is_required()
    {
        $payload = $this->preparePayload(['current_password' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['current_password'][0], 'The current password field is required.');
    }

    public function test_current_password_must_be_a_correct_password()
    {
        $payload = $this->preparePayload(['current_password' => 'Secret']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['current_password'][0], 'The current password is incorrect.');
    }

    public function test_new_password_field_is_required()
    {
        $payload = $this->preparePayload(['new_password' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['new_password'][0], 'The new password field is required.');
    }

    public function test_confirm_new_password_field_is_required()
    {
        $payload = $this->preparePayload(['new_password_confirmation' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

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
