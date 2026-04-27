<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Loan extends Model
{
    use HasFactory;

    protected $guarded = [];

    // =========================
    // BOOT (LOG)
    // =========================
    protected static function booted()
    {
        static::created(function ($loan) {
            ActivityLog::log(auth()->id(), "Membuat pinjaman ".$loan->code);
        });

        static::updated(function ($loan) {
            ActivityLog::log(auth()->id(), "Mengubah pinjaman ".$loan->code);
        });

        static::deleted(function ($loan) {
            ActivityLog::log(auth()->id(), "Menghapus pinjaman ".$loan->code);
        });
    }

    // =========================
    // RELATION
    // =========================
    public function loanType()
    {
        return $this->belongsTo(LoanType::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    // =========================
    // CREATE DATA
    // =========================
    public static function createData($request)
    {
        $type = LoanType::findOrFail($request->loan_type_id);

        $principal = $request->principal;
        $rate      = $type->interest_rate;
        $tenor     = $request->tenor;

        // =========================
        // HITUNG BUNGA
        // =========================
        if ($type->interest_type == 'flat') {
            $total_interest = ($principal * $rate / 100) * $tenor;
        } else {
            // simple approach (biar tidak ribet dulu)
            $total_interest = ($principal * $rate / 100) * ($tenor / 2);
        }

        $total_amount = $principal + $total_interest;
        $installment  = $total_amount / $tenor;

        $loan = self::create([
            'code'              => 'LN' . date('YmdHis'),
            'date'              => $request->date,
            'due_date'          => now()->parse($request->date)->addMonths($tenor),

            'loan_type_id'      => $type->id,
            'member_id'         => $request->member_id,
            'user_id'           => auth()->id(),

            // snapshot
            'principal'         => $principal,
            'interest_rate'     => $rate,
            'tenor'             => $tenor,

            'total_interest'    => $total_interest,
            'total_amount'      => $total_amount,
            'installment'       => $installment,

            'remaining_balance' => $total_amount,
            'status'            => 'ongoing',

            'account_id'        => $request->account_id,
        ]);

        // =========================
        // LEDGER
        // =========================
        Ledger::catatPencairanLoan($loan);

        return $loan;
    }

    // =========================
    // UPDATE DATA
    // =========================
    public static function updateData($id, $request)
    {
        $loan = self::findOrFail($id);
        $type = LoanType::findOrFail($request->loan_type_id);

        $principal = $request->principal;
        $rate      = $type->interest_rate;
        $tenor     = $request->tenor;

        if ($type->interest_type == 'flat') {
            $total_interest = ($principal * $rate / 100) * $tenor;
        } else {
            $total_interest = ($principal * $rate / 100) * ($tenor / 2);
        }

        $total_amount = $principal + $total_interest;
        $installment  = $total_amount / $tenor;

        $loan->update([
            'date'              => $request->date,
            'due_date'          => now()->parse($request->date)->addMonths($tenor),

            'loan_type_id'      => $type->id,
            'member_id'         => $request->member_id,
            'user_id'           => auth()->id(),

            'principal'         => $principal,
            'interest_rate'     => $rate,
            'tenor'             => $tenor,

            'total_interest'    => $total_interest,
            'total_amount'      => $total_amount,
            'installment'       => $installment,

            'remaining_balance' => $total_amount, // ⚠️ reset (hati-hati kalau sudah ada pembayaran)
        ]);

        // reset ledger
        Ledger::where('loan_id', $loan->id)->delete();
        Ledger::catatPencairanLoan($loan);

        return $loan;
    }

    // =========================
    // DELETE DATA
    // =========================
    public static function deleteData($loan)
    {
        Ledger::where('loan_id', $loan->id)->delete();
        $loan->delete();
    }
}
