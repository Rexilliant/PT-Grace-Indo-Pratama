<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procurement extends Model
{
    protected $fillable = [
        'request_by',
        'province',
        'status',
        'total_price',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'reason',
        'note',
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
}
