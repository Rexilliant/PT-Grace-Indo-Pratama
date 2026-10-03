<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
class Procurement extends Model
{
    use SoftDeletes, LogsActivity;

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

    public function purchaseReceipts()
    {
        return $this->hasMany(PurchaseReceipt::class);
    }

    public function canBeDeleted(): bool
    {
        if ($this->status === 'Menunggu') {
            return true;
        }

        $hasReceipts = isset($this->purchase_receipts_count)
            ? $this->purchase_receipts_count > 0
            : (isset($this->purchase_receipts_exists) ? $this->purchase_receipts_exists : $this->purchaseReceipts()->exists());

        return !$hasReceipts;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}