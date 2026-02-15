<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Employee extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'nip',
        'name',
        'position',
        'birthday',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
    ];

    // relasi ke user
    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function registerMediaCollections(): void
    {
        // hanya 1 file, upload baru akan replace yang lama
        $this->addMediaCollection('profile_images')
            ->singleFile();
    }
}
