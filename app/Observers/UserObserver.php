<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserAddress;

class UserObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the User "created" event.
     */
    public function created(User $user)
    {
        $user->billingAddress()->create(['type' => UserAddress::BILLING]);
        $user->shippingAddress()->create(['type' => UserAddress::SHIPPING]);
    }
}
