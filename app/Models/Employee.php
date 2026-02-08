<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nip',
        'name',
        'birthday',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'photo',
    ];

    // relasi ke user
    public function user()
    {
        return $this->hasOne(User::class);
    }
}
