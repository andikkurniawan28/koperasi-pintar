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
        Schema::create('loan_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->double('interest_rate')->default(0); // bunga (%)
            $table->enum('interest_type', ['flat', 'effective'])->default('flat');
            $table->integer('tenor_min')->nullable(); // minimal bulan
            $table->integer('tenor_max')->nullable(); // maksimal bulan
            $table->double('max_amount')->nullable(); // plafon pinjaman
            $table->boolean('requires_collateral')->default(false); // pakai jaminan atau tidak
            $table->foreignId('account_id')->constrained(); // akun piutang pinjaman
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_types');
    }
};
