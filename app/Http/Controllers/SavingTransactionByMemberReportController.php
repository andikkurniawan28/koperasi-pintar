<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Saving;
use App\Models\SavingType;
use App\Models\Loan;
use App\Models\Installment;
use App\Models\Sale;

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

        // =========================
        // AMBIL TIPE SIMPANAN
        // =========================
        $savingTypes = SavingType::pluck('name', 'id'); // [id => name]

        // =========================
        // AMBIL MEMBER
        // =========================
        $members = Member::all();

        $rows = [];

        foreach ($members as $member) {

            $row = [
                'member' => $member->name,
            ];

            // =========================
            // SIMPANAN PER TIPE (PIVOT)
            // =========================
            foreach ($savingTypes as $typeId => $typeName) {

                $amount = Saving::where('member_id', $member->id)
                    ->where('saving_type_id', $typeId)
                    ->where('direction', 'in')
                    ->whereBetween('date', [$date_from, $date_to])
                    ->sum('total');

                $row['saving_'.$typeId] = $amount;
            }

            // =========================
            // PENARIKAN
            // =========================
            $row['saving_out'] = Saving::where('member_id', $member->id)
                ->where('direction', 'out')
                ->whereBetween('date', [$date_from, $date_to])
                ->sum('total');

            // =========================
            // TOTAL SIMPANAN (ALL TIME)
            // =========================
            $row['saving_balance'] = Saving::where('member_id', $member->id)
                ->selectRaw("
                    SUM(CASE WHEN direction = 'in' THEN total ELSE 0 END) -
                    SUM(CASE WHEN direction = 'out' THEN total ELSE 0 END)
                as balance")
                ->value('balance') ?? 0;

            // =========================
            // PINJAMAN
            // =========================
            $row['loan'] = Loan::where('member_id', $member->id)
                ->whereBetween('date', [$date_from, $date_to])
                ->sum('principal');

            $row['loan_remaining'] = Loan::where('member_id', $member->id)
                ->sum('remaining_balance');

            // =========================
            // ANGSURAN
            // =========================
            $row['installment'] = Installment::where('member_id', $member->id)
                ->whereBetween('date', [$date_from, $date_to])
                ->sum('total');

            // =========================
            // SALES (opsional koperasi toko)
            // =========================
            $row['sales'] = Sale::where('member_id', $member->id)
                ->whereBetween('date', [$date_from, $date_to])
                ->sum('grand_total');

            $rows[] = $row;
        }

        return response()->json([
            'columns' => $savingTypes,
            'data' => $rows
        ]);
    }
}
