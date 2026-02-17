<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawMaterialStock extends Model
{
    use SoftDeletes;

    protected $table = 'raw_material_stocks';

    protected $fillable = [
        'raw_material_id',
        'stock',
        'province',
    ];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
