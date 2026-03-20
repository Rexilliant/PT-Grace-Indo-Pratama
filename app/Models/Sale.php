<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Sale extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'report_date',
        'sale_date',
        'person_responsible_id',
        'warehouse_id',
        'sale_type',
        'customer_province',
        'customer_city',
        'customer_address',
        'customer_name',
        'customer_contact',
        'total_amount',
        'paid_amount',
        'debt_amount',
        'notes',
        'status',
    ];

    protected $casts = [
        'report_date' => 'date',
        'sale_date' => 'date',
        'total_amount' => 'integer',
        'paid_amount' => 'integer',
        'debt_amount' => 'integer',
    ];

    public function personResponsible()
    {
        return $this->belongsTo(User::class, 'person_responsible_id');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function paymentHistories()
    {
        return $this->hasMany(HistorySalePayment::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('invoice_payment')->singleFile();
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}