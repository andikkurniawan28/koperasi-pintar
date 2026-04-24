<?php

namespace App\Models;

use App\Models\Configuration;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function account(){
        return $this->belongsTo(Account::class);
    }

    public static function catatPenjualan($sales, $request, $totalHpp)
    {
        $config = Configuration::first();

        // Tentukan akun pendapatan
        if($request->type == "member"){
            $revenueAccount = $config->sales_revenue_member_account_id;
        } else {
            $revenueAccount = $config->sales_revenue_customer_account_id;
        }

        // =========================
        // 1. KAS / BANK (DEBIT)
        // =========================
        self::insert([
            'date' => $sales->date,
            'user_id' => $sales->user_id,
            'account_id' => $sales->account_id,
            'description' => 'Penjualan '.$sales->code,
            'debit' => $sales->grand_total,
            'credit' => 0,
            'sales_id' => $sales->id,
        ]);

        // =========================
        // 2. DISKON (DEBIT - Contra Revenue)
        // =========================
        if ($sales->discount > 0) {
            self::insert([
                'date' => $sales->date,
                'user_id' => $sales->user_id,
                'account_id' => $config->sales_discount_account_id,
                'description' => 'Diskon Penjualan '.$sales->code,
                'debit' => $sales->discount,
                'credit' => 0,
                'sales_id' => $sales->id,
            ]);
        }

        // =========================
        // 3. PENDAPATAN (CREDIT - SUBTOTAL)
        // =========================
        self::insert([
            'date' => $sales->date,
            'user_id' => $sales->user_id,
            'account_id' => $revenueAccount,
            'description' => 'Pendapatan '.$sales->code,
            'credit' => $sales->subtotal,
            'debit' => 0,
            'sales_id' => $sales->id,
        ]);

        // =========================
        // 4. PPN KELUARAN (CREDIT)
        // =========================
        if ($sales->taxes > 0) {
            self::insert([
                'date' => $sales->date,
                'user_id' => $sales->user_id,
                'account_id' => $config->sales_tax_account_id,
                'description' => 'PPN Keluaran '.$sales->code,
                'credit' => $sales->taxes,
                'debit' => 0,
                'sales_id' => $sales->id,
            ]);
        }

        // =========================
        // 5. BIAYA TAMBAHAN (opsional)
        // =========================
        if ($sales->expenses > 0) {
            self::insert([
                'date' => $sales->date,
                'user_id' => $sales->user_id,
                'account_id' => $config->sales_expense_account_id,
                'description' => 'Biaya Tambahan '.$sales->code,
                'credit' => $sales->expenses, // diasumsikan dibebankan ke customer
                'debit' => 0,
                'sales_id' => $sales->id,
            ]);
        }

        // =========================
        // 6. HPP
        // =========================
        if ($totalHpp > 0) {

            // Debit HPP
            self::insert([
                'date' => $sales->date,
                'user_id' => $sales->user_id,
                'account_id' => $config->hpp_account_id,
                'description' => 'HPP '.$sales->code,
                'debit' => $totalHpp,
                'credit' => 0,
                'sales_id' => $sales->id,
            ]);

            // Credit Persediaan
            self::insert([
                'date' => $sales->date,
                'user_id' => $sales->user_id,
                'account_id' => $config->inventory_account_id,
                'description' => 'Persediaan keluar '.$sales->code,
                'credit' => $totalHpp,
                'debit' => 0,
                'sales_id' => $sales->id,
            ]);
        }
    }

    public static function catatPembelian($purchase)
    {
        $config = Configuration::first();

        // =========================
        // 1. PERSEDIAAN (DEBIT)
        // =========================
        self::insert([
            'date' => $purchase->date,
            'user_id' => $purchase->user_id,
            'account_id' => $config->inventory_account_id,
            'description' => 'Pembelian '.$purchase->code,
            'debit' => $purchase->subtotal,
            'credit' => 0,
            'purchase_id' => $purchase->id,
        ]);

        // =========================
        // 2. DISKON PEMBELIAN (CREDIT)
        // =========================
        if ($purchase->discount > 0) {
            self::insert([
                'date' => $purchase->date,
                'user_id' => $purchase->user_id,
                'account_id' => $config->purchase_discount_account_id,
                'description' => 'Diskon Pembelian '.$purchase->code,
                'credit' => $purchase->discount,
                'debit' => 0,
                'purchase_id' => $purchase->id,
            ]);
        }

        // =========================
        // 3. PPN MASUKAN (DEBIT)
        // =========================
        if ($purchase->taxes > 0) {
            self::insert([
                'date' => $purchase->date,
                'user_id' => $purchase->user_id,
                'account_id' => $config->purchase_tax_account_id,
                'description' => 'PPN Masukan '.$purchase->code,
                'debit' => $purchase->taxes,
                'credit' => 0,
                'purchase_id' => $purchase->id,
            ]);
        }

        // =========================
        // 4. BIAYA PEMBELIAN (DEBIT)
        // =========================
        if ($purchase->expenses > 0) {
            self::insert([
                'date' => $purchase->date,
                'user_id' => $purchase->user_id,
                'account_id' => $config->purchase_expense_account_id,
                'description' => 'Biaya Pembelian '.$purchase->code,
                'debit' => $purchase->expenses,
                'credit' => 0,
                'purchase_id' => $purchase->id,
            ]);
        }

        // =========================
        // 5. KAS / BANK (CREDIT)
        // =========================
        self::insert([
            'date' => $purchase->date,
            'user_id' => $purchase->user_id,
            'account_id' => $purchase->account_id,
            'description' => 'Pembayaran '.$purchase->code,
            'credit' => $purchase->grand_total,
            'debit' => 0,
            'purchase_id' => $purchase->id,
        ]);
    }

    public static function catatAdjustment($data)
    {
        $config = Configuration::first();

        $product = Product::find($data->product_id);
        $value = $data->total;

        if ($data->inOut == 'in') {

            // Persediaan naik
            self::insert([
                'stock_adjustment_id' => $data->id,
                'date' => $data->date,
                'user_id' => $data->user_id,
                'account_id' => $config->inventory_account_id,
                'description' => 'Stok Opname Masuk '.$data->code,
                'debit' => $value,
                'credit' => 0,
            ]);

            // lawan pendapatan lain
            self::insert([
                'stock_adjustment_id' => $data->id,
                'date' => $data->date,
                'user_id' => $data->user_id,
                'account_id' => $config->stock_adjustment_gain_account_id,
                'description' => 'Stok Opname Masuk '.$data->code,
                'credit' => $value,
                'debit' => 0,
            ]);

        } else {

            // Persediaan turun
            self::insert([
                'stock_adjustment_id' => $data->id,
                'date' => $data->date,
                'user_id' => $data->user_id,
                'account_id' => $config->inventory_account_id,
                'description' => 'Stok Opname Keluar '.$data->code,
                'credit' => $value,
                'debit' => 0,
            ]);

            // lawan beban
            self::insert([
                'stock_adjustment_id' => $data->id,
                'date' => $data->date,
                'user_id' => $data->user_id,
                'account_id' => $config->stock_adjustment_loss_account_id,
                'description' => 'Stok Opname Keluar '.$data->code,
                'debit' => $value,
                'credit' => 0,
            ]);
        }
    }
}
