<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Ledger;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Sales;
use App\Models\Saving;
use App\Models\SavingType;
use Illuminate\Http\Request;

class SavingTransactionByMemberReportController extends Controller
{
    public function index()
    {
        // return $this->process();
        return view('report.saving_transaction_by_member');
    }

    public function process(Request $request)
    {
        // ambil dari request + fallback
        $date_from = $request->date_from ?? date('Y-m-01');
        $date_to   = $request->date_to ?? date('Y-m-t');

        // kirim parameter ke laba rugi
        $labaRugi = $this->hitungLaba($date_from, $date_to);

        $savingTypes = SavingType::all();
        $members = Member::all();

        $rows = [];

        foreach ($members as $member) {

            $row = [
                'member' => $member->name,
            ];

            foreach ($savingTypes as $s) {

                $in = Saving::where('member_id', $member->id)
                    ->where('saving_type_id', $s->id)
                    ->where('direction', 'in')
                    ->whereBetween('date', [$date_from, $date_to])
                    ->sum('total');

                $out = Saving::where('member_id', $member->id)
                    ->where('saving_type_id', $s->id)
                    ->where('direction', 'out')
                    ->whereBetween('date', [$date_from, $date_to])
                    ->sum('total');

                $row[$s->name] = $in - $out;
            }

            $row['Belanja'] = Sales::where('member_id', $member->id)
                ->whereBetween('date', [$date_from, $date_to])
                ->sum('grand_total');

            $row['Jasa'] = Invoice::where('member_id', $member->id)
                ->whereBetween('date', [$date_from, $date_to])
                ->sum('grand_total');

            $row['Pinjaman'] = Loan::where('member_id', $member->id)
                ->whereBetween('date', [$date_from, $date_to])
                ->sum('principal');

            $rows[] = $row;
        }

        return response()->json([
            'savingTypes' => $savingTypes,
            'data' => $rows,
            'labaRugi' => $labaRugi,
        ]);
    }

    public function hitungLaba($date_from, $date_to)
    {
        $accounts = Account::whereIn('group', ['Pendapatan','HPP','Beban', 'Modal'])
            ->orderBy('code')
            ->get();

        foreach ($accounts as $account) {

            $total = Ledger::where('account_id', $account->id)
                ->whereBetween('date', [$date_from, $date_to])
                ->selectRaw('SUM(debit) as debit, SUM(credit) as credit')
                ->first();

            if ($account->normal_balance == 'Debit') {
                $balance = ($total->debit ?? 0) - ($total->credit ?? 0);
            } else {
                $balance = ($total->credit ?? 0) - ($total->debit ?? 0);
            }

            $account->balance = $balance;
        }

        $modal_detail       = $accounts->where('group','Modal')->values();
        $pendapatan_detail  = $accounts->where('group','Pendapatan')->values();
        $hpp_detail         = $accounts->where('group','HPP')->values();
        $beban_detail       = $accounts->where('group','Beban')->values();

        $modal      = $modal_detail->sum('balance');
        $pendapatan = $pendapatan_detail->sum('balance');
        $hpp        = $hpp_detail->sum('balance');
        $beban      = $beban_detail->sum('balance');

        return [
            'modal_detail'      => $modal_detail,
            'pendapatan_detail' => $pendapatan_detail,
            'hpp_detail'        => $hpp_detail,
            'beban_detail'      => $beban_detail,
            'modal'             => $modal,
            'pendapatan'        => $pendapatan,
            'hpp'               => $hpp,
            'beban'             => $beban,
            'laba_kotor'        => $pendapatan - $hpp,
            'laba_bersih'       => ($pendapatan - $hpp) - $beban,
        ];
    }
}
