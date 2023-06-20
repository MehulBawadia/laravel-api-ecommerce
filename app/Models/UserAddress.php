<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',

        'billing_first_name', 'billing_last_name', 'billing_email', 'billing_contact',
        'billing_address_line_1', 'billing_address_line_2', 'billing_area', 'billing_landmark',
        'billing_city', 'billing_postal_code', 'billing_state_province', 'billing_country',

        'shipping_first_name', 'shipping_last_name', 'shipping_email', 'shipping_contact',
        'shipping_address_line_1', 'shipping_address_line_2', 'shipping_area', 'shipping_landmark',
        'shipping_city', 'shipping_postal_code', 'shipping_state_province', 'shipping_country',
    ];

    /**
     * An address belongs to a single user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
