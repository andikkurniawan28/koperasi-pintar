<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($customer) {
            ActivityLog::log(auth()->user()->id, "Membuat customer ". $customer->name);
        });
        static::updated(function ($customer) {
            ActivityLog::log(auth()->user()->id, "Mengedit customer ". $customer->name);
        });
        static::deleted(function ($customer) {
            ActivityLog::log(auth()->user()->id, "Menghapus customer ". $customer->name);
        });
    }
}
