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
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('saving_type_id')->constrained();
            $table->foreignId('member_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('direction')->default('in');
            $table->double('total');
            // $table->string('code')->unique();
            // $table->date('date');
            // $table->date('withdraw_allowed_at')->nullable();
            // $table->foreignId('saving_type_id')->constrained();
            // $table->foreignId('member_id')->constrained();
            $table->foreignId('account_id')->constrained();
            // $table->foreignId('user_id')->constrained();
            // $table->double('total');
            // $table->double('witdrawn')->default(0);
            // $table->double('left')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings');
    }
};
