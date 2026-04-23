<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Ledger;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index()
    {
        return view('ledger.index', [
            'accounts' => Account::orderBy('code')->get()
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'account_id' => 'required'
        ]);

        $account = Account::findOrFail($request->account_id);

        $ledger = Ledger::where('account_id', $request->account_id)
            ->whereBetween('date', [$request->date_from, $request->date_to])
            ->orderBy('ledgers.date')
            ->orderBy('ledgers.id')
            ->select(
                'ledgers.*',
            )
            ->get();


        $balance = 0;

        foreach ($ledger as $row) {

            if ($account->normal_balance == 'Debit') {

                $balance += ($row->debit - $row->credit);

            } else {

                $balance += ($row->credit - $row->debit);

            }

            $row->balance = $balance;
        }

        return response()->json([
            'account' => $account,
            'data' => $ledger
        ]);
    }
}
