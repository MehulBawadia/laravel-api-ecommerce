<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Keys
    |--------------------------------------------------------------------------
    |
    | The publishable key and the secrety key that is provided by the Stripe.
    | Create an account in Stripe and get it from the following url:
    | https://dashboard.stripe.com/test/developers
    |
    */
    'keys' => [
        'publishable' => env('STRIPE_PUBLISHABLE_KEY', null),
        'secret' => env('STRIPE_SECRET_KEY', null),
    ],
];
