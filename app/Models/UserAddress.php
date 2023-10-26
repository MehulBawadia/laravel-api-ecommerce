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
        'user_id', 'type',

        'first_name', 'last_name', 'email', 'contact',
        'address_line_1', 'address_line_2', 'area', 'landmark',
        'city', 'postal_code', 'state_province', 'country',
    ];

    public const BILLING = 1;

    public const SHIPPING = 2;

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
