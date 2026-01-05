<?php

namespace App\Console\Commands;

use App\Models\Installment;
use App\Models\Loan;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LoanDelay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:loan-delay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        Log::info('cron job running');

        $installments = Installment::whereNull('given_at')
            ->with(['loan.user', 'loan.plan'])
            ->orderBy('installment_date')
            ->get()
            ->groupBy('loan_id')
            ->map(fn($group) => $group->first());

        foreach ($installments as $installment) {
            $loan = $installment->loan;
            $plan = $loan->plan;
            $user = $loan->user;

            try {
                $shortCodes = $loan->shortCodes();
                $shortCodes['due_date'] = showDateTime($installment->installment_date, 'd M Y');
                $shortCodes['amount'] = showAmount($loan->charge_per_installment);
                $shortCodes['plan_name'] = $plan->name;
                $shortCodes['loan_number'] = $loan->loan_number;
                $shortCodes['per_installment'] = $loan->per_installment;
                $shortCodes['installment_interval'] = $loan->installment_interval;
                $shortCodes['charge_per_installment'] = $loan->charge_per_installment;
            }
            catch (\Exception $e) {
                Log::error("Notify failed for user {$user->id}: " . $e->getMessage());
            }

            if (!$plan || !$user) continue;

            $installmentDate = Carbon::parse($installment->installment_date);
            $intervalDays = (int) $plan->installment_interval; // e.g. 7 days grace period
            $graceEndDate = $installmentDate->copy()->addDays($intervalDays);

            $daysUntilDue = now()->startOfDay()->diffInDays($installmentDate->startOfDay(), false);
            $daysUntilGraceEnds = now()->startOfDay()->diffInDays($graceEndDate->startOfDay(), false);

//            Reminder - 3 days before due date
            if ($daysUntilDue == 3) {
                notify($user, "LOAN_REMINDER_THREE_DAYS_DUE", $shortCodes);
                continue;
            }

            // 📅 2. On due date
            if ($daysUntilDue == 2) {
                notify($user, "LOAN_REMINDER_TWO_DAYS_DUE", $shortCodes);
                continue;
            }

            // ⚠️ 3. After due date but still within interval (grace period)
            if ($daysUntilDue < 0 && $daysUntilGraceEnds >= 0) {
                if (is_null($installment->missed_payment_reminder_sent_at)) {
                    $daysLeft = $daysUntilGraceEnds;

                    notify($user, "LOAN_REMINDER_INSTALMENT_INTERVAL", $shortCodes);

                    $installment->missed_payment_reminder_sent_at = now();
                    $installment->save();
                    continue;
                }
            }

            // 🚨 4. After grace period — apply delay charge
            if (now()->greaterThan($graceEndDate)) {

                $delayCharge = 0;
                if ($plan->fixed_charge > 0) {
                    $delayCharge = $plan->fixed_charge;
                } elseif (!empty($plan->percent_charge)) {
                    $unpaid_loan_count = Installment::where('loan_id',$loan->id)->whereNull('given_at')->count();
                    $remaining_loan_balance = $loan->per_installment * $unpaid_loan_count;
                    $delayCharge = $remaining_loan_balance * ($plan->percent_charge / 100);
                }

                // Apply and save
                $installment->delay_charge = ($installment->delay_charge ?? 0) + $delayCharge;
                $installment->save();

                $shortCodes['late_fee'] = $delayCharge;
                $shortCodes['balance'] = $user->balance;
                notify($user, "CHARGES_APPLIED_ON_LOAN", $shortCodes);

                Log::info("Delay charge applied", [
                    'installment_id' => $installment->id,
                    'due_date' => $installmentDate->toDateString(),
                    'grace_end' => $graceEndDate->toDateString(),
                    'delay_charge' => $delayCharge,
                ]);
            }
        }
    }
}
