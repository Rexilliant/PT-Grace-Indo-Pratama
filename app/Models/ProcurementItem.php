<?php

namespace App\Models;

use App\Models\Procurement;
use App\Models\RawMaterial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcurementItem extends Model
{
    use SoftDeletes;
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
