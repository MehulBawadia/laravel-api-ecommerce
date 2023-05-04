<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Send a post request in the JSON format.
     *
     * @param  string  $url
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function postJsonPayload($url, $data = [], $headers = [])
    {
        $allHeaders = array_merge([
            'Accept' => 'application/json',
        ], $headers);

        return $this->postJson($url, $data, $allHeaders);
    }

    /**
     * Send a put request in the JSON format.
     *
     * @param  string  $url
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function putJsonPayload($url, $data = [], $headers = [])
    {
        $allHeaders = array_merge([
            'Accept' => 'application/json',
        ], $headers);

        return $this->putJson($url, $data, $allHeaders);
    }

    /**
     * Create a user for testing.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    public function createUser($data = [])
    {
        $payload = array_merge([
            'email' => 'admin@example.com',
            'password' => bcrypt('Password'),
        ], $data);

        return User::factory()->create($payload);
    }

    /**
     * Sign in the administrator.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    public function signInAdmin($data = [])
    {
        $payload = array_merge([
            'first_name' => 'Super',
            'last_name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('Password'),
        ], $data);
        $user = $this->createUser($payload);

        Sanctum::actingAs($user, ['*']);

        return $user;
    }
}
