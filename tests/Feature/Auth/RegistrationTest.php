<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $postRoute = null;

    public function setup(): void
    {
        parent::setUp();

        $this->createUser(['is_admin' => true]);

        $this->postRoute = route('auth.register');
    }

    /**
     * A basic feature test example.
     */
    public function test_user_can_register(): void
    {
        Http::fake();
        $this->withoutExceptionHandling();

        $payload = $this->preparePayload();

        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(201);
        $this->assertEquals(2, User::count());

        $data = $response->json();
        $this->assertEquals($data['status'], 'success');
        $this->assertEquals($data['message'], __('response.auth.register'));
    }

    public function test_creates_customer_in_stripe()
    {
        $fakeCustomerId = 'G3E99LH8110';
        $payload = $this->preparePayload();
        $fakeData = $this->createdCustomerData([
            'id' => "cus_$fakeCustomerId",
            'name' => "{$payload['first_name']} {$payload['last_name']}",
        ]);

        Http::fake([
            'https://api.stripe.com/v1/customers' => Http::response($fakeData, 200),
        ]);

        $this->withoutExceptionHandling();

        $this->postJsonPayload($this->postRoute, $payload);

        $user = User::find(2);
        $this->assertEquals($user->stripe_user_id, "cus_$fakeCustomerId");
        $this->assertEquals($user->first_name, 'User');
        $this->assertEquals($user->last_name, 'One');
    }

    public function test_user_address_gets_created_after_registration()
    {
        Http::fake();

        $this->withoutExceptionHandling();

        $payload = $this->preparePayload();

        $this->postJsonPayload($this->postRoute, $payload);

        $this->assertNotNull(User::with(['billingAddress'])->find(2)->billingAddress->first());
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

    protected function createdCustomerData($overrideData = [])
    {
        $data = [
            'id' => 'cus_9s6XKzkNRiz8i3',
            'name' => null,
            'email' => 'test@test.com',
            'object' => 'customer',
        ];

        return json_encode(array_merge($data, $overrideData));
    }
}
