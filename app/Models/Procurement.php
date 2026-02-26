<?php

namespace App\Models;

use App\Models\ProcurementItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Procurement extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'request_by',
        'province',
        'status',
        'total_price',
        'purchase_at',
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
