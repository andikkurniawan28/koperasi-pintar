<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Ledger;

class BalanceSheetController extends Controller
{
    public function index()
    {
        return view('report.balance_sheet');
    }

    public function process(Request $request)
    {
        $date_from = $request->date_from;
        $date_to   = $request->date_to;

        $accounts = Account::orderBy('code')->get();

        foreach($accounts as $account){

            $total = Ledger::where('account_id',$account->id)
                ->whereBetween('date', [$date_from, $date_to])
                ->selectRaw('SUM(debit) as debit, SUM(credit) as credit')
                ->first();

            if($account->normal_balance == 'Debit'){
                $balance = ($total->debit ?? 0) - ($total->credit ?? 0);
            }else{
                $balance = ($total->credit ?? 0) - ($total->debit ?? 0);
            }

            $account->balance = $balance;
        }

        // =========================
        // GROUPING
        // =========================
        $aset        = $accounts->where('group','Aset')->values();
        $kewajiban   = $accounts->where('group','Kewajiban')->values();
        $ekuitas     = $accounts->where('group','Modal')->values();
        $pendapatan  = $accounts->where('group','Pendapatan')->values();
        $hpp         = $accounts->where('group','HPP')->values(); // 🔥 tambahkan
        $beban       = $accounts->where('group','Beban')->values();

        // =========================
        // TOTAL
        // =========================
        $total_aset       = $aset->sum('balance');
        $total_kewajiban  = $kewajiban->sum('balance');
        $total_ekuitas    = $ekuitas->sum('balance');

        $total_pendapatan = $pendapatan->sum('balance');
        $total_hpp        = $hpp->sum('balance');
        $total_beban      = $beban->sum('balance');

        // =========================
        // LABA BERJALAN
        // =========================
        $laba = $total_pendapatan - $total_hpp - $total_beban;

        // =========================
        // TOTAL PASIVA
        // =========================
        $total_pasiva = $total_kewajiban + $total_ekuitas + $laba;

        return response()->json([
            'aset' => $aset,
            'kewajiban' => $kewajiban,
            'ekuitas' => $ekuitas,

            'pendapatan' => $pendapatan,
            'hpp' => $hpp,
            'beban' => $beban,

            'total_aset' => $total_aset,
            'total_kewajiban' => $total_kewajiban,
            'total_ekuitas' => $total_ekuitas,

            'total_pendapatan' => $total_pendapatan,
            'total_hpp' => $total_hpp,
            'total_beban' => $total_beban,

            'laba' => $laba,

            'total_pasiva' => $total_pasiva,
        ]);
    }
}
