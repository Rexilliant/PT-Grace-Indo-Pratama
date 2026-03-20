<?php

namespace App\Models;

use App\Models\RawMaterial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawMaterialStock extends Model
{
    use SoftDeletes;

    protected $table = 'raw_material_stocks';

    protected $fillable = [
        'raw_material_id',
        'stock',
        'warehouse_id',
    ];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
