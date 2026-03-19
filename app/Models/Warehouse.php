<?php

namespace App\Models;

use App\Models\RawMaterialStock;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes;

    protected $table = 'warehouses';

    protected $fillable = [
        'name',
        'province',
        'city',
        'responsible_id',
    ];

    protected $dates = [
        'deleted_at',
    ];

    /**
     * Relasi ke user (penanggung jawab)
     */
    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    /**
     * Relasi ke stock (contoh kalau ada raw_material_stocks)
     */
    public function rawMaterialStocks()
    {
        return $this->hasMany(RawMaterialStock::class);
    }
    public function procurements(){
        return this->hasMany(procurements::class);
    }
}
