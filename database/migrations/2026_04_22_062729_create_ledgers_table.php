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
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->text('description');
            $table->double('debit');
            $table->double('credit');
            $table->foreignId('account_id')->constrained();
            $table->foreignId('sales_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('purchase_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('saving_id')->nullable()->constrained()->onDelete('cascade');
            // $table->foreignId('withdraw_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('loan_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('installment_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('stock_adjustment_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
