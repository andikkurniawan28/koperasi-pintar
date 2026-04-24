<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public static function log($user_id, $description){
        ActivityLog::insert([
            'user_id' => $user_id,
            'description' => $description,
        ]);
    }
}
