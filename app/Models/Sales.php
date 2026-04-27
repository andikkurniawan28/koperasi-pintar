<?php

namespace App\Models;

use App\Models\SalesProduct;
use App\Models\StockLedger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($sales) {
            ActivityLog::log(auth()->user()->id, "Membuat penjualan ". $sales->code);
        });
        static::updated(function ($sales) {
            ActivityLog::log(auth()->user()->id, "Mengedit penjualan ". $sales->code);
        });
        static::deleted(function ($sales) {
            ActivityLog::log(auth()->user()->id, "Menghapus penjualan ". $sales->code);
        });
    }

    public function member(){
        return $this->belongsTo(Member::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function product(){
        return $this->hasMany(SalesProduct::class);
    }

    public static function catatPenjualan($request)
    {
        $code = 'SLS' . date('YmdHis');

        $sales = self::create([
            'type' => $request->type,
            'code' => $code,
            'date' => $request->date,
            'member_id' => $request->member_id ?? null,
            'customer_id' => $request->customer_id ?? null,
            'user_id' => auth()->id(),
            'subtotal' => $request->subtotal,
            'discount' => $request->discount,
            'expenses' => $request->expenses,
            'taxes' => $request->taxes,
            'grand_total' => $request->grand_total,
            'account_id' => $request->account_id,
        ]);

        $totalHpp = 0;

        foreach ($request->items as $item) {

            $product = Product::find($item['product_id']);

            $hpp = $product->buy_price * $item['qty'];

            $totalHpp += $hpp;

            SalesProduct::create([
                'sales_id' => $sales->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'amount' => $item['amount'],
            ]);

            StockLedger::create([
                'sales_id' => $sales->id,
                'product_id' => $item['product_id'],
                'date' => $sales->date,
                'user_id' => $sales->user_id,
                'description' => "Penjualan toko ". $sales->code,
                'out' => $item['qty'],
                'in' => 0,
            ]);
        }

        Ledger::catatPenjualan($sales, $request, $totalHpp);

        return $sales;
    }

    public static function updatePenjualan($id, $request)
    {
        $sales = Sales::findOrFail($id);

        $sales->update([
            'type'        => $request->type,
            'date'        => $request->date,
            'member_id'   => $request->member_id ?? null,
            'customer_id' => $request->customer_id ?? null,
            'subtotal'    => $request->subtotal,
            'discount'    => $request->discount,
            'expenses'    => $request->expenses,
            'taxes'       => $request->taxes,
            'grand_total' => $request->grand_total,
            'account_id'  => $request->account_id,
        ]);

        SalesProduct::where('sales_id', $sales->id)->delete();
        StockLedger::where('sales_id', $sales->id)->delete();
        Ledger::where('sales_id', $sales->id)->delete();

        $totalHpp = 0;

        foreach ($request->items as $item) {

            $product = Product::find($item['product_id']);
            $hpp = $product->buy_price * $item['qty'];
            $totalHpp += $hpp;

            SalesProduct::create([
                'sales_id'   => $sales->id,
                'product_id' => $item['product_id'],
                'qty'        => $item['qty'],
                'price'      => $item['price'],
                'amount'     => $item['amount'],
            ]);

            StockLedger::create([
                'sales_id' => $sales->id,
                'product_id' => $item['product_id'],
                'date' => $sales->date,
                'user_id' => $sales->user_id,
                'description' => "Penjualan toko ". $sales->code,
                'out' => $item['qty'],
                'in' => 0,
            ]);
        }

        Ledger::catatPenjualan($sales, $request, $totalHpp);

        return $sales;
    }
}
