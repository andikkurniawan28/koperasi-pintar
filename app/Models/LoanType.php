<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function account(){
        return $this->belongsTo(Account::class);
    }

    public function savings(){
        return $this->hasMany(Loan::class);
    }

    protected static function booted()
    {
        static::created(function ($saving_type) {
            ActivityLog::log(auth()->user()->id, "Membuat jenis pinjaman ". $saving_type->name);
        });
        static::updated(function ($saving_type) {
            ActivityLog::log(auth()->user()->id, "Mengedit jenis pinjaman ". $saving_type->name);
        });
        static::deleted(function ($saving_type) {
            ActivityLog::log(auth()->user()->id, "Menghapus jenis pinjaman ". $saving_type->name);
        });
    }
}
