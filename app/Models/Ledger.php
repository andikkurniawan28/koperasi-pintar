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

    public static function catatPenjualan($sales, $request, $totalHpp)
    {
        // Kas / Bank
        self::insert([
            'date' => $sales->date,
            'user_id' => $sales->user_id,
            'account_id' => $sales->account_id,
            'description' => 'Penjualan '.$sales->code,
            'debit' => $sales->grand_total,
            'credit' => 0,
            'sales_id' => $sales->id,
        ]);

        // Pendapatan
        $account_id = ($sales->type == 'member') ? 13 : 14;

        self::insert([
            'date' => $sales->date,
            'user_id' => $sales->user_id,
            'account_id' => $account_id,
            'description' => 'Penjualan '.$sales->code,
            'credit' => $sales->grand_total,
            'debit' => 0,
            'sales_id' => $sales->id,
        ]);

        // HPP
        if ($totalHpp > 0) {

            // Debit HPP
            self::insert([
                'date' => $sales->date,
                'user_id' => $sales->user_id,
                'account_id' => 20,
                'description' => 'HPP '.$sales->code,
                'debit' => $totalHpp,
                'credit' => 0,
                'sales_id' => $sales->id,
            ]);

            // Credit Persediaan
            self::insert([
                'date' => $sales->date,
                'user_id' => $sales->user_id,
                'account_id' => 5,
                'description' => 'Persediaan keluar '.$sales->code,
                'credit' => $totalHpp,
                'debit' => 0,
                'sales_id' => $sales->id,
            ]);
        }
    }
}
