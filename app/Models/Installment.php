<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;

    protected $guarded = [];

    // =========================
    // BOOT (LOG)
    // =========================
    protected static function booted()
    {
        static::created(function ($item) {
            ActivityLog::log(auth()->id(), "Membayar angsuran " . $item->code);
        });

        static::updated(function ($item) {
            ActivityLog::log(auth()->id(), "Mengubah angsuran " . $item->code);
        });

        static::deleted(function ($item) {
            ActivityLog::log(auth()->id(), "Menghapus angsuran " . $item->code);
        });
    }

    // =========================
    // RELATION
    // =========================
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // =========================
    // CREATE DATA
    // =========================
    public static function createData($request)
    {
        $loan = Loan::findOrFail($request->loan_id);

        // ambil cicilan standar
        $installmentAmount = $loan->installment;

        // bunga per bulan (simple)
        $interest = $loan->total_interest / $loan->tenor;

        // denda (basic: bisa kamu kembangkan nanti)
        $penalty = $request->penalty ?? 0;

        // pokok = total - bunga - denda
        $principal = $installmentAmount - $interest - $penalty;

        // total bayar
        $total = $principal + $interest + $penalty;

        // angsuran ke-
        $installmentNumber = self::where('loan_id', $loan->id)->count() + 1;

        // hitung sisa hutang
        $remaining = $loan->remaining_balance - $principal;

        $item = self::create([
            'code'               => 'INS' . date('YmdHis'),
            'date'               => $request->date,

            'loan_id'            => $loan->id,
            'member_id'          => $loan->member_id,

            'installment_number' => $installmentNumber,

            'total'              => $total,
            'principal'          => $principal,
            'interest'           => $interest,
            'penalty'            => $penalty,

            'remaining_balance'  => $remaining,

            'account_id'         => $request->account_id,
            'user_id'            => auth()->id(),
        ]);

        // =========================
        // UPDATE LOAN
        // =========================
        $loan->update([
            'remaining_balance' => $remaining,
            'status' => $remaining <= 0 ? 'paid' : 'ongoing',
        ]);

        // =========================
        // LEDGER
        // =========================
        Ledger::catatPembayaranLoan($loan, $item);

        return $item;
    }

    // =========================
    // DELETE DATA
    // =========================
    public static function deleteData($item)
    {
        $loan = $item->loan;

        // rollback saldo
        $loan->remaining_balance += $item->principal;

        $loan->status = 'ongoing';
        $loan->save();

        // hapus ledger
        Ledger::where('loan_id', $loan->id)
            ->whereDate('date', $item->date)
            ->delete();

        $item->delete();
    }
}
