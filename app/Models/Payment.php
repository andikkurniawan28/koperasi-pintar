<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function invoice(){ return $this->belongsTo(Invoice::class); }
    public function member(){ return $this->belongsTo(Member::class); }
    public function customer(){ return $this->belongsTo(Customer::class); }
    public function user(){ return $this->belongsTo(User::class); }
    public function account(){ return $this->belongsTo(Account::class); }

    // =========================
    // SYNC INVOICE
    // =========================
    public static function syncInvoice($invoice)
    {
        $paid = self::where('invoice_id', $invoice->id)->sum('total');
        $left = $invoice->grand_total - $paid;

        if ($left < 0) $left = 0;

        if ($paid == 0) {
            $status = 'Belum Bayar';
        } elseif ($left == 0) {
            $status = 'Lunas';
        } else {
            $status = 'DP';
        }

        $invoice->update([
            'paid' => $paid,
            'left' => $left,
            'status' => $status,
        ]);
    }

    // =========================
    // CREATE DP DARI INVOICE
    // =========================
    public static function createFromInvoice($invoice, $amount, $request)
    {
        $payment = self::create([
            'code' => 'RCP'.date('YmdHis'),
            'date' => $invoice->date,
            'invoice_id' => $invoice->id,
            'member_id' => $invoice->member_id,
            'customer_id' => $invoice->customer_id,
            'user_id' => auth()->id(),
            'total' => $amount,
            'account_id' => $request->account_id,
        ]);

        self::syncInvoice($invoice);

        Ledger::catatPembayaran($payment);

        return $payment;
    }

    // =========================
    // CREATE MANUAL
    // =========================
    public static function createData($request)
    {
        $invoice = Invoice::findOrFail($request->invoice_id);

        $payment = self::create([
            'code' => 'RCP'.date('YmdHis'),
            'date' => $request->date,
            'invoice_id' => $invoice->id,
            'member_id' => $invoice->member_id,
            'customer_id' => $invoice->customer_id,
            'user_id' => auth()->id(),
            'total' => $request->total,
            'account_id' => $request->account_id,
        ]);

        self::syncInvoice($invoice);

        Ledger::catatPembayaran($payment);

        return $payment;
    }

    // =========================
    // UPDATE PAYMENT (🔥 NEW)
    // =========================
    public static function updateData($id, $request)
    {
        $payment = self::findOrFail($id);
        $invoice = $payment->invoice;

        // update data
        $payment->update([
            'date' => $request->date,
            'total' => $request->total,
            'account_id' => $request->account_id,
        ]);

        // update ledger (hapus lama → insert ulang)
        Ledger::where('payment_id', $payment->id)->delete();
        Ledger::catatPembayaran($payment);

        // sync invoice
        self::syncInvoice($invoice);

        return $payment;
    }

    // =========================
    // DELETE
    // =========================
    public static function deleteData($payment)
    {
        $invoice = $payment->invoice;

        Ledger::where('payment_id', $payment->id)->delete();

        $payment->delete();

        self::syncInvoice($invoice);
    }
}
