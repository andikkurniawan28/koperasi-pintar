<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function account(){
        return $this->belongsTo(Account::class);
    }

    public function savings(){
        return $this->hasMany(Saving::class);
    }

    protected static function booted()
    {
        static::created(function ($saving_type) {
            ActivityLog::log(auth()->user()->id, "Membuat jenis simpanan ". $saving_type->name);
        });
        static::updated(function ($saving_type) {
            ActivityLog::log(auth()->user()->id, "Mengedit jenis simpanan ". $saving_type->name);
        });
        static::deleted(function ($saving_type) {
            ActivityLog::log(auth()->user()->id, "Menghapus jenis simpanan ". $saving_type->name);
        });
    }
}
