<?php

// =============================================================
// File: app/Http/Controllers/User/LoanController.php
// =============================================================
namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Category;
use App\Models\Installment;
use App\Models\Loan;
use App\Services\Loan\LoanAgreementGenerator;
use App\Models\LoanPlan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use TCPDF;

class LoanController extends Controller {
    public function list() {
        $pageTitle = 'My Loans';
        $loans     = Loan::where('user_id', auth()->id())->with('nextInstallment')->with('plan')->searchable(['loan_number'])->filter(['status'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.loan.list', compact('pageTitle', 'loans'));
    }

    public function submit_agreement(Request $request)
    {

        $loan = Loan::find($request->loan_id);

        $request->validate([
            'signed_agreement' => 'required|file|mimetypes:application/pdf',
        ]);

        $user = auth()->user();

        if ($request->hasFile('signed_agreement')) {
            $pdf = $request->file('signed_agreement');

            $fileName = 'agreement_' . time() . '_' . $user->id . '.pdf';
            $filePath = 'user_agreements/' . $fileName;

            // Save into storage/app/user_agreements/
            Storage::disk('local')->put($filePath, file_get_contents($pdf));

            // Store path in DB (assuming you have column)
            $loan->signed_agreement = $filePath;
            $loan->save();

            $notify[] = ['success', 'Agreement uploaded successfully.'];
            return back()->withNotify($notify);
        }

        $notify[] = ['error', 'No file uploaded.'];

        return back()->withNotify($notify);
    }

    public function plans() {
        $pageTitle = 'Loan Plans';
        $categories = Category::where('Status', Status::ENABLE)
            ->with(['plans' => function ($query) {
                $query->where('status', Status::ENABLE);
            }])
            ->whereHas('plans', function ($query) {
                $query->where('status', Status::ENABLE);
            })
            ->latest()
            ->get();
        return view('Template::user.loan.plans', compact('pageTitle', 'categories'));
    }

    public function applyLoan(Request $request, $id) {
        $plan = LoanPlan::active()->findOrFail($id);
        $request->validate(['amount' => "required|numeric|min:$plan->minimum_amount|max:$plan->maximum_amount"]);
        session()->put('loan', ['plan' => $plan, 'amount' => $request->amount]);
        return to_route('user.loan.apply.form');
    }

    public function loanPreview() {
        $loan = session('loan');
        if (!$loan) {
            return to_route('user.loan.plans');
        }
        $plan      = $loan['plan'];
        $amount    = $loan['amount'];
        $pageTitle = 'Apply For Loan';

        // Authoritative figures from the calculation engine. Null for plans
        // that do not use the Fixed Capital engine — the view then falls back
        // to its previous display (spec s.24).
        $plan  = LoanPlan::active()->findOrFail($plan->id);
        $quote = $plan->quoteFor((float) $amount);

        return view('Template::user.loan.form', compact('pageTitle', 'plan', 'amount', 'quote'));
    }

    public function confirm(Request $request) {

        $loan = session('loan');
        if (!$loan) {
            return to_route('user.loan.plans');
        }
        $plan   = $loan['plan'];
        $amount = $loan['amount'];
        $user            = auth()->user();

        $percentCharge = $amount * $plan->application_percent_charge / 100;

        $applicationFee = $plan->application_fixed_charge + $percentCharge;

//        if ($applicationFee > $user->balance) {
//            $notify[] = ['error', 'Insufficient balance. You have to pay the application fee.'];
//            return back()->withNotify($notify)->withInput($request->all());
//        }

        $plan   = LoanPlan::active()->with('category')->where('id', $plan->id)->firstOrFail();

        $formData       = $plan->form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $applicationForm = $formProcessor->processFormData($request, $formData);

        // Instalment figures come from the ONE calculation engine, the same one
        // that produced the quotation the borrower just reviewed (spec s.20).
        // Plans not on the Fixed Capital engine keep their previous formula.
        $quote = $plan->quoteFor((float) $amount);

        if ($quote) {
            $perInstallment       = $quote->totalPerInstallment;
            $total_amount_payable = $quote->totalContractualRepayment;
        } else {
            $total_amount_payable = ($amount * $plan->per_installment / 100 * $plan->total_installment) + $amount;
            $perInstallment       = $total_amount_payable / $plan->total_installment;
        }

        $percentCharge = $plan->per_installment * $plan->percent_charge / 100;
        $charge        = $plan->fixed_charge + $percentCharge;

        $user->balance -=  $applicationFee;
        $user->save();

        $applicationTrx = getTrx();

        $loan                         = new Loan();
        $loan->loan_number            =  $applicationTrx;
        $loan->user_id                = $user->id;
        $loan->plan_id                = $plan->id;

        $loan->amount                 = round($amount, 2);
        $loan->per_installment        = round($perInstallment, 2);
        $loan->charge_per_installment = round($charge, 2);

        $loan->installment_interval   = $plan->installment_interval;
        $loan->delay_value            = $plan->delay_value;
        $loan->total_installment      = $plan->total_installment;
        $loan->application_form       = $applicationForm;

        // Freeze the contracted capital/profit allocation onto the loan so a
        // later plan edit cannot re-split it (spec s.11, s.21).
        if ($quote) {
            $loan->capital_ratio            = $quote->terms->capitalRatio;
            $loan->profit_ratio             = $quote->terms->profitRatio;
            $loan->allocation_ledger_active = true;
        }

        $loan->save();


        //transaction
        $general = gs();
        $transaction = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       =  $applicationFee;
        $transaction->post_balance = $user->balance;
        $transaction->charge       = 0;
        $transaction->trx_type     = '-';
        $transaction->details      = showAmount($amount) . ' '   . 'Charged for application fee ' . $plan->name;
        $transaction->trx          = $applicationTrx;
        $transaction->remark       = 'application_fee';
        $transaction->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'New loan request';
        $adminNotification->click_url = urlPath('admin.loan.index') . '?search=' . $loan->loan_number;
        $adminNotification->save();

        session()->forget('loan');

        $admins = Admin::all();
        foreach ($admins as $admin){
            notify($admin, 'New_Loan_Application_Submitted', [
                'name' => $user->username,
                'amount' => showAmount($amount,currencyFormat:false),
                'email' => $user->email,
                'plan_name' => $plan->name,
                'loan_no' => $loan->loan_number,
            ]);
        }

        $shortcodes = $loan->shortCodes();
        $shortcodes['first_name'] = $user->firstname;
        $shortcodes['last_name'] = $user->lastname;
        $shortcodes['mobile'] = $user->mobile;
        $shortcodes['email'] = $user->email;
        $shortcodes['application_percent_charge'] = $applicationFee;

        $pdfPath = $this->generateLoanPdf($user, $loan, $plan);

        $admin = User::where('email', 'Loans@PienaarGroupExecutive.com')->first();

        notify($user, "LOAN_APPLIED", $shortcodes, null, true, null, [$pdfPath]);
        notify($admin, "LOAN_APPLIED", $shortcodes, null, true, null, [$pdfPath]);

        $notify[] = ['success', 'Loan application submitted successfully'];
        return to_route('user.loan.list')->withNotify($notify);
    }

    /**
     * Generate the Pre-Loan Advice / Pre-Agreement Statement.
     *
     * Delegates to LoanAgreementGenerator (spec s.20). Signature kept so
     * existing callers are unchanged. Output is unchanged.
     */
    public function generateLoanPdf($user, $loan, $plan)
    {
        return app(LoanAgreementGenerator::class)->generate(
            $user,
            $loan,
            $plan,
            LoanAgreementGenerator::CONTEXT_APPLICATION
        );
    }

    public function installments($loanNumber) {
        $loan         = Loan::where('loan_number', $loanNumber)->where('user_id', auth()->id())->firstOrFail();
        $installments = $loan->installments()
            ->orderBy('installment_date', 'asc')
            ->paginate(getPaginate());
        $pageTitle    = 'Loan Instalments';
        return view('Template::user.loan.installments', compact('pageTitle', 'installments', 'loan'));
    }

    public function pay_installment(Request $request){

        $installment = Installment::find($request->id);
        $loan = Loan::find($installment->loan_id);

        $total_installments = Installment::where('loan_id', $loan->id)->count();

        $installment->given_at = today();
        $installment->save();

        $paid_installments = Installment::where('loan_id', $loan->id)
            ->whereNotNull('given_at')
            ->count();

        $loan->given_installment = $paid_installments;

        $user = auth()->user();
        $user->balance = auth()->user()->balance - ($loan->per_installment + $installment->delay_charge);
        $user->save();

        // Post the receipt to the capital/profit allocation ledger.
        // Only for loans with the ledger switched on (spec s.21). The delay
        // charge is passed separately and is never allocated to capital or
        // profit (spec s.16).
        if ($loan->allocation_ledger_active) {
            try {
                app(\App\Services\Loan\LoanPaymentAllocator::class)->record(
                    loan:            $loan,
                    amountReceived:  (float) $loan->per_installment + (float) $installment->delay_charge,
                    scheduledAmount: (float) $loan->per_installment,
                    lateFeePortion:  (float) $installment->delay_charge,
                    source:          \App\Services\Loan\LoanPaymentAllocator::SOURCE_SCHEDULED,
                    installment:     $installment
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error(
                    'Allocation ledger write failed for loan ' . $loan->id . ': ' . $e->getMessage()
                );
            }
        }

        $shortCodes = $loan->shortCodes();
        $shortCodes['due_date'] = showDateTime($installment->installment_date, 'd M Y');
        $shortCodes['amount'] = showAmount($loan->per_installment + $installment->delay_charge,currencyFormat:false);
        $shortCodes['balance'] = showAmount(auth()->user()->balance,currencyFormat:false);
        $shortCodes['current_installment'] = $paid_installments;
        $shortCodes['total_installment'] = $total_installments;

        notify($user, 'Loan_Repayment_Received', $shortCodes);
        $loan_manager = Admin::find($loan->approved_by);
        if ($loan_manager){
            notify($loan_manager, 'Loan_Repayment_Received', $shortCodes);
        }
        else{
            notify(Admin::where('id','1')->first(), 'Loan_Repayment_Received', $shortCodes);
        }

        $allInstallments      = Installment::where('loan_id', $installment->loan_id)->count();
        $paidInstallments     = Installment::where('loan_id', $installment->loan_id)
            ->whereNotNull('given_at')
            ->count();

        if ($allInstallments > 0 && $allInstallments === $paidInstallments) {
                notify($user, 'Loan_Fully_Repaid', [
                    'loan_number' => $loan->loan_number,
                ]);

            $loan->status = Status::LOAN_PAID;
        }

        $loan->save();

        $notify[] = ['success', 'Installment Paid successfully'];
        return back()->withNotify($notify);
    }
}
