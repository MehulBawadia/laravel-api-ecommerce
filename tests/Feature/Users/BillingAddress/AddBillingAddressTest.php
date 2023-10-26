<?php

namespace Tests\Feature\Users\BillingAddress;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AddBillingAddressTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $postRoute = null;

    protected $user;

    public function setup(): void
    {
        parent::setUp();

        $this->createUser(['is_admin' => true]);

        $this->user = $this->signInUser();

        $this->postRoute = route('v1_user.billingAddress.store');
    }

    public function test_user_can_add_their_address()
    {
        $this->withoutExceptionHandling();

        $payload = $this->preparePayload();
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(200);
        $response->assertSeeText('status');
        $response->assertSeeText(__('response.user.address.success', ['type' => 'Billing', 'action' => 'created']));
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

    public function test_first_name_cannot_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['first_name' => fake()->paragraph(5, true)]);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['first_name'][0], 'The first name field must not be greater than 100 characters.');
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

    public function test_last_name_cannot_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['last_name' => fake()->paragraph(5, true)]);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['last_name'][0], 'The last name field must not be greater than 100 characters.');
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

    public function test_contact_field_is_required()
    {
        $payload = $this->preparePayload(['contact' => '']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['contact'][0], 'The contact field is required.');
    }

    public function test_contact_must_not_be_greater_than_20_characters()
    {
        $payload = $this->preparePayload(['contact' => fake()->paragraph(1, true)]);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['contact'][0], 'The contact field must not be greater than 20 characters.');
    }

    public function test_address_line_1_field_is_required()
    {
        $payload = $this->preparePayload(['address_line_1' => '']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['address_line_1'][0], 'The address line 1 field is required.');
    }

    public function test_address_line_1_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['address_line_1' => fake()->paragraph(5, true)]);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['address_line_1'][0], 'The address line 1 field must not be greater than 100 characters.');
    }

    public function test_address_line_2_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['address_line_2' => fake()->paragraph(5, true)]);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['address_line_2'][0], 'The address line 2 field must not be greater than 100 characters.');
    }

    public function test_area_field_is_required()
    {
        $payload = $this->preparePayload(['area' => '']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['area'][0], 'The area field is required.');
    }

    public function test_area_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['area' => fake()->paragraph(5, true)]);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['area'][0], 'The area field must not be greater than 100 characters.');
    }

    public function test_landmark_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['landmark' => fake()->paragraph(5, true)]);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['landmark'][0], 'The landmark field must not be greater than 100 characters.');
    }

    public function test_city_field_is_required()
    {
        $payload = $this->preparePayload(['city' => '']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['city'][0], 'The city field is required.');
    }

    public function test_city_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['city' => fake()->paragraph(5, true)]);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['city'][0], 'The city field must not be greater than 100 characters.');
    }

    public function test_postal_code_field_is_required()
    {
        $payload = $this->preparePayload(['postal_code' => '']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['postal_code'][0], 'The postal code field is required.');
    }

    public function test_postal_code_must_be_alpha_numeric_characters_only()
    {
        $payload = $this->preparePayload(['postal_code' => fake()->sentence(1, true)]);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['postal_code'][0], 'The postal code field must only contain letters and numbers.');
    }

    public function test_postal_code_must_not_be_greater_than_20_characters()
    {
        $payload = $this->preparePayload(['postal_code' => 's0meRand0mGeneratedStr1ng']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['postal_code'][0], 'The postal code field must not be greater than 20 characters.');
    }

    public function test_state_province_field_is_required()
    {
        $payload = $this->preparePayload(['state_province' => '']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['state_province'][0], 'The state province field is required.');
    }

    public function test_state_province_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['state_province' => fake()->paragraph(5, true)]);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['state_province'][0], 'The state province field must not be greater than 100 characters.');
    }

    public function test_country_field_is_required()
    {
        $payload = $this->preparePayload(['country' => '']);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['country'][0], 'The country field is required.');
    }

    public function test_country_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['country' => fake()->paragraph(5, true)]);
        $response = $this->postJsonPayload($this->postRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['country'][0], 'The country field must not be greater than 100 characters.');
    }

    protected function preparePayload($data = [])
    {
        return array_merge([
            'first_name' => $this->user ? $this->user->first_name : fake()->firstName(),
            'last_name' => $this->user ? $this->user->last_name : fake()->lastName(),
            'email' => $this->user ? $this->user->email : fake()->safeEmail(),
            'contact' => fake()->phoneNumber(),
            'address_line_1' => fake()->buildingNumber(),
            'address_line_2' => fake()->streetName(),
            'area' => fake()->streetAddress(),
            'landmark' => fake()->streetSuffix(),
            'city' => fake()->city(),
            'postal_code' => Str::replace([' ', '-', '.'], [''], fake()->postcode()),
            'state_province' => fake()->citySuffix(),
            'country' => fake()->country(),
        ], $data);
    }
}
