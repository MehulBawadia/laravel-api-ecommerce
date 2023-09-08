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

    'account_settings' => [
        'general' => 'General settings updated successfully.',
        'failed_updating_general_settings' => 'Could not update general settings. Try again later.',

        'change_password' => 'Password changed successfully.',
        'failed_change_password' => 'Could not change password. Try again later.',
    ],

    'user' => [
        'address' => ':addressType address updated successfully.',
        'address_failed' => 'Could not update :addressType address. Try again later.',
    ],

    'admin' => [
        'brands' => [
            'success' => 'Brand :actionType successfully.',
            'not_found' => 'Brand not found.',
            'failed' => 'Could not :actionType the brand. Try again later.',
        ],

        'tags' => [
            'success' => 'Tag :actionType successfully.',
            'not_found' => 'Tag not found.',
            'failed' => 'Could not :actionType the tag. Try again later.',
        ],
    ],
];
