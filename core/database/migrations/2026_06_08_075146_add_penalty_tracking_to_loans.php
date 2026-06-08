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
            $table->decimal('accrued_penalties', 28, 8)->default(0)->after('delay_charge')
                ->comment('Total penalties accrued by the daily cron');
            $table->decimal('penalties_paid', 28, 8)->default(0)->after('accrued_penalties')
                ->comment('Total penalties already deducted from user balance');
            $table->decimal('penalties_waived', 28, 8)->default(0)->after('penalties_paid')
                ->comment('Total penalties manually waived by admin');
            $table->timestamp('penalties_last_run_at')->nullable()->after('penalties_waived')
                ->comment('Last time the cron ran for this loan');
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
