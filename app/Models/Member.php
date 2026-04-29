<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($member) {
            ActivityLog::log(auth()->user()->id, "Membuat anggota ". $member->name);
        });
        static::updated(function ($member) {
            ActivityLog::log(auth()->user()->id, "Mengedit anggota ". $member->name);
        });
        static::deleted(function ($member) {
            ActivityLog::log(auth()->user()->id, "Menghapus anggota ". $member->name);
        });
    }

    public function savings()
    {
        return $this->hasMany(Saving::class);
    }

    public static function salesRevenue($member_id){
        $account_id = AutoJournal::first()->sales_revenue_member_account_id;
        $account = Account::findOrFail($account_id);
        $debit = Ledger::where('account_id', $account_id)->where('member_id', $member_id)->sum('debit');
        $credit = Ledger::where('account_id', $account_id)->where('member_id', $member_id)->sum('credit');

        if($account->normal_balance == 'debit'){
            $amount = $debit - $credit;
        } else {
            $amount = $credit - $debit;
        }

        return $amount;
    }

    public static function serviceRevenue($member_id){
        $account_id = AutoJournal::first()->service_revenue_member_account_id;
        $account = Account::findOrFail($account_id);
        $debit = Ledger::where('account_id', $account_id)->where('member_id', $member_id)->sum('debit');
        $credit = Ledger::where('account_id', $account_id)->where('member_id', $member_id)->sum('credit');

        if($account->normal_balance == 'debit'){
            $amount = $debit - $credit;
        } else {
            $amount = $credit - $debit;
        }

        return $amount;
    }
}
