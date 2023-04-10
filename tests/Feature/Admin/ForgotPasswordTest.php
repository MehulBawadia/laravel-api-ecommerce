<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\v1\Admin\ForgotPasswordMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public $postRoute = null;

    public function setup(): void
    {
        parent::setUp();

        User::factory()->create(['email' => 'admin@example.com', 'password' => bcrypt('Password')]);

        $this->postRoute = route('v1_admin.forgotPassword');
    }

    public function test_admin_requests_for_password_reset_link()
    {
        $this->withoutExceptionHandling();

        $payload = $this->preparePayload();
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(201);
        $response->assertSeeText('Password reset link sent successfully.');

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'admin@example.com',
            'created_at' => now(),
        ]);
    }

    public function test_logged_in_admin_cannot_request_password_reset_link()
    {
        $this->withoutDeprecationHandling();

        Sanctum::actingAs(User::first());

        $response = $this->postJsonPayload($this->postRoute, $this->preparePayload());
        $response->assertStatus(302);
    }

    public function test_admin_receives_the_password_reset_link_via_email()
    {
        Mail::fake();
        $this->withoutExceptionHandling();

        $payload = $this->preparePayload();
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(201);
        $response->assertSeeText('Password reset link sent successfully.');

        Mail::assertSent(ForgotPasswordMail::class, function (ForgotPasswordMail $mail) {
            return $mail->hasTo('admin@example.com') &&
                    $mail->hasSubject(config('app.name') .': Reset Password Link');
        });
    }

    public function test_admin_reset_password_mail_content()
    {
        $data = [
            'email' => 'admin@example.com',
            'token' => 'random-string',
            'created_at' => now(),
            'reset_password_link' => env('APP_FRONTEND_BASE_URL') . '/reset-password/random-string?email=admin@example.com',
        ];

        $mailable = new ForgotPasswordMail($data);

        $mailable->assertFrom('no-reply@example.com');
        $mailable->assertHasTo($data['email']);
        $mailable->assertHasSubject(config('app.name') .': Reset Password Link');

        $mailable->assertSeeInHtml('Dear '. $data['email']);
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
        $payload = $this->preparePayload(['email' => 'admin@$%^&.com']);
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

    protected function preparePayload($data = [])
    {
        return array_merge([
            'email' => 'admin@example.com',
        ], $data);
    }
}
