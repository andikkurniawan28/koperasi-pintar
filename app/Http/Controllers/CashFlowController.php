<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use Illuminate\Http\Request;

class CashFlowController extends Controller
{
    public function index()
    {
        return view('report.cash_flow');
    }

    public function process(Request $request)
    {
        $date_from = $request->date_from;
        $date_to   = $request->date_to;

        $cash = Ledger::where('account_id','1')
            ->whereBetween('ledgers.date',[$date_from,$date_to])
            ->orderBy('ledgers.date')
            ->get();

        $balance = 0;

        foreach($cash as $row){

            $balance += ($row->debit - $row->credit);

            $row->balance = $balance;
        }

        return response()->json($cash);
    }
}
