<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sales;
use App\Models\Purchase;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\Installment;
use App\Models\Invoice;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // =========================
        // RANGE BULAN INI
        // =========================
        $start = $now->copy()->startOfMonth();
        $end   = $now->copy()->endOfMonth();

        // =========================
        // KAS MASUK & KELUAR
        // =========================
        $cashIn = Payment::whereBetween('date', [$start, $end])->sum('total')
            + Saving::where('direction', 'in')->whereBetween('date', [$start, $end])->sum('total')
            + Installment::whereBetween('date', [$start, $end])->sum('total');

        $cashOut = Purchase::whereBetween('date', [$start, $end])->sum('grand_total')
            + Saving::where('direction', 'out')->whereBetween('date', [$start, $end])->sum('total');

        // =========================
        // USAHA TOKO
        // =========================
        $sales = Sales::whereBetween('date', [$start, $end])->sum('grand_total');
        $purchases = Purchase::whereBetween('date', [$start, $end])->sum('grand_total');

        // =========================
        // SIMPANAN
        // =========================
        $totalSavings = Saving::where('direction', 'in')->sum('total')
            - Saving::where('direction', 'out')->sum('total');

        $monthlySavings = Saving::where('direction', 'in')
            ->whereBetween('date', [$start, $end])
            ->sum('total');

        // =========================
        // PINJAMAN
        // =========================
        $loanDisbursed = Loan::whereBetween('date', [$start, $end])->sum('principal');

        $outstandingLoan = Loan::sum('remaining_balance');

        $badLoan = Loan::where('status', 'default')->sum('remaining_balance');

        // =========================
        // ANGSURAN
        // =========================
        $installments = Installment::whereBetween('date', [$start, $end])->sum('total');

        // =========================
        // PIUTANG (INVOICE)
        // =========================
        $receivables = Invoice::sum('left');

        $overdue = Invoice::where('due_date', '<', now())
            ->where('left', '>', 0)
            ->sum('left');

        // =========================
        // CHART BULANAN (12 BULAN)
        // =========================
        $chart = [];
        for ($i = 1; $i <= 12; $i++) {
            $chart[] = [
                'month' => $i,
                'sales' => Sales::whereMonth('date', $i)->whereYear('date', $now->year)->sum('grand_total'),
                'loan'  => Loan::whereMonth('date', $i)->whereYear('date', $now->year)->sum('principal'),
                'saving' => Saving::where('direction', 'in')
                    ->whereMonth('date', $i)
                    ->whereYear('date', $now->year)
                    ->sum('total'),
            ];
        }

        return response()->json([
            'summary' => [
                'cash_in' => $cashIn,
                'cash_out' => $cashOut,
                'net_cash' => $cashIn - $cashOut,

                'sales' => $sales,
                'purchases' => $purchases,

                'total_savings' => $totalSavings,
                'monthly_savings' => $monthlySavings,

                'loan_disbursed' => $loanDisbursed,
                'outstanding_loan' => $outstandingLoan,
                'bad_loan' => $badLoan,

                'installments' => $installments,

                'receivables' => $receivables,
                'overdue' => $overdue,
            ],

            'chart' => $chart
        ]);
    }
}
