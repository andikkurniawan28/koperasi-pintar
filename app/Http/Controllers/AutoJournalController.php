<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ActivityLog;
use App\Models\AutoJournal;
use Illuminate\Http\Request;

class AutoJournalController extends Controller
{
    public function index()
    {
        $auto_journal = AutoJournal::first();
        $accounts = Account::all();
        return view('auto_journal.index', compact('auto_journal', 'accounts'));
    }

    public function process(Request $request)
    {
        $request->validate([
            // PENJUALAN
            'sales_revenue_member_account_id' => 'required|exists:accounts,id',
            'sales_revenue_customer_account_id' => 'required|exists:accounts,id',
            'sales_discount_account_id' => 'required|exists:accounts,id',
            'sales_expense_account_id' => 'required|exists:accounts,id',
            'sales_tax_account_id' => 'required|exists:accounts,id',

            // PEMBELIAN
            'purchase_discount_account_id' => 'required|exists:accounts,id',
            'purchase_expense_account_id' => 'required|exists:accounts,id',
            'purchase_tax_account_id' => 'required|exists:accounts,id',

            // PERSEDIAAN
            'hpp_account_id' => 'required|exists:accounts,id',
            'inventory_account_id' => 'required|exists:accounts,id',

            // STOCK OPNAME
            'stock_adjustment_gain_account_id' => 'required|exists:accounts,id',
            'stock_adjustment_loss_account_id' => 'required|exists:accounts,id',
        ]);

        $auto_journal = AutoJournal::first();

        $auto_journal->update([

            // =========================
            // PENJUALAN
            // =========================
            'sales_revenue_member_account_id' => $request->sales_revenue_member_account_id,
            'sales_revenue_customer_account_id' => $request->sales_revenue_customer_account_id,
            'sales_discount_account_id' => $request->sales_discount_account_id,
            'sales_expense_account_id' => $request->sales_expense_account_id,
            'sales_tax_account_id' => $request->sales_tax_account_id,

            // =========================
            // PEMBELIAN
            // =========================
            'purchase_discount_account_id' => $request->purchase_discount_account_id,
            'purchase_expense_account_id' => $request->purchase_expense_account_id,
            'purchase_tax_account_id' => $request->purchase_tax_account_id,

            // =========================
            // PERSEDIAAN
            // =========================
            'hpp_account_id' => $request->hpp_account_id,
            'inventory_account_id' => $request->inventory_account_id,

            // =========================
            // STOCK OPNAME
            // =========================
            'stock_adjustment_gain_account_id' => $request->stock_adjustment_gain_account_id,
            'stock_adjustment_loss_account_id' => $request->stock_adjustment_loss_account_id,

        ]);
        ActivityLog::log(auth()->user()->id, 'Memperbarui Konfigurasi Auto Jurnal');

        return redirect()->route('auto_journal.index')->with('success', 'Konfigurasi Auto Jurnal berhasil diupdate');
    }
}
