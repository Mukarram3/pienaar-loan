<?php

// =============================================================
// File: database/migrations/2026_09_08_000001_add_payment_allocation_ledger.php
// =============================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Payment allocation ledger.
 *
 * Implements the contractual clause:
 *   "Each payment received shall be allocated as follows: 50% shall be applied
 *    towards repayment of the outstanding Capital Sum, and the remaining 50%
 *    shall be applied towards payment of interest due under the Loan."
 *
 * Two structural points:
 *
 * 1. The ratio is SNAPSHOT ONTO THE LOAN, not read live from the plan.
 *    Previously Loan::capitalProfitAllocation() read $this->plan->capital_ratio
 *    at display time, which meant editing a plan silently re-split every
 *    historic loan that referenced it. Snapshotting freezes each loan's
 *    allocation at its contracted value (spec s.21).
 *
 * 2. Capital and profit are maintained as SEPARATE running balances, not
 *    derived pro-rata from a single combined figure (spec s.11).
 *
 * Late fees are tracked separately again and are never allocated to capital or
 * profit (spec s.16).
 *
 * This migration is additive. It creates one table and adds nullable/zero-
 * default columns. It does not modify or recalculate any existing row.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // Contracted allocation, frozen at loan creation.
            $table->decimal('capital_ratio', 5, 4)->nullable()->after('total_repayable_override')
                ->comment('Contracted capital share of each payment. NULL = fall back to plan.');
            $table->decimal('profit_ratio', 5, 4)->nullable()->after('capital_ratio')
                ->comment('Contracted profit share of each payment. NULL = fall back to plan.');

            // Running ledger balances.
            $table->decimal('capital_repaid', 18, 2)->default(0)->after('profit_ratio')
                ->comment('Cumulative capital received. Maintained by LoanPaymentAllocator.');
            $table->decimal('profit_received', 18, 2)->default(0)->after('capital_repaid')
                ->comment('Cumulative profit received. Maintained by LoanPaymentAllocator.');
            $table->decimal('late_fees_paid', 18, 2)->default(0)->after('profit_received')
                ->comment('Cumulative late charges received. Never allocated to capital or profit.');
            $table->decimal('unallocated_credit', 18, 2)->default(0)->after('late_fees_paid')
                ->comment('Overpayment held pending policy. Not allocated to capital or profit.');

            $table->boolean('allocation_ledger_active')->default(false)->after('unallocated_credit')
                ->comment('1 = this loan uses the allocation ledger. Off for pre-existing loans.');
        });

        Schema::create('loan_payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->unsignedBigInteger('installment_id')->nullable();
            $table->unsignedBigInteger('recorded_by')->nullable()
                ->comment('Admin id, or NULL when written by the cron');

            $table->string('source', 40)
                ->comment('opening_balance | scheduled_installment | manual | settlement');

            $table->decimal('amount_received', 18, 2)
                ->comment('Gross amount received from the borrower');
            $table->decimal('scheduled_amount', 18, 2)->default(0)
                ->comment('What was contractually due for this instalment');

            $table->decimal('late_fee_portion', 18, 2)->default(0)
                ->comment('Deducted BEFORE allocation. Separate ledger (spec s.16).');
            $table->decimal('allocatable_amount', 18, 2)
                ->comment('amount_received less late fees less unallocated excess');

            $table->decimal('capital_allocated', 18, 2);
            $table->decimal('profit_allocated', 18, 2);
            $table->decimal('unallocated_excess', 18, 2)->default(0)
                ->comment('Overpayment held. Never converted into profit (spec s.13).');

            $table->decimal('capital_ratio_applied', 5, 4);
            $table->decimal('profit_ratio_applied', 5, 4);

            $table->decimal('capital_outstanding_after', 18, 2);
            $table->decimal('profit_outstanding_after', 18, 2);

            $table->boolean('is_partial')->default(false);
            $table->boolean('is_overpayment')->default(false);

            $table->date('value_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('loan_id');
            $table->index('installment_id');
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_payment_allocations');

        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'capital_ratio',
                'profit_ratio',
                'capital_repaid',
                'profit_received',
                'late_fees_paid',
                'unallocated_credit',
                'allocation_ledger_active',
            ]);
        });
    }
};
