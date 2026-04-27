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

    public function savings()
    {
        return $this->hasMany(Saving::class);
    }

    public static function savingBalance($member_id, $saving_type_id)
    {
        $in = Saving::where('member_id', $member_id)
            ->where('saving_type_id', $saving_type_id)
            ->where('direction', 'in')
            ->sum('total');

        $out = Saving::where('member_id', $member_id)
            ->where('saving_type_id', $saving_type_id)
            ->where('direction', 'out')
            ->sum('total');

        return $in - $out;
    }
}
