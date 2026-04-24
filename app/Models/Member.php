<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($member) {
            ActivityLog::log(auth()->user()->id, "Membuat anggota ". $member->name);
        });
        static::updated(function ($member) {
            ActivityLog::log(auth()->user()->id, "Mengedit anggota ". $member->name);
        });
        static::deleted(function ($member) {
            ActivityLog::log(auth()->user()->id, "Menghapus anggota ". $member->name);
        });
    }
}
