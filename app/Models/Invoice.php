<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::created(fn($i) => ActivityLog::log(auth()->id(), "Membuat tagihan ".$i->code));
        static::updated(fn($i) => ActivityLog::log(auth()->id(), "Mengedit tagihan ".$i->code));
        static::deleted(fn($i) => ActivityLog::log(auth()->id(), "Menghapus tagihan ".$i->code));
    }

    public function member(){ return $this->belongsTo(Member::class); }
    public function customer(){ return $this->belongsTo(Customer::class); }
    public function user(){ return $this->belongsTo(User::class); }
    public function account(){ return $this->belongsTo(Account::class); }
    public function item(){ return $this->hasMany(InvoiceItem::class); }

    // =========================
    // CREATE
    // =========================
    public static function catatTagihan($request)
    {
        $invoice = self::create([
            'type' => $request->type,
            'code' => 'INV'.date('YmdHis'),
            'date' => $request->date,
            'due_date' => $request->due_date,
            'member_id' => $request->member_id,
            'customer_id' => $request->customer_id,
            'user_id' => auth()->id(),
            'subtotal' => $request->subtotal,
            'discount' => $request->discount,
            'expenses' => $request->expenses,
            'taxes' => $request->taxes,
            'grand_total' => $request->grand_total,
            'paid' => 0,
            'left' => $request->grand_total,
            'status' => 'Belum Bayar',
            // 'account_id' => $request->account_id,
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

        // DP langsung
        if ($request->paid > 0) {
            Payment::createFromInvoice($invoice, $request->paid);
        }

        return $invoice;
    }

    // =========================
    // UPDATE
    // =========================
    public static function updateTagihan($id, $request)
    {
        $invoice = self::findOrFail($id);

        $invoice->update([
            'type' => $request->type,
            'date' => $request->date,
            'due_date' => $request->due_date,
            'member_id' => $request->member_id,
            'customer_id' => $request->customer_id,
            'subtotal' => $request->subtotal,
            'discount' => $request->discount,
            'expenses' => $request->expenses,
            'taxes' => $request->taxes,
            'grand_total' => $request->grand_total,
            // 'paid' => $request->paid,
            // 'left' => $request->left,
            // 'account_id' => $request->account_id,
        ]);

        // reset item & ledger saja
        InvoiceItem::where('invoice_id', $invoice->id)->delete();
        Ledger::where('invoice_id', $invoice->id)->delete();

        foreach ($request->items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'name' => $item['name'],
                'description' => $item['description'],
                'amount' => $item['amount'],
            ]);
        }

        Ledger::catatTagihan($invoice, $request);

        // sinkron ulang (JANGAN HAPUS PAYMENT)
        Payment::syncInvoice($invoice);

        return $invoice;
    }
}
