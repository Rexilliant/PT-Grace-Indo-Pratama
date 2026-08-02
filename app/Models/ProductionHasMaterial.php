<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class ProductionHasMaterial extends Model
{
    use LogsActivity;
    protected $table = 'production_has_materials';

    protected $fillable = [
        'production_batch_id',
        'raw_material_id',
        'stock',
        'quantity_use',
    ];

    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class);
    }

    public function rawMaterial()
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