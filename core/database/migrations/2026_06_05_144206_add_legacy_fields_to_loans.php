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
        Schema::table('loans', function (Blueprint $table) {
            $table->boolean('is_legacy')->default(false)->after('plan_id')
                ->comment('1=imported legacy loan');
            $table->date('original_loan_date')->nullable()->after('is_legacy');
            $table->string('original_agreement_ref', 80)->nullable()->after('original_loan_date');
            $table->decimal('total_repayable_override', 28, 8)->nullable()->after('original_agreement_ref')
                ->comment('For legacy: overrides per_installment * total_installment');
            $table->decimal('historical_late_fees', 28, 8)->default(0)->after('total_repayable_override')
                ->comment('Legacy late fees imported at migration');
            $table->decimal('other_charges', 28, 8)->default(0)->after('historical_late_fees')
                ->comment('Legal fees, other charges');
            $table->integer('historical_missed_count')->default(0)->after('other_charges');
            $table->integer('historical_days_late')->default(0)->after('historical_missed_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            //
        });
    }
};
