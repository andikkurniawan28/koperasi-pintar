<?php

namespace App\Models;

use App\Models\PurchaseProduct;
use App\Models\StockLedger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($purchase) {
            ActivityLog::log(auth()->user()->id, "Membuat pembelian ". $purchase->code);
        });
        static::updated(function ($purchase) {
            ActivityLog::log(auth()->user()->id, "Mengedit pembelian ". $purchase->code);
        });
        static::deleted(function ($purchase) {
            ActivityLog::log(auth()->user()->id, "Menghapus pembelian ". $purchase->code);
        });
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function product(){
        return $this->hasMany(PurchaseProduct::class);
    }

    public static function catatPembelian($request)
    {
        $code = 'PCS' . date('YmdHis');

        $purchase = self::create([
            'code' => $code,
            'date' => $request->date,
            'supplier_id' => $request->supplier_id,
            'user_id' => auth()->id(),
            'subtotal' => $request->subtotal,
            'discount' => $request->discount,
            'expenses' => $request->expenses,
            'taxes' => $request->taxes,
            'grand_total' => $request->grand_total,
            'account_id' => $request->account_id,
        ]);

        foreach ($request->items as $item) {

            PurchaseProduct::create([
                'purchase_id' => $purchase->id, // (biarin dulu kalau schema masih ini)
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'amount' => $item['amount'],
            ]);

            // ✅ STOCK MASUK
            StockLedger::create([
                'purchase_id' => $purchase->id,
                'product_id' => $item['product_id'],
                'date' => $purchase->date,
                'user_id' => $purchase->user_id,
                'description' => "Pembelian ". $purchase->code,
                'in' => $item['qty'],
                'out' => 0,
            ]);
        }

        // ✅ JURNAL PEMBELIAN
        Ledger::catatPembelian($purchase);

        return $purchase;
    }

    public static function updatePembelian($id, $request)
    {
        $purchase = self::findOrFail($id);

        $purchase->update([
            'date'        => $request->date,
            'supplier_id' => $request->supplier_id,
            'subtotal'    => $request->subtotal,
            'discount'    => $request->discount,
            'expenses'    => $request->expenses,
            'taxes'       => $request->taxes,
            'grand_total' => $request->grand_total,
            'account_id'  => $request->account_id,
        ]);

        PurchaseProduct::where('purchase_id', $purchase->id)->delete();
        StockLedger::where('purchase_id', $purchase->id)->delete();
        Ledger::where('purchase_id', $purchase->id)->delete();

        foreach ($request->items as $item) {

            PurchaseProduct::create([
                'purchase_id'   => $purchase->id,
                'product_id' => $item['product_id'],
                'qty'        => $item['qty'],
                'price'      => $item['price'],
                'amount'     => $item['amount'],
            ]);

            // ✅ STOCK MASUK
            StockLedger::create([
                'purchase_id' => $purchase->id,
                'product_id' => $item['product_id'],
                'date' => $purchase->date,
                'user_id' => $purchase->user_id,
                'description' => "Pembelian ". $purchase->code,
                'in' => $item['qty'],
                'out' => 0,
            ]);
        }

        Ledger::catatPembelian($purchase);

        return $purchase;
    }
}
