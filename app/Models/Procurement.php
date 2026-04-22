<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Procurement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'request_by',
        'warehouse_id',
        'status',
        'total_price',
        'purchase_at',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'deleted_by',
        'reason',
        'note',
    ];

    protected $casts = [
        'purchase_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'total_price' => 'integer',
    ];

    public function procurement_items()
    {
        return $this->hasMany(ProcurementItem::class);
    }

    public function userRequest()
    {
        return $this->belongsTo(User::class, 'request_by');
    }

    public function userApproved()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function userRejected()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function userDeleted()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}