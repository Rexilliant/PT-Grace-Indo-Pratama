<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItemFulfillment extends Model
{
    protected $fillable = [
        'sale_id',
        'sale_item_id',
        'product_stock_id',
        'quantity',
        'fulfillment_date',
        'created_by',
        'note',
    ];

    protected $casts = [
        'fulfillment_date' => 'date',
        'quantity' => 'integer',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function productStock()
    {
        return $this->belongsTo(ProductStock::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}