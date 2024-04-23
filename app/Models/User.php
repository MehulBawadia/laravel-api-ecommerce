<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
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
            'email_verified_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    /**
     * A user may have multiple billing addresses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function billingAddress()
    {
        return $this->hasMany(UserAddress::class)
            ->where('type', UserAddress::BILLING);
    }

    /**
     * A user may have multiple shipping addresses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shippingAddress()
    {
        return $this->hasMany(UserAddress::class)
            ->where('type', UserAddress::SHIPPING);
    }

    /**
     * A user may have multiple products in their wishlist.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productWishlist()
    {
        return $this->hasMany(ProductWishlist::class);
    }

    /**
     * A user has many products in their cart.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartProducts()
    {
        return $this->hasMany(CartProduct::class);
    }

    /**
     * Add a product in the cart of the auth user with a default quantity of 1
     * which can be overriden in the second paramter.
     *
     * @param  \App\Models\Product  $product
     * @param  int  $quantity
     * @return \App\Models\CartProduct
     */
    public function addProductInCart($product, $quantity = 1)
    {
        return $this->cartProducts()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'quantity' => $quantity,
            'rate' => $product->rate,
            'amount' => (float) ($product->rate * (int) $quantity),
        ]);
    }
}
