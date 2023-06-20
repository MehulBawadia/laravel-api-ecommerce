<?php

namespace Tests\Feature\Users\Addresses;

use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    public $putRoute = null;

    protected $user;

    public function setup(): void
    {
        parent::setUp();

        // Create the administrator for the application
        $this->createUser();

        // Create a non-admin user and log in them
        $this->user = $this->signInUser();

        UserAddress::factory()->create(['user_id' => $this->user->id]);

        $this->putRoute = route('v1_user.addresses.shipping');
    }

    public function test_user_can_update_their_shipping_address()
    {
        $this->withoutExceptionHandling();

        $payload = $this->preparePayload();
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(200);
        $response->assertSeeText('status');
        $response->assertSeeText('Shipping address updated successfully.');
    }

    public function test_shipping_first_name_field_is_required()
    {
        $payload = $this->preparePayload(['shipping_first_name' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_first_name'][0], 'The shipping first name field is required.');
    }

    public function test_shipping_first_name_cannot_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['shipping_first_name' => fake()->paragraph(5, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_first_name'][0], 'The shipping first name field must not be greater than 100 characters.');
    }

    public function test_shipping_last_name_field_is_required()
    {
        $payload = $this->preparePayload(['shipping_last_name' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_last_name'][0], 'The shipping last name field is required.');
    }

    public function test_shipping_last_name_cannot_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['shipping_last_name' => fake()->paragraph(5, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_last_name'][0], 'The shipping last name field must not be greater than 100 characters.');
    }

    public function test_shipping_email_field_is_required()
    {
        $payload = $this->preparePayload(['shipping_email' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_email'][0], 'The shipping email field is required.');
    }

    public function test_email_must_be_valid_email_address()
    {
        $payload = $this->preparePayload(['shipping_email' => 'admin@$%^&.com']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_email'][0], 'The shipping email field must be a valid email address.');
    }

    public function test_shipping_contact_field_is_required()
    {
        $payload = $this->preparePayload(['shipping_contact' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_contact'][0], 'The shipping contact field is required.');
    }

    public function test_shipping_contact_must_not_be_greater_than_20_characters()
    {
        $payload = $this->preparePayload(['shipping_contact' => fake()->paragraph(1, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_contact'][0], 'The shipping contact field must not be greater than 20 characters.');
    }

    public function test_shipping_address_line_1_field_is_required()
    {
        $payload = $this->preparePayload(['shipping_address_line_1' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_address_line_1'][0], 'The shipping address line 1 field is required.');
    }

    public function test_shipping_address_line_1_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['shipping_address_line_1' => fake()->paragraph(5, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_address_line_1'][0], 'The shipping address line 1 field must not be greater than 100 characters.');
    }

    public function test_shipping_address_line_2_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['shipping_address_line_2' => fake()->paragraph(5, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_address_line_2'][0], 'The shipping address line 2 field must not be greater than 100 characters.');
    }

    public function test_shipping_area_field_is_required()
    {
        $payload = $this->preparePayload(['shipping_area' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_area'][0], 'The shipping area field is required.');
    }

    public function test_shipping_area_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['shipping_area' => fake()->paragraph(5, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_area'][0], 'The shipping area field must not be greater than 100 characters.');
    }

    public function test_shipping_landmark_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['shipping_landmark' => fake()->paragraph(5, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_landmark'][0], 'The shipping landmark field must not be greater than 100 characters.');
    }

    public function test_shipping_city_field_is_required()
    {
        $payload = $this->preparePayload(['shipping_city' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_city'][0], 'The shipping city field is required.');
    }

    public function test_shipping_city_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['shipping_city' => fake()->paragraph(5, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_city'][0], 'The shipping city field must not be greater than 100 characters.');
    }

    public function test_shipping_postal_code_field_is_required()
    {
        $payload = $this->preparePayload(['shipping_postal_code' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_postal_code'][0], 'The shipping postal code field is required.');
    }

    public function test_shipping_postal_code_must_be_alpha_numeric_characters_only()
    {
        $payload = $this->preparePayload(['shipping_postal_code' => fake()->sentence(1, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_postal_code'][0], 'The shipping postal code field must only contain letters and numbers.');
    }

    public function test_shipping_postal_code_must_not_be_greater_than_20_characters()
    {
        $payload = $this->preparePayload(['shipping_postal_code' => 's0meRand0mGeneratedStr1ng']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_postal_code'][0], 'The shipping postal code field must not be greater than 20 characters.');
    }

    public function test_shipping_state_province_field_is_required()
    {
        $payload = $this->preparePayload(['shipping_state_province' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_state_province'][0], 'The shipping state province field is required.');
    }

    public function test_shipping_state_province_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['shipping_state_province' => fake()->paragraph(5, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_state_province'][0], 'The shipping state province field must not be greater than 100 characters.');
    }

    public function test_shipping_country_field_is_required()
    {
        $payload = $this->preparePayload(['shipping_country' => '']);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_country'][0], 'The shipping country field is required.');
    }

    public function test_shipping_country_must_not_be_greater_than_100_characters()
    {
        $payload = $this->preparePayload(['shipping_country' => fake()->paragraph(5, true)]);
        $response = $this->putJsonPayload($this->putRoute, $payload);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['shipping_country'][0], 'The shipping country field must not be greater than 100 characters.');
    }

    protected function preparePayload($data = [])
    {
        return array_merge([
            'shipping_first_name' => $this->user ? $this->user->first_name : fake()->firstName(),
            'shipping_last_name' => $this->user ? $this->user->last_name : fake()->lastName(),
            'shipping_email' => $this->user ? $this->user->email : fake()->safeEmail(),
            'shipping_contact' => fake()->phoneNumber(),
            'shipping_address_line_1' => fake()->buildingNumber(),
            'shipping_address_line_2' => fake()->streetName(),
            'shipping_area' => fake()->streetAddress(),
            'shipping_landmark' => fake()->streetSuffix(),
            'shipping_city' => fake()->city(),
            'shipping_postal_code' => Str::replace([' ', '-', '.'], [''], fake()->postcode()),
            'shipping_state_province' => fake()->citySuffix(),
            'shipping_country' => fake()->country(),
        ], $data);
    }
}
