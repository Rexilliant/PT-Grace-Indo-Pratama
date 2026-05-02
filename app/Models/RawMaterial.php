<?php

namespace App\Models;

use App\Models\ProcurementItem;
use App\Models\RawMaterialStock;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class RawMaterial extends Model
{
    use SoftDeletes;

    protected $table = 'raw_materials';

    protected $fillable = [
        'code',
        'name',
        'unit',
        'status',
        'deleted_by',
    ];

    public function stock()
    {
        return $this->hasOne(RawMaterialStock::class);
    }

    public function stocks()
    {
        return $this->hasMany(RawMaterialStock::class);
    }

    public function procurement_items()
    {
        return $this->hasMany(ProcurementItem::class);
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    protected static function booted()
    {
        static::deleting(function ($model) {
            if (Auth::check()) {
                $model->deleted_by = Auth::id();
                $model->save();
            }
        });
    }
}
