<?php

namespace App\Models;

use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($invoice) {
            ActivityLog::log(auth()->user()->id, "Membuat tagihan ". $invoice->code);
        });
        static::updated(function ($invoice) {
            ActivityLog::log(auth()->user()->id, "Mengedit tagihan ". $invoice->code);
        });
        static::deleted(function ($invoice) {
            ActivityLog::log(auth()->user()->id, "Menghapus tagihan ". $invoice->code);
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

    public function account(){
        return $this->belongsTo(Account::class);
    }

    public function item(){
        return $this->hasMany(InvoiceItem::class);
    }

    public static function catatTagihan($request)
    {
        $code = 'INV' . date('YmdHis');

        $invoice = self::create([
            'type' => $request->type,
            'code' => $code,
            'date' => $request->date,
            'due_date' => $request->due_date,
            'member_id' => $request->member_id ?? null,
            'customer_id' => $request->customer_id ?? null,
            'user_id' => auth()->id(),
            'subtotal' => $request->subtotal,
            'discount' => $request->discount,
            'expenses' => $request->expenses,
            'taxes' => $request->taxes,
            'grand_total' => $request->grand_total,
            'paid' => $request->paid,
            'left' => $request->left,
            'account_id' => $request->account_id,
            'status' => 'Belum Bayar',
        ]);

        foreach ($request->items as $item) {

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'name' => $item['name'],
                'description' => $item['description'],
                'amount' => $item['amount'],
            ]);
        }

        Ledger::catatTagihan($invoice, $request);

        if($invoice->paid > 0)
        {
            $payment = Payment::catatPembayaranDariInvoice($invoice);
            Ledger::catatPembayaran($payment);
        }

        return $invoice;
    }

    public static function updateTagihan($id, $request)
    {
        $invoice = Invoice::findOrFail($id);

        $invoice->update([
            'type'        => $request->type,
            'date'        => $request->date,
            'due_date'    => $request->due_date,
            'member_id'   => $request->member_id ?? null,
            'customer_id' => $request->customer_id ?? null,
            'subtotal'    => $request->subtotal,
            'discount'    => $request->discount,
            'expenses'    => $request->expenses,
            'taxes'       => $request->taxes,
            'grand_total' => $request->grand_total,
            'paid'        => $request->paid,
            'left'        => $request->left,
            'account_id'  => $request->account_id,
        ]);

        // =========================
        // RESET DATA
        // =========================
        InvoiceItem::where('invoice_id', $invoice->id)->delete();

        // HAPUS LEDGER lama
        Ledger::where('invoice_id', $invoice->id)->delete();

        // HAPUS PAYMENT lama (biar tidak double)
        Payment::where('invoice_id', $invoice->id)->delete();

        // =========================
        // INSERT ITEM BARU
        // =========================
        foreach ($request->items as $item) {

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'name'       => $item['name'],
                'description'=> $item['description'],
                'amount'     => $item['amount'],
            ]);
        }

        // =========================
        // LEDGER TAGIHAN
        // =========================
        Ledger::catatTagihan($invoice, $request);

        // =========================
        // JIKA ADA PEMBAYARAN
        // =========================
        if ($invoice->paid > 0) {

            $payment = Payment::catatPembayaranDariInvoice($invoice);

            Ledger::catatPembayaran($payment);
        }

        return $invoice;
    }
}
