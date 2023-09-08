<?php

return [

    /*
    | ----------------------------------------------------------------------------
    | API Response Messages
    | ----------------------------------------------------------------------------
    |
    | The following language lines are used during API response for various
    | messages that we need to display to the user.
    |
    */

    'auth' => [
        'logged_in' => ':userType logged in successfully.',
        'logged_out' => ':userType logged out successfully.',
        'could_not_login' => 'Could not login the :userType. Try again later.',

        'password_reset_link_sent' => 'Password reset link sent successfullly.',
        'failed_sending_password_reset_link' => 'Could not send password reset link. Try again later.',

        'reset_password' => 'Password reset successfully.',
        'reset_password_token_expired' => 'Token expired. Generate a reset token.',
        'reset_password_user_not_found' => 'User with the provided email address not found.',
        'could_not_reset_password' => 'Could not reset password. Try again later.',
    ],
];
