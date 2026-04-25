<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Saving extends Model
{
    use HasFactory;

    protected $guarded = [];

    // =========================
    // BOOT (LOG)
    // =========================
    protected static function booted()
    {
        static::created(function ($saving) {
            ActivityLog::log(auth()->id(), "Menambah simpanan ".$saving->code);
        });

        static::updated(function ($saving) {
            ActivityLog::log(auth()->id(), "Mengubah simpanan ".$saving->code);
        });

        static::deleted(function ($saving) {
            ActivityLog::log(auth()->id(), "Menghapus simpanan ".$saving->code);
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
        $code = 'SVG' . date('YmdHis');

        $saving = self::create([
            'code'                => $code,
            'date'                => $request->date,
            'withdraw_allowed_at' => $request->withdraw_allowed_at,
            'saving_type_id'      => $request->saving_type_id,
            'member_id'           => $request->member_id,
            'account_id'          => $request->account_id,
            'total'               => $request->total,
            'user_id'             => auth()->user()->id,
        ]);

        // =========================
        // LEDGER (WAJIB DI KOPERASI)
        // =========================
        Ledger::catatSimpananMasuk($saving);

        return $saving;
    }

    // =========================
    // UPDATE DATA
    // =========================
    public static function updateData($id, $request)
    {
        $saving = self::findOrFail($id);

        $saving->update([
            'date'                => $request->date,
            'withdraw_allowed_at' => $request->withdraw_allowed_at,
            'saving_type_id'      => $request->saving_type_id,
            'member_id'           => $request->member_id,
            'account_id'          => $request->account_id,
            'user_id'             => auth()->user()->id,
            'total'               => $request->total,
        ]);

        // reset ledger
        Ledger::where('saving_id', $saving->id)->delete();
        Ledger::catatSimpananMasuk($saving);

        return $saving;
    }

    // =========================
    // DELETE DATA
    // =========================
    public static function deleteData($saving)
    {
        Ledger::where('saving_id', $saving->id)->delete();
        $saving->delete();
    }
}
