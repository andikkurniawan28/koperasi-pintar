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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();

            $table->date('date'); // pencairan
            $table->date('due_date');

            $table->foreignId('loan_type_id')->constrained();
            $table->foreignId('account_id')->constrained();
            $table->foreignId('member_id')->constrained();
            $table->foreignId('user_id')->constrained();

            $table->double('principal');
            $table->double('interest_rate');
            $table->integer('tenor');

            $table->double('total_interest');
            $table->double('total_amount');
            $table->double('installment');

            $table->double('remaining_balance');

            $table->enum('status', ['ongoing', 'paid_off', 'default'])
                ->default('ongoing');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
