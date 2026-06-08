<?php

namespace App\Console\Commands;

use App\Constants\Status;
use App\Models\Admin;
use App\Models\Installment;
use App\Models\Loan;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Console\Command\Command as CommandAlias;

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

        $excludedLoanIds = [9,10,11,12,13,14,15,16,17,18];
        $installments = Installment::whereNull('given_at')
            ->whereNotIn('loan_id', $excludedLoanIds)
            ->with(['loan.user', 'loan.plan'])
            ->orderBy('installment_date')
            ->get();

        foreach ($installments as $installment) {
            $loan = $installment->loan;

            if (!$loan || !$loan->plan) {
                Log::warning("Skipping installment {$installment->id}: loan is null or plan missing");
                continue; // Skip this installment and continue to the next
            }

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
                Log::info('3 days before due');
                $manager = Admin::find($loan->approved_by);
                $shortCodes['manager_full_name'] = $manager->name;

                if ($manager){
                    notify($manager, 'LOAN_REMINDER_THREE_DAYS_DUE', $shortCodes);
                }
                else{
//                    notify(Admin::where('id','1')->first(), 'LOAN_REMINDER_THREE_DAYS_DUE', $shortCodes);
                }
                notify($user, "LOAN_REMINDER_THREE_DAYS_DUE", $shortCodes);
                continue;
            }

            // 📅 2. On due date
            if ($daysUntilDue == 2) {
                Log::info('2 days before due');
                $manager = Admin::find($loan->approved_by);

                if ($manager){
                    $shortCodes['manager_full_name'] = $manager->name;
                    notify($manager, 'LOAN_REMINDER_TWO_DAYS_DUE', $shortCodes);
                }
                else{
                    $shortCodes['manager_full_name'] = '';
//                    notify(Admin::where('id','1')->first(), 'LOAN_REMINDER_TWO_DAYS_DUE', $shortCodes);
                }

                notify($user, "LOAN_REMINDER_TWO_DAYS_DUE", $shortCodes);
                continue;
            }

            // ⚠️ 3. After due date but still within interval (grace period)
            if ($daysUntilDue < 0 && $daysUntilGraceEnds >= 0) {
                Log::info('loan is now due');
                if (is_null($installment->missed_payment_reminder_sent_at)) {
                    $daysLeft = $daysUntilGraceEnds;

                    $manager = Admin::find($loan->approved_by);

                    if ($manager){
                        $shortCodes['manager_full_name'] = $manager->name;
                        notify($manager, "LOAN_REMINDER_INSTALMENT_INTERVAL", $shortCodes);
                    }
                    else{
                        $shortCodes['manager_full_name'] = '';
//                        notify(Admin::where('id','1')->first(), 'LOAN_REMINDER_INSTALMENT_INTERVAL', $shortCodes);
                    }
                    notify($user, "LOAN_REMINDER_INSTALMENT_INTERVAL", $shortCodes);

                    $installment->missed_payment_reminder_sent_at = now();
                    $installment->save();
                    continue;
                }
            }

            // 🚨 4. After grace period — apply delay charge
            if (now()->greaterThan($graceEndDate)) {
                Log::info('charges applied');

                $delayCharge = 0;
                if ($plan->fixed_charge > 0) {
                    $delayCharge = $plan->fixed_charge;
                } elseif (!empty($plan->percent_charge)) {
                    $loan_balance = $loan->amount;
                    $delayCharge = $loan_balance * ($plan->percent_charge / 100);
                }

                if ($delayCharge > 0 && $user) {
                    DB::transaction(function () use ($loan, $user, $installment, $delayCharge) {
                        // 1. Track on installment row
                        $installment->delay_charge = (float) ($installment->delay_charge ?? 0) + $delayCharge;
                        $installment->save();

                        // 2. Accumulate on loan model
                        $loan->accrued_penalties = (float) $loan->accrued_penalties + $delayCharge;

                        // 3. Deduct from user balance
                        $user->balance = (float) $user->balance - $delayCharge;
                        $user->save();

                        // 4. Track how much was deducted
                        $loan->penalties_paid = (float) $loan->penalties_paid + $delayCharge;
                        $loan->penalties_last_run_at = now();
                        $loan->save();
                    });
                }

                $shortCodes['late_fee'] = showAmount($delayCharge);
                $shortCodes['balance']  = showAmount($user->balance);

                $manager = Admin::find($loan->approved_by);
                if ($manager) {
                    $shortCodes['manager_full_name'] = $manager->name;
                    notify($manager, 'CHARGES_APPLIED_ON_LOAN', $shortCodes);
                } else {
                    $shortCodes['manager_full_name'] = '';
                }
                notify($user, "CHARGES_APPLIED_ON_LOAN", $shortCodes);

                Log::info("Delay charge applied", [
                    'loan_number'     => $loan->loan_number,
                    'installment_id'  => $installment->id,
                    'due_date'        => $installmentDate->toDateString(),
                    'grace_end'       => $graceEndDate->toDateString(),
                    'delay_charge'    => $delayCharge,
                    'accrued_total'   => $loan->accrued_penalties,
                    'paid_total'      => $loan->penalties_paid,
                ]);
            }
        }
    }
}
