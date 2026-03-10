<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionBatch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'person_responsible_id',
        'product_stock_id',
        'province',
        'entry_date',
        'quantity',
        'note',
        'status',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public function personResponsible()
    {
        return $this->belongsTo(User::class, 'person_responsible_id');
    }

    public function productStock()
    {
        return $this->belongsTo(ProductStock::class);
    }

    public function materials()
    {
        return $this->hasMany(ProductionHasMaterial::class);
    }
}