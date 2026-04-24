<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($product) {
            ActivityLog::log(auth()->user()->id, "Membuat product ". $product->name);
        });
        static::updated(function ($product) {
            ActivityLog::log(auth()->user()->id, "Mengedit product ". $product->name);
        });
        static::deleted(function ($product) {
            ActivityLog::log(auth()->user()->id, "Menghapus product ". $product->name);
        });
    }
}
