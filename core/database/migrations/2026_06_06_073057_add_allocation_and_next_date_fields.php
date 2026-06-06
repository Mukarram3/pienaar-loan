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
        Schema::table('loan_plans', function (Blueprint $table) {
            $table->decimal('capital_ratio', 5, 4)->default(0.5000)->after('is_legacy')
                ->comment('Portion of each instalment allocated to capital (0.5 = 50%)');
            $table->decimal('profit_ratio', 5, 4)->default(0.5000)->after('capital_ratio')
                ->comment('Portion of each instalment allocated to profit (0.5 = 50%)');
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->date('next_installment_date')->nullable()->after('historical_days_late')
                ->comment('Override next instalment date — used for legacy migration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_plans', function (Blueprint $table) {
            $table->dropColumn(['capital_ratio', 'profit_ratio']);
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('next_installment_date');
        });
    }
};
