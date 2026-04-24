<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ActivityLog;
use App\Models\Configuration;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public function index()
    {
        $configuration = Configuration::first();
        $accounts = Account::all();
        return view('configuration.index', compact('configuration', 'accounts'));
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

        $configuration = Configuration::first();

        $configuration->update([

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
        ActivityLog::log(auth()->user()->id, 'Memperbarui konfigurasi');

        return redirect()->route('configuration.index')->with('success', 'Konfigurasi berhasil diupdate');
    }
}
