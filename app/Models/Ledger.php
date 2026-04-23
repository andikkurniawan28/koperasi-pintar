<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function account(){
        return $this->belongsTo(Account::class);
    }

    public static function catatPenjualan($sales, $request)
    {
        self::insert([
            'date' => $sales->date,
            'user_id' => $sales->user_id,
            'account_id' => $sales->account_id,
            'description' => 'Penjualan '.$sales->code,
            'debit' => $sales->grand_total,
            'credit' => 0,
            'sales_id' => $sales->id,
        ]);
        if($sales->type == 'member'){
            $account_id = 13;
        } else {
            $account_id = 14;
        }

        self::insert([
            'date' => $sales->date,
            'user_id' => $sales->user_id,
            'account_id' => $account_id,
            'description' => 'Penjualan '.$sales->code,
            'credit' => $sales->grand_total,
            'debit' => 0,
            'sales_id' => $sales->id,
        ]);
    }
}
