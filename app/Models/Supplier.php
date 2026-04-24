<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($supplier) {
            ActivityLog::log(auth()->user()->id, "Membuat supplier ". $supplier->name);
        });
        static::updated(function ($supplier) {
            ActivityLog::log(auth()->user()->id, "Mengedit supplier ". $supplier->name);
        });
        static::deleted(function ($supplier) {
            ActivityLog::log(auth()->user()->id, "Menghapus supplier ". $supplier->name);
        });
    }
}
