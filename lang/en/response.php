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
        'register' => 'You have registered successfully.',
        'could_not_register' => 'Could not register you. Try again later.',

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
        'address' => [
            'success' => ':type address :action successfully.',
            'not_found' => ':type address not found.',
            'failed' => 'Could not :action the :type address. Try again later.',
        ],

        'wishlist' => [
            'success' => 'Product has been successfully :action.',
            'not_found' => 'Product not found with the provided id.',
            'failed' => 'Could not :action the product. Try again later.',
            'product_exists' => 'Product already exists in your wishlist.',
        ],

        'checkout_address' => [
            'success' => ':type address successfully selected.',
            'not_found' => ':type address not found with the provided id.',
            'failed' => 'Could not select the :type address.',
        ],
    ],

    'cart' => [
        'product_added' => 'Product added in the cart successfully.',
        'product_not_found' => 'Product not found with the provided id.',
        'product_updated' => 'Product quantity updated in the cart successfully.',
        'product_removed' => 'Product removed from the cart successfully.',
        'empty' => 'Cart emptied successfully.',
    ],

    'admin' => [
        'generate' => [
            'success' => 'Administrator generated successfully.',
            'failed' => 'Could not generate the administrator. Try again later.',
        ],

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

        'category' => [
            'success' => 'Category :actionType successfully.',
            'not_found' => 'Category not found.',
            'failed' => 'Could not :actionType the category. Try again later.',
        ],

        'products' => [
            'success' => 'Product :actionType successfully.',
            'not_found' => 'Product not found.',
            'failed' => 'Could not :actionType the product. Try again later.',
        ],
    ],
];
