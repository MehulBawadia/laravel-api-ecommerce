<?php

namespace Tests\Feature\Users\Addresses;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BillingAddressTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $putRoute = null;

    protected $user;

    public function setup(): void
    {
        parent::setUp();

        // Create the administrator for the application
        $this->createUser(['is_admin' => true]);

        // Create a non-admin user and log in them
        $this->user = $this->signInUser();

        $this->putRoute = route('v1_user.addresses.billing');
    }

    public function test_user_can_update_their_billing_address()
    {
        $this->withoutExceptionHandling();

        $payload = $this->preparePayload();
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(200);
        $response->assertSeeText('status');
        $response->assertSeeText(__('response.user.address', ['addressType' => 'Billing']));
    }

    public function test_billing_first_name_field_is_required()
    {
        $payload = $this->preparePayload(['billing_first_name' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_first_name'][0], 'The billing first name field is required.');
    }

    public function test_billing_first_name_cannot_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['billing_first_name' => fake()->paragraph(5, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_first_name'][0], 'The billing first name field must not be greater than 100 characters.');
    }

    public function test_billing_last_name_field_is_required()
    {
        $payload = $this->preparePayload(['billing_last_name' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_last_name'][0], 'The billing last name field is required.');
    }

    public function test_billing_last_name_cannot_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['billing_last_name' => fake()->paragraph(5, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_last_name'][0], 'The billing last name field must not be greater than 100 characters.');
    }

    public function test_billing_email_field_is_required()
    {
        $payload = $this->preparePayload(['billing_email' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_email'][0], 'The billing email field is required.');
    }

    public function test_email_must_be_valid_email_address()
    {
        $payload = $this->preparePayload(['billing_email' => 'admin@$%^&.com']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_email'][0], 'The billing email field must be a valid email address.');
    }

    public function test_billing_contact_field_is_required()
    {
        $payload = $this->preparePayload(['billing_contact' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_contact'][0], 'The billing contact field is required.');
    }

    public function test_billing_contact_must_not_be_greater_than_20_characters()
    {
        $payload = $this->preparePayload(['billing_contact' => fake()->paragraph(1, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_contact'][0], 'The billing contact field must not be greater than 20 characters.');
    }

    public function test_billing_address_line_1_field_is_required()
    {
        $payload = $this->preparePayload(['billing_address_line_1' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_address_line_1'][0], 'The billing address line 1 field is required.');
    }

    public function test_billing_address_line_1_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['billing_address_line_1' => fake()->paragraph(5, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_address_line_1'][0], 'The billing address line 1 field must not be greater than 100 characters.');
    }

    public function test_billing_address_line_2_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['billing_address_line_2' => fake()->paragraph(5, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_address_line_2'][0], 'The billing address line 2 field must not be greater than 100 characters.');
    }

    public function test_billing_area_field_is_required()
    {
        $payload = $this->preparePayload(['billing_area' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_area'][0], 'The billing area field is required.');
    }

    public function test_billing_area_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['billing_area' => fake()->paragraph(5, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_area'][0], 'The billing area field must not be greater than 100 characters.');
    }

    public function test_billing_landmark_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['billing_landmark' => fake()->paragraph(5, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_landmark'][0], 'The billing landmark field must not be greater than 100 characters.');
    }

    public function test_billing_city_field_is_required()
    {
        $payload = $this->preparePayload(['billing_city' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_city'][0], 'The billing city field is required.');
    }

    public function test_billing_city_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['billing_city' => fake()->paragraph(5, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_city'][0], 'The billing city field must not be greater than 100 characters.');
    }

    public function test_billing_postal_code_field_is_required()
    {
        $payload = $this->preparePayload(['billing_postal_code' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_postal_code'][0], 'The billing postal code field is required.');
    }

    public function test_billing_postal_code_must_be_alpha_numeric_characters_only()
    {
        $payload = $this->preparePayload(['billing_postal_code' => fake()->sentence(1, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_postal_code'][0], 'The billing postal code field must only contain letters and numbers.');
    }

    public function test_billing_postal_code_must_not_be_greater_than_20_characters()
    {
        $payload = $this->preparePayload(['billing_postal_code' => 's0meRand0mGeneratedStr1ng']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_postal_code'][0], 'The billing postal code field must not be greater than 20 characters.');
    }

    public function test_billing_state_province_field_is_required()
    {
        $payload = $this->preparePayload(['billing_state_province' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_state_province'][0], 'The billing state province field is required.');
    }

    public function test_billing_state_province_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['billing_state_province' => fake()->paragraph(5, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_state_province'][0], 'The billing state province field must not be greater than 100 characters.');
    }

    public function test_billing_country_field_is_required()
    {
        $payload = $this->preparePayload(['billing_country' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_country'][0], 'The billing country field is required.');
    }

    public function test_billing_country_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['billing_country' => fake()->paragraph(5, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_country'][0], 'The billing country field must not be greater than 100 characters.');
    }

    protected function preparePayload($data = [])
    {
        return array_merge([
            'billing_first_name' => $this->user ? $this->user->first_name : fake()->firstName(),
            'billing_last_name' => $this->user ? $this->user->last_name : fake()->lastName(),
            'billing_email' => $this->user ? $this->user->email : fake()->safeEmail(),
            'billing_contact' => fake()->phoneNumber(),
            'billing_address_line_1' => fake()->buildingNumber(),
            'billing_address_line_2' => fake()->streetName(),
            'billing_area' => fake()->streetAddress(),
            'billing_landmark' => fake()->streetSuffix(),
            'billing_city' => fake()->city(),
            'billing_postal_code' => Str::replace([' ', '-', '.'], [''], fake()->postcode()),
            'billing_state_province' => fake()->citySuffix(),
            'billing_country' => fake()->country(),
        ], $data);
    }
}
