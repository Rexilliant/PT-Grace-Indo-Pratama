<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Shippment extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'shippment_code',
        'shippment_type',
        'person_responsible_id',
        'status',
        'warehouse_id',
        'address',
        'shippment_request_at',
        'created_by_id',
        'received_by_id',
        'approved_at',
        'approved_by_id',
        'rejected_at',
        'rejected_by_id',
        'reason',
        'cancelled_by_id',
        'cancelled_at',
        'shippment_at',
        'shippment_services',
        'received_at',
        'contact',
        'shipping_fleet',
        'notes',
    ];

    protected $casts = [
        'shippment_request_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'shippment_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function personResponsible()
    {
        return $this->belongsTo(User::class, 'person_responsible_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by_id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by_id');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by_id');
    }

    public function shippmentItems()
    {
        return $this->hasMany(ShippmentItem::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
