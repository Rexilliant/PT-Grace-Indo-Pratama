<?php

namespace App\Models;

use App\Models\ShipmentItem;
use App\Models\ShipmentReceipt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentReceiptItem extends Model
{
    use SoftDeletes;

    protected $table = 'shipment_receipt_items';

    protected $fillable = [
        'shipment_receipt_id',
        'shipment_item_id',
        'qty_received',
        'notes',
    ];

    protected $casts = [
        'qty_received' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function shipmentReceipt()
    {
        return $this->belongsTo(ShipmentReceipt::class);
    }

    public function shipmentItem()
    {
        return $this->belongsTo(ShipmentItem::class);
    }
}
