<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [];

    public function role(){
        return $this->belongsTo(Role::class);
    }

    protected static function booted()
    {
        static::created(function ($user) {
            ActivityLog::log(auth()->user()->id, "Membuat user ". $user->name);
        });
        static::updated(function ($user) {
            ActivityLog::log(auth()->user()->id, "Mengedit user ". $user->name);
        });
        static::deleted(function ($user) {
            ActivityLog::log(auth()->user()->id, "Menghapus user ". $user->name);
        });
    }
}
