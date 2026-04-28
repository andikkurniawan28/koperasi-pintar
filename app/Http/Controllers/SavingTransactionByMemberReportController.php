<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Saving;
use App\Models\SavingType;
use App\Models\Loan;
use App\Models\Installment;
use App\Models\Sales;

class SavingTransactionByMemberReportController extends Controller
{
    public function index()
    {
        return view('report.saving_transaction_by_member');
    }

    public function process(Request $request)
    {
        $date_from = $request->date_from;
        $date_to   = $request->date_to;

        // $date_from = "2026-04-01";
        // $date_to = "2026-04-30";

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
                $amount = $in - $out;
                $row[$s->name] = $amount;
            }

            $row['Belanja'] = Sales::where('member_id', $member->id)
                ->whereBetween('date', [$date_from, $date_to])
                ->sum('grand_total');

            $row['Pinjaman'] = Loan::where('member_id', $member->id)
                ->whereBetween('date', [$date_from, $date_to])
                ->sum('principal');

            $rows[] = $row;
        }

        return response()->json([
            'savingTypes' => $savingTypes,
            'data' => $rows
        ]);
    }
}
