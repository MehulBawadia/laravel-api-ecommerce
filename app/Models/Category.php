<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug', 'description',
        'meta_title', 'meta_description', 'meta_keywords',
    ];

    /**
     * Cast the attributes to their native types.
     *
     * @var array
     */
    protected $casts = [
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Do some processing with the model events related to Category.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        Category::creating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
        Category::updating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }
}
