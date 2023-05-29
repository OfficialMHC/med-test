<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'id', 'title', 'sku', 'description'
    ];



    public function productVariantPrices()
    {
        return $this->hasMany(ProductVariantPrice::class);
    }
}
