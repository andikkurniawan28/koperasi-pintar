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

    public static function catatTagihan($invoice)
    {
        $config = Configuration::first();

        // Tentukan akun pendapatan
        $revenueAccount = $invoice->type == "member"
            ? $config->service_revenue_member_account_id
            : $config->service_revenue_customer_account_id;

        // =========================
        // 1. PIUTANG USAHA (DEBIT)
        // =========================
        self::insert([
            'date' => $invoice->date,
            'user_id' => $invoice->user_id,
            'account_id' => $config->account_receivable_account_id,
            'description' => 'Tagihan ' . $invoice->code,
            'debit' => $invoice->grand_total,
            'credit' => 0,
            'invoice_id' => $invoice->id,
        ]);

        // =========================
        // 2. DISKON
        // =========================
        if ($invoice->discount > 0) {
            self::insert([
                'date' => $invoice->date,
                'user_id' => $invoice->user_id,
                'account_id' => $config->sales_discount_account_id,
                'description' => 'Diskon ' . $invoice->code,
                'debit' => $invoice->discount,
                'credit' => 0,
                'invoice_id' => $invoice->id,
            ]);
        }

        // =========================
        // 3. PENDAPATAN
        // =========================
        self::insert([
            'date' => $invoice->date,
            'user_id' => $invoice->user_id,
            'account_id' => $revenueAccount,
            'description' => 'Pendapatan ' . $invoice->code,
            'credit' => $invoice->subtotal,
            'debit' => 0,
            'invoice_id' => $invoice->id,
        ]);

        // =========================
        // 4. PPN
        // =========================
        if ($invoice->taxes > 0) {
            self::insert([
                'date' => $invoice->date,
                'user_id' => $invoice->user_id,
                'account_id' => $config->sales_tax_account_id,
                'description' => 'PPN ' . $invoice->code,
                'credit' => $invoice->taxes,
                'debit' => 0,
                'invoice_id' => $invoice->id,
            ]);
        }

        // =========================
        // 5. BIAYA TAMBAHAN
        // =========================
        if ($invoice->expenses > 0) {
            self::insert([
                'date' => $invoice->date,
                'user_id' => $invoice->user_id,
                'account_id' => $config->sales_expense_account_id,
                'description' => 'Biaya ' . $invoice->code,
                'credit' => $invoice->expenses,
                'debit' => 0,
                'invoice_id' => $invoice->id,
            ]);
        }
    }

    public static function catatPembayaran($payment)
    {
        $config = Configuration::first();

        // =========================
        // 1. KAS / BANK (DEBIT)
        // =========================
        self::insert([
            'date' => $payment->date,
            'user_id' => $payment->user_id,
            'account_id' => $payment->account_id,
            'description' => 'Pembayaran ' . $payment->invoice->code,
            'debit' => $payment->total,
            'credit' => 0,
            'payment_id' => $payment->id,
        ]);

        // =========================
        // 2. PIUTANG USAHA (CREDIT)
        // =========================
        self::insert([
            'date' => $payment->date,
            'user_id' => $payment->user_id,
            'account_id' => $config->account_receivable_account_id,
            'description' => 'Pelunasan ' . $payment->invoice->code,
            'credit' => $payment->total,
            'debit' => 0,
            'payment_id' => $payment->id,
        ]);
    }

    public static function catatSimpananMasuk($saving)
    {
        $config = Configuration::first();

        // =========================
        // 1. KAS / BANK (DEBIT)
        // =========================
        self::insert([
            'date'        => $saving->date,
            'user_id'     => auth()->id(),
            'account_id'  => $saving->account_id, // akun kewajiban simpanan
            'description' => "Setoran Simpanan {$saving->savingType->name} oleh {$saving->member->name}",
            'debit'       => $saving->total,
            'credit'      => 0,
            'saving_id'   => $saving->id,
        ]);

        // =========================
        // 2. SIMPANAN (KREDIT)
        // =========================
        self::insert([
            'date'        => $saving->date,
            'user_id'     => auth()->id(),
            'account_id'  => $saving->savingType->account_id, // akun k
            'description' => "Setoran Simpanan {$saving->savingType->name} oleh {$saving->member->name}",
            'debit'       => 0,
            'credit'      => $saving->total,
            'saving_id'   => $saving->id,
        ]);
    }

    public static function catatSimpananKeluar($withdraw)
    {
        $config = Configuration::first();

        // =========================
        // 1. KAS / BANK (DEBIT)
        // =========================
        self::insert([
            'date'        => $withdraw->date,
            'user_id'     => auth()->id(),
            'account_id'  => $withdraw->account_id, // akun kewajiban simpanan
            'description' => "Penarikan Simpanan {$withdraw->savingType->name} oleh {$withdraw->member->name}",
            'credit'       => $withdraw->total,
            'debit'      => 0,
            'saving_id'   => $withdraw->id,
        ]);

        // =========================
        // 2. SIMPANAN (KREDIT)
        // =========================
        self::insert([
            'date'        => $withdraw->date,
            'user_id'     => auth()->id(),
            'account_id'  => $withdraw->savingType->account_id, // akun kas/bank dari type
            'description' => "Penarikan Simpanan {$withdraw->savingType->name} oleh {$withdraw->member->name}",
            'credit'       => 0,
            'debit'      => $withdraw->total,
            'saving_id'   => $withdraw->id,
        ]);
    }

    public static function catatPencairanLoan($loan)
    {
        // Ambil akun dari loan type (piutang)
        $receivableAccount = $loan->loanType->account_id;

        // Ambil akun kas/bank (kamu harus kirim dari form / loan)
        $cashAccount = $loan->account_id ?? null;

        if (!$cashAccount) {
            throw new \Exception("Akun kas/bank belum diset untuk pencairan.");
        }

        // Debit: Piutang
        self::create([
            'date'        => $loan->date,
            'description' => 'Pencairan Pinjaman ' . $loan->code,
            'debit'       => $loan->principal,
            'credit'      => 0,
            'account_id'  => $receivableAccount,
            'loan_id'     => $loan->id,
            'user_id'     => auth()->id(),
        ]);

        // Credit: Kas / Bank
        self::create([
            'date'        => $loan->date,
            'description' => 'Pencairan Pinjaman ' . $loan->code,
            'debit'       => 0,
            'credit'      => $loan->principal,
            'account_id'  => $cashAccount,
            'loan_id'     => $loan->id,
            'user_id'     => auth()->id(),
        ]);
    }

    public static function catatPembayaranLoan($loan, $payment)
    {
        $config = Configuration::first();

        $receivableAccount = $loan->loanType->account_id;
        $cashAccount       = $payment->account_id;

        $interestAccount = $config->interest_income_account_id;
        $penaltyAccount  = $config->interest_income_account_id; // 🔥 jangan pakai interest lagi

        // =========================
        // Debit: Kas / Bank
        // =========================
        self::create([
            'date'        => $payment->date,
            'description' => 'Pembayaran Pinjaman ' . $loan->code,
            'debit'       => $payment->total,
            'credit'      => 0,
            'account_id'  => $cashAccount,
            'installment_id' => $payment->id, // 🔥 fix typo
            'user_id'     => auth()->id(),
        ]);

        // =========================
        // Credit: Piutang (Pokok)
        // =========================
        if ($payment->principal > 0) {
            self::create([
                'date'        => $payment->date,
                'description' => 'Bayar Pokok ' . $loan->code,
                'debit'       => 0,
                'credit'      => $payment->principal,
                'account_id'  => $receivableAccount,
                'installment_id' => $payment->id,
                'user_id'     => auth()->id(),
            ]);
        }

        // =========================
        // Credit: Pendapatan Bunga
        // =========================
        if ($payment->interest > 0) {
            self::create([
                'date'        => $payment->date,
                'description' => 'Bayar Bunga ' . $loan->code,
                'debit'       => 0,
                'credit'      => $payment->interest,
                'account_id'  => $interestAccount,
                'installment_id' => $payment->id,
                'user_id'     => auth()->id(),
            ]);
        }

        // =========================
        // Credit: Denda
        // =========================
        if ($payment->penalty > 0) {
            self::create([
                'date'        => $payment->date,
                'description' => 'Bayar Denda ' . $loan->code,
                'debit'       => 0,
                'credit'      => $payment->penalty,
                'account_id'  => $penaltyAccount,
                'installment_id' => $payment->id,
                'user_id'     => auth()->id(),
            ]);
        }
    }
}
