<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GeneratesAdministratorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Generates an administrator test.
     *
     */
    public function test_generates_an_administrator(): void
    {
        $this->withoutExceptionHandling();

        $response = $this->postJson('/api/v1/admin/generate', [
            'first_name' => 'Super',
            'last_name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('Password'),
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $this->assertEquals(1, User::count());
    }
}
