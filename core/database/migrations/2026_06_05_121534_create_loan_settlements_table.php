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
        Schema::create('loan_settlements', function (Blueprint $table) {
            $table->id();
            $table->string('settlement_reference', 40)->unique();
            $table->string('certificate_reference', 40)->unique();
            $table->unsignedBigInteger('loan_id');
            $table->unsignedBigInteger('issued_by')->nullable();
            $table->decimal('original_loan_amount', 28, 8);
            $table->decimal('total_repaid', 28, 8);
            $table->timestamp('final_settlement_date')->nullable();
            $table->timestamp('closure_effective_date')->nullable();
            $table->tinyInteger('settlement_type')->default(1)->comment('1=full term, 2=early redemption');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('loan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_settlements');
    }
};
