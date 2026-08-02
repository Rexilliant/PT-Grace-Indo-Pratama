<?php

namespace App\Models;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Employee extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes, LogsActivity;

    protected $fillable = ['nip', 'name', 'position', 'birthday', 'email', 'phone', 'address', 'city', 'province', 'postal_code', 'country', 'warehouse_id'];

    public function user()
    {
        return $this->hasOne(User::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_images')->singleFile();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
