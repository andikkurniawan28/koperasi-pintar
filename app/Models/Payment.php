<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function invoice(){
        return $this->belongsTo(Invoice::class);
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

    public function account(){
        return $this->belongsTo(Account::class);
    }

    public static function catatPembayaranDariInvoice($invoice){
        $code = 'RCP' . date('YmdHis');
        $payment = self::create([
            'code' => $code,
            'date' => $invoice->date,
            'invoice_id' => $invoice->id,
            'member_id' => $invoice->member_id ?? null,
            'customer_id' => $invoice->customer_id ?? null,
            'user_id' => auth()->id(),
            'total' => $invoice->paid,
            'account_id' => $invoice->account_id,
        ]);
        return $payment;
    }

    public static function createData($request)
    {
        $code = 'RCP' . date('YmdHis');
        $invoice = Invoice::findOrFail($request->invoice_id);
        $payment = self::create([
            'code'        => $code,
            'date'        => $request->date,
            'invoice_id'  => $invoice->id,
            'member_id'   => $invoice->member_id,
            'customer_id' => $invoice->customer_id,
            'user_id'     => auth()->id(),
            'total'       => $request->total,
            'account_id'  => $request->account_id,
        ]);

        $paid = self::where('invoice_id', $invoice->id)->sum('total');
        $left = $invoice->grand_total - $paid;

        $invoice->update([
            'paid' => $paid,
            'left' => $left
        ]);

        Ledger::catatPembayaran($payment);

        return $payment;
    }

    public static function deleteData($payment)
    {
        $invoice = $payment->invoice;

        // hapus ledger
        Ledger::where('payment_id', $payment->id)->delete();

        $payment->delete();

        // update ulang invoice
        $paid = self::where('invoice_id', $invoice->id)->sum('total');
        $left = $invoice->grand_total - $paid;

        $invoice->update([
            'paid' => $paid,
            'left' => $left
        ]);
    }
}
