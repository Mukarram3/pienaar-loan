<?php

// =============================================================
// File: app/Console/Commands/SeedLoanAllocationLedger.php
// =============================================================

namespace App\Console\Commands;

use App\Models\Loan;
use App\Services\Loan\LoanPaymentAllocator;
use Illuminate\Console\Command;

/**
 * Opens the capital/profit allocation ledger on legacy loans that were
 * imported BEFORE the ledger existed.
 *
 * This is a deliberate, manually invoked operation. It is not part of the
 * migration, because spec s.21 requires that deploying code must not
 * retrospectively touch the loan book on its own.
 *
 * What it does NOT do:
 *   - change any balance, instalment amount or total repayable
 *   - alter arrears, penalties or settlement figures
 *   - modify any agreement
 *
 * What it DOES do:
 *   - freeze the plan's capital/profit ratio onto each loan
 *   - record one opening entry stating the capital/profit composition of the
 *     amount already repaid
 *   - switch the ledger on so future receipts are allocated
 *
 * Runs as a DRY RUN by default. Pass --commit to write.
 *
 *   php artisan loans:seed-allocation-ledger
 *   php artisan loans:seed-allocation-ledger --commit
 *   php artisan loans:seed-allocation-ledger --loan=14 --commit
 */
class SeedLoanAllocationLedger extends Command
{
    protected $signature = 'loans:seed-allocation-ledger
                            {--commit : Write changes. Without this the command only reports.}
                            {--loan= : Restrict to a single loan id.}
                            {--include-standard : Also seed non-legacy loans. Off by default.}';

    protected $description = 'Open the capital/profit allocation ledger on existing loans (dry run by default).';

    public function handle(LoanPaymentAllocator $allocator): int
    {
        $commit = (bool) $this->option('commit');

        $query = Loan::with('plan')
            ->where('allocation_ledger_active', false);

        if (!$this->option('include-standard')) {
            $query->where('is_legacy', true);
        }

        if ($this->option('loan')) {
            $query->where('id', (int) $this->option('loan'));
        }

        $loans = $query->orderBy('id')->get();

        if ($loans->isEmpty()) {
            $this->info('No loans require seeding.');
            return self::SUCCESS;
        }

        if (!$commit) {
            $this->warn('DRY RUN — no changes will be written. Re-run with --commit to apply.');
        }

        $rows    = [];
        $skipped = 0;

        foreach ($loans as $loan) {
            if (!$loan->plan) {
                $this->error("Loan {$loan->id} ({$loan->loan_number}) has no plan. Skipped.");
                $skipped++;
                continue;
            }

            $paid = (float) $loan->per_installment * (int) $loan->given_installment;

            $capitalRatio = $loan->capital_ratio !== null
                ? (float) $loan->capital_ratio
                : (float) $loan->plan->capital_ratio;
            $profitRatio  = 1 - $capitalRatio;

            $capital = round($paid * $capitalRatio, 2);
            $profit  = round($paid - $capital, 2);

            $rows[] = [
                $loan->id,
                $loan->loan_number,
                number_format((float) $loan->amount, 2),
                $loan->given_installment . '/' . $loan->total_installment,
                number_format($paid, 2),
                $allocator->pct($capitalRatio) . '/' . $allocator->pct($profitRatio),
                number_format($capital, 2),
                number_format($profit, 2),
            ];

            if ($commit) {
                $allocator->seedOpeningBalance($loan, $paid);
            }
        }

        $this->table(
            ['ID', 'Loan Number', 'Amount', 'Paid', 'Repaid', 'Cap/Prof %', 'Capital', 'Profit'],
            $rows
        );

        $this->newLine();
        $this->info(sprintf(
            '%s %d loan(s).%s',
            $commit ? 'Seeded' : 'Would seed',
            count($rows),
            $skipped ? " {$skipped} skipped." : ''
        ));

        if (!$commit) {
            $this->warn('Nothing was written. Re-run with --commit once the figures above look right.');
        }

        return self::SUCCESS;
    }
}
