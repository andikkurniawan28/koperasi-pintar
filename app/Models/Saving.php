<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Saving extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function phrase($saving)
    {
        if($saving->direction == "in"){
            return "Menyetor";
        }
        else {
            return "Menarik";
        }
    }

    // =========================
    // BOOT (LOG)
    // =========================
    protected static function booted()
    {
        static::created(function ($saving) {
            $phrase = self::phrase($saving);
            ActivityLog::log(auth()->id(), "{$phrase} simpanan ".$saving->id);
        });

        static::updated(function ($saving) {
            ActivityLog::log(auth()->id(), "Mengubah simpanan ".$saving->id);
        });

        static::deleted(function ($saving) {
            ActivityLog::log(auth()->id(), "Menghapus simpanan ".$saving->id);
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
        $id = 'SVG' . date('YmdHis');

        $saving = self::create([
            'date'                => $request->date,
            'direction'           => 'in',
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
            'direction'           => 'in',
            'saving_type_id'      => $request->saving_type_id,
            'member_id'           => $request->member_id,
            'account_id'          => $request->account_id,
            'total'               => $request->total,
            'user_id'             => auth()->user()->id,
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
