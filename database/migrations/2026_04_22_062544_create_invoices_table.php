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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('code')->unique();
            $table->date('date');
            $table->date('due_date');
            $table->foreignId('member_id')->nullable()->constrained();
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->foreignId('user_id')->constrained();
            $table->double('subtotal');
            $table->double('discount');
            $table->double('expenses');
            $table->double('taxes');
            $table->double('grand_total');
            $table->double('paid');
            $table->double('left');
            $table->foreignId('account_id')->constrained();
            $table->string('boolean')->default('Belum Bayar'); // Sudah DP, Lunas
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
