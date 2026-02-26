<?php

namespace App\Models;

use App\Models\RawMaterial;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawMaterialStockMovement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'province',
        'raw_material_id',
        'type',
        'stock',
        'ref_type',
        'ref_id',
        'responsible_id',
        'note',
    ];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }
}
