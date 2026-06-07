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
        Schema::create('settlement_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->unsignedBigInteger('quote_id')->nullable();
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->decimal('expected_amount', 28, 8);
            $table->decimal('received_amount', 28, 8);
            $table->decimal('shortfall', 28, 8)->default(0);
            $table->date('payment_date');
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_reference', 100)->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=accepted_full, 2=accepted_short, 3=rejected');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('loan_id');
            $table->index('quote_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlement_payments');
    }
};
