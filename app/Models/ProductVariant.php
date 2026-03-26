<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductVariant extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'pack_size',
        'unit',
        'price',
        'status',
    ];

    protected $casts = [
        'pack_size' => 'integer',
        'price' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productStocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product_variant_image')->singleFile();
    }
}