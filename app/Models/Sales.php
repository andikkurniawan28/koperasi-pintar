<?php

namespace App\Models;

use App\Models\SalesProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $guarded = [];

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

        // Insert table sales
        $sales = Sales::create([
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

        // Insert table sales product
        foreach ($request->items as $item) {
            SalesProduct::create([
                'sales_id' => $sales->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'amount' => $item['amount'],
            ]);
        }

        // Insert table ledger
        Ledger::catatPenjualan($sales, $request);

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

        foreach ($request->items as $item) {
            SalesProduct::create([
                'sales_id'   => $sales->id,
                'product_id' => $item['product_id'],
                'qty'        => $item['qty'],
                'price'      => $item['price'],
                'amount'     => $item['amount'],
            ]);
        }

        Ledger::where('sales_id', $sales->id)->delete();

        Ledger::catatPenjualan($sales, $request);

        return $sales;
    }
}
