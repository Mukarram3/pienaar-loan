<?php

// =============================================================
// File: database/migrations/2026_09_08_000002_add_calculation_type_to_loan_plans.php
// =============================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Explicit calculation discriminator for loan plans (spec s.24).
 *
 * Until now the only thing distinguishing a Fixed Capital Repayment plan from
 * an ordinary one was `is_legacy`, which actually means "this plan can be used
 * by the legacy import screen" — a different question. Overloading it would
 * have meant the calculation methodology changed whenever someone toggled an
 * import setting.
 *
 *   standard      Existing behaviour. Instalment = the stored per_installment.
 *                 Nothing changes for these plans.
 *   fixed_capital Per Instalment % is a CAPITAL rate. The instalment is
 *                 derived as capital / capital_ratio (spec s.3-s.5).
 *
 * BACKFILL: existing plans are seeded from is_legacy, so plans already
 * flagged legacy — which is where the Fixed Capital Repayment product lives —
 * pick up the corrected engine, and ordinary plans are untouched.
 *
 * This migration does NOT touch the `loans` table. No existing loan is
 * repriced (spec s.21). Only loans created AFTER this deploys use the new
 * engine.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_plans', function (Blueprint $table) {
            $table->string('calculation_type', 20)->default('standard')->after('is_legacy')
                ->comment('standard | fixed_capital — selects the instalment calculation engine');
        });

        // Seed from is_legacy so current behaviour is preserved for ordinary
        // plans and the Fixed Capital product is corrected.
        DB::table('loan_plans')->where('is_legacy', 1)->update([
            'calculation_type' => 'fixed_capital',
        ]);
    }

    public function down(): void
    {
        Schema::table('loan_plans', function (Blueprint $table) {
            $table->dropColumn('calculation_type');
        });
    }
};
