<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Brand extends Model
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
     * Do some processing with the model events related to Tag.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        Brand::creating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
        Brand::updating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }
}
