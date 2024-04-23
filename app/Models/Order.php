<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'user_id', 'user_details',
        'billing_address', 'shipping_address', 'total_amount',
    ];

    /**
     * Cast the attributes to their native types.
     *
     * @return array
     */
    protected function casts()
    {
        return [
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    /**
     * An order has many products.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }
}
