<?php

namespace App\Models;

use App\Models\PurchaseReceipt;
use App\Models\RawMaterial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PurchaseReceiptItem extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'purchase_receipt_id',
        'raw_material_id',
        'quantity_received',
    ];

    public function purchaseReceipt()
    {
        return $this->belongsTo(PurchaseReceipt::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
