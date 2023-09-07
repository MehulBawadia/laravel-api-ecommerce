<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public $postRoute = null;

    public function setup(): void
    {
        parent::setUp();

        $this->createUser(['email' => 'admin@example.com', 'is_admin' => true]);

        $this->createUser(['email' => 'user@example.com', 'password' => bcrypt('Password')]);

        $this->postRoute = route('auth.resetPassword');
    }

    public function test_admin_or_user_resets_their_password()
    {
        $this->withoutExceptionHandling();

        $createdAt = now()->format('Y-m-d H:i:s');
        $data = [
            'email' => 'user@example.com',
            'token' => 'some-random-string',
            'created_at' => $createdAt,
        ];
        DB::table('password_reset_tokens')->insert($data);
        $this->assertDatabaseHas('password_reset_tokens', $data);

        $payload = $this->preparePayload();
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(201);
        $response->assertSeeText('Password reset successfully.');
        $this->assertDatabaseMissing('password_reset_tokens', $data);
    }

    public function test_admin_or_user_cannot_reset_password_if_already_logged_in()
    {
        $this->withoutExceptionHandling();

        Sanctum::actingAs(
            User::all()->random()
        );

        $response = $this->postJsonPayload($this->postRoute, $this->preparePayload());
        $response->assertStatus(302);
    }

    public function test_admin_or_user_cannot_reset_password_if_token_has_expired()
    {
        $this->withoutExceptionHandling();

        $createdAt = now()->subHours(2)->format('Y-m-d H:i:s');
        $data = [
            'email' => 'user@example.com',
            'token' => 'some-random-string',
            'created_at' => $createdAt,
        ];
        DB::table('password_reset_tokens')->insert($data);
        $this->assertDatabaseHas('password_reset_tokens', $data);

        $payload = $this->preparePayload();
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(403);
        $response->assertSeeText('Token expired. Generate a new token.');
        $this->assertDatabaseMissing('password_reset_tokens', $data);
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
        $payload = $this->preparePayload(['email' => 'user_not_found@example.com']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['email'][0], 'The selected email does not exist.');
    }

    public function test_token_field_is_required()
    {
        $payload = $this->preparePayload(['token' => '']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['token'][0], 'The token is required.');
    }

    public function test_token_must_exist_in_the_system()
    {
        $payload = $this->preparePayload(['token' => 'some-super-secret-token']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['token'][0], 'The token does not exist.');
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

    public function test_repeat_new_password_field_is_required()
    {
        $payload = $this->preparePayload(['repeat_new_password' => '']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['repeat_new_password'][0], 'The repeat new password field is required.');
    }

    public function test_repeat_new_password_and_password_must_match()
    {
        $payload = $this->preparePayload(['password' => 'Password', 'repeat_new_password' => 'secret']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['repeat_new_password'][0], 'The repeat new password field must match new password.');
    }

    protected function preparePayload($data = [])
    {
        return array_merge([
            'email' => 'user@example.com',
            'token' => 'some-random-string',
            'new_password' => 'Secret',
            'repeat_new_password' => 'Secret',
        ], $data);
    }
}
