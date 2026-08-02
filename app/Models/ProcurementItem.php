<?php

namespace App\Models;

use App\Models\Procurement;
use App\Models\RawMaterial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
class ProcurementItem extends Model
{
    use SoftDeletes, LogsActivity;
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
