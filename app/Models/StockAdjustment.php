<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::created(fn($row) => ActivityLog::log(auth()->id(), "Membuat stok opname ".$row->code));
        static::updated(fn($row) => ActivityLog::log(auth()->id(), "Update stok opname ".$row->code));
        static::deleted(fn($row) => ActivityLog::log(auth()->id(), "Hapus stok opname ".$row->code));
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    // =========================
    // CREATE
    // =========================
    public static function createData($request)
    {
        $code = 'ADJ'.date('YmdHis');

        $data = self::create([
            'code'       => $code,
            'date'       => $request->date,
            'inOut'      => $request->inOut,
            'product_id' => $request->product_id,
            'qty'        => $request->qty,
            'total'      => $request->total,
            'user_id'    => auth()->id(),
        ]);

        self::handleStock($data);
        Ledger::catatAdjustment($data);

        return $data;
    }

    // =========================
    // UPDATE
    // =========================
    public static function updateData($id, $request)
    {
        $data = self::findOrFail($id);

        // rollback dulu
        self::rollback($data);

        $data->update([
            'date'       => $request->date,
            'inOut'      => $request->inOut,
            'product_id' => $request->product_id,
            'qty'        => $request->qty,
            'total'      => $request->total,
        ]);

        self::handleStock($data);
        Ledger::catatAdjustment($data);

        return $data;
    }

    // =========================
    // STOCK HANDLER
    // =========================
    private static function handleStock($data)
    {
        StockLedger::create([
            'stock_adjustment_id' => $data->id,
            'product_id' => $data->product_id,
            'date'       => $data->date,
            'user_id'    => $data->user_id,
            'description'=> 'Stok Opname '.$data->code,
            'in'         => $data->inOut == 'in' ? $data->qty : 0,
            'out'        => $data->inOut == 'out' ? $data->qty : 0,
        ]);
    }

    public static function rollback($data)
    {
        StockLedger::where('stock_adjustment_id', $data->id)->delete();
        Ledger::where('stock_adjustment_id', $data->id)->delete();
    }
}
