<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcurementItem extends Model
{
    protected $fillable = [
        'procurement_id',
        'raw_material_id',
        'quantity_requested',
    ];

    public function procurement()
    {
        return $this->belongsTo(Procurement::class);
    }

    public function raw_material()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
