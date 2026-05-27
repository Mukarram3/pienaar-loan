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
        Schema::create('redemption_quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_reference', 40)->unique();
            $table->unsignedBigInteger('loan_id');
            $table->unsignedBigInteger('generated_by')->nullable(); // admin_id
            $table->decimal('loan_amount', 28, 8);
            $table->decimal('amount_paid', 28, 8);
            $table->decimal('outstanding_balance', 28, 8);
            $table->decimal('calc_a_value', 28, 8); // Outstanding * 0.90
            $table->decimal('calc_b_value', 28, 8); // (Loan*1.5) - Paid
            $table->decimal('settlement_amount', 28, 8);
            $table->decimal('discount_applied', 28, 8)->default(0);
            $table->timestamp('expires_at');
            $table->tinyInteger('status')->default(1)->comment('1=active, 2=expired, 3=settled, 4=void');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('loan_id');
            $table->index('quote_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redemption_quotes');
    }
};
