<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug', 'description', 'quantity', 'rate',
        'brand_id', 'category_id',
        'meta_title', 'meta_description', 'meta_keywords',
    ];

    /**
     * A product belongs to a single brand.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * A product belongs to a single category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Register the media collection for generating images.
     * It will also generate necessary responsive images.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product-images')
            ->withResponsiveImages();
    }

    /**
     * Do some processing with the model events related to Product.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        Product::creating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
        Product::updating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }
}
