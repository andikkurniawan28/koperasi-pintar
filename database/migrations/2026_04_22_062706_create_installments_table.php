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
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->date('date');

            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained();

            $table->integer('installment_number'); // angsuran ke-

            // pembayaran
            $table->double('total');        // total dibayar
            $table->double('principal');    // bayar pokok
            $table->double('interest');     // bayar bunga
            $table->double('penalty')->default(0); // denda

            // setelah transaksi
            $table->double('remaining_balance'); // sisa hutang setelah bayar

            // kas / bank
            $table->foreignId('account_id')->constrained();

            // audit
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
        Schema::dropIfExists('installments');
    }
};
