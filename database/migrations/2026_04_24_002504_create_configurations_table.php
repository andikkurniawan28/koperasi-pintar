<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();

            // Pendapatan Toko
            $table->foreignId('sales_revenue_member_account_id')->constrained('accounts');
            $table->foreignId('sales_revenue_customer_account_id')->constrained('accounts');

            // Item Penjualan
            $table->foreignId('sales_discount_account_id')->constrained('accounts');
            $table->foreignId('sales_expense_account_id')->constrained('accounts');
            $table->foreignId('sales_tax_account_id')->constrained('accounts');

            // Item Pembelian
            $table->foreignId('purchase_discount_account_id')->constrained('accounts');
            $table->foreignId('purchase_expense_account_id')->constrained('accounts');
            $table->foreignId('purchase_tax_account_id')->constrained('accounts');

            // HPP & Persediaan
            $table->foreignId('hpp_account_id')->constrained('accounts');
            $table->foreignId('inventory_account_id')->constrained('accounts');

            // Stok Opname
            $table->foreignId('stock_adjustment_gain_account_id')->constrained('accounts');
            $table->foreignId('stock_adjustment_loss_account_id')->constrained('accounts');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
