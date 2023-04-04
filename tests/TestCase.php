<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

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
}
