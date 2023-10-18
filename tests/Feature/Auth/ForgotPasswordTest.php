<?php

namespace Tests\Feature\Auth;

use App\Mail\v1\Auth\ForgotPasswordMail;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $postRoute = null;

    public function setup(): void
    {
        parent::setUp();

        $this->createUser(['is_admin' => true]);

        $this->createUser(['email' => 'user@example.com', 'password' => bcrypt('Password')]);

        $this->postRoute = route('auth.forgotPassword');
    }

    public function test_admin_or_user_requests_for_password_reset_link()
    {
        $this->withoutExceptionHandling();

        $createdAt = now()->format('Y-m-d H:i:s');
        $payload = $this->preparePayload(['created_at' => $createdAt]);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(201);
        $response->assertSeeText(__('response.auth.password_reset_link_sent'));

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'user@example.com',
            'created_at' => $createdAt,
        ]);
    }

    public function test_logged_in_admin_or_user_cannot_request_password_reset_link()
    {
        $this->withoutExceptionHandling();

        Sanctum::actingAs(
            User::all()->random()
        );

        $response = $this->postJsonPayload($this->postRoute, $this->preparePayload());
        $response->assertStatus(302);
    }

    public function test_admin_or_user_receives_the_password_reset_link_via_email()
    {
        Mail::fake();
        $this->withoutExceptionHandling();

        $payload = $this->preparePayload();
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(201);
        $response->assertSeeText(__('response.auth.password_reset_link_sent'));

        Mail::assertSent(ForgotPasswordMail::class, function (ForgotPasswordMail $mail) {
            return $mail->hasTo('user@example.com') &&
                    $mail->hasSubject(config('app.name').': Reset Password Link');
        });
    }

    public function test_admin_or_user_reset_password_mail_content()
    {
        $data = [
            'email' => 'user@example.com',
            'token' => 'random-string',
            'created_at' => now(),
            'reset_password_link' => env('APP_FRONTEND_BASE_URL').'/reset-password/random-string?email=user@example.com',
        ];

        $mailable = new ForgotPasswordMail($data);

        $mailable->assertFrom('no-reply@example.com');
        $mailable->assertHasTo($data['email']);
        $mailable->assertHasSubject(config('app.name').': Reset Password Link');

        $mailable->assertSeeInHtml('Dear '.$data['email']);
        $mailable->assertSeeInHtml($data['reset_password_link']);
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

    protected function preparePayload($data = [])
    {
        return array_merge([
            'email' => 'user@example.com',
        ], $data);
    }
}
