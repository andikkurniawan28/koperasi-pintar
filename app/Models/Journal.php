<?php

namespace App\Models;

use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($journal) {
            ActivityLog::log(auth()->user()->id, "Membuat jurnal umum ". $journal->code);
        });
        static::updated(function ($journal) {
            ActivityLog::log(auth()->user()->id, "Mengedit jurnal umum ". $journal->code);
        });
        static::deleted(function ($journal) {
            ActivityLog::log(auth()->user()->id, "Menghapus jurnal umum ". $journal->code);
        });
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function entry(){
        return $this->hasMany(JournalEntry::class);
    }

    public static function catatJurnal($request)
    {
        $code = 'JRN' . date('YmdHis');

        $journal = self::create([
            'code' => $code,
            'date' => $request->date,
            'user_id' => auth()->id(),
            'description' => auth()->id(),
        ]);

        foreach ($request->items as $item) {

            JournalEntry::create([
                'journal_id' => $journal->id,
                'account_id' => $item['account_id'],
                'debit' => $item['debit'],
                'credit' => $item['credit'],
            ]);

            Ledger::insert([
                'journal_id'    => $journal->id,
                'account_id'    => $item['account_id'],
                'date'          => $journal->date,
                'user_id'       => $journal->user_id,
                'description'   => "Jurnal Umum {$journal->code} - {$journal->description}",
                'debit'         => $item['debit'],
                'credit'        => $item['credit'],
            ]);
        }

        return $journal;
    }
}
