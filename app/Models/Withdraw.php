<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Withdraw extends Model
{
    use HasFactory;

    protected $table = "savings";

    protected $guarded = [];

    public static function phrase($withdraw)
    {
        if($withdraw->direction == "in"){
            return "Menerima penyetoran";
        }
        else {
            return "Melayani penarikan";
        }
    }

    // =========================
    // BOOT (LOG)
    // =========================
    protected static function booted()
    {
        static::created(function ($withdraw) {
            $phrase = self::phrase($withdraw);
            ActivityLog::log(auth()->id(), "{$phrase} simpanan ".$withdraw->code);
        });

        static::updated(function ($withdraw) {
            ActivityLog::log(auth()->id(), "Mengubah penarikan simpanan ".$withdraw->code);
        });

        static::deleted(function ($withdraw) {
            ActivityLog::log(auth()->id(), "Menghapus penarikan simpanan ".$withdraw->code);
        });
    }

    // =========================
    // RELATION
    // =========================
    public function savingType()
    {
        return $this->belongsTo(SavingType::class);
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
        $code = 'WD' . date('YmdHis');

        $withdraw = self::create([
            'code'                => $code,
            'date'                => $request->date,
            'direction'           => 'out',
            'saving_type_id'      => $request->saving_type_id,
            'member_id'           => $request->member_id,
            'account_id'          => $request->account_id,
            'total'               => $request->total,
            'user_id'             => auth()->user()->id,
        ]);

        // =========================
        // LEDGER (WAJIB DI KOPERASI)
        // =========================
        Ledger::catatSimpananKeluar($withdraw);

        return $withdraw;
    }

    // =========================
    // UPDATE DATA
    // =========================
    public static function updateData($id, $request)
    {
        $withdraw = self::findOrFail($id);

        $withdraw->update([
            'date'                => $request->date,
            'direction'           => 'out',
            'saving_type_id'      => $request->saving_type_id,
            'member_id'           => $request->member_id,
            'account_id'          => $request->account_id,
            'total'               => $request->total,
            'user_id'             => auth()->user()->id,
        ]);

        // reset ledger
        Ledger::where('saving_id', $withdraw->id)->delete();
        Ledger::catatSimpananKeluar($withdraw);

        return $withdraw;
    }

    // =========================
    // DELETE DATA
    // =========================
    public static function deleteData($withdraw)
    {
        Ledger::where('withdraw_id', $withdraw->id)->delete();
        $withdraw->delete();
    }
}
