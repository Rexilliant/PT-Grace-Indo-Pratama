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
        'deleted_by',
        'type',
    ];

    protected $dates = [
        'deleted_at',
    ];


    /**
     * Relasi ke user yang menghapus data
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Relasi ke stock (contoh kalau ada raw_material_stocks)
     */
    public function rawMaterialStocks()
    {
        return $this->hasMany(RawMaterialStock::class);
    }

    public function procurements()
    {
        return $this->hasMany(Procurement::class);
    }

    public function productStocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
