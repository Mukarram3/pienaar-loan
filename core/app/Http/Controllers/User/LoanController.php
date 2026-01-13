<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Category;
use App\Models\Installment;
use App\Models\Loan;
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
        $categories = Category::where('Status', Status::ENABLE)->with('plans')->whereHas('plans', function ($query) {
            $query->where('status', Status::ENABLE);
        })->latest()->get();
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
        return view('Template::user.loan.form', compact('pageTitle', 'plan', 'amount'));
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

        $total_amount_payable = ($amount * $plan->per_installment / 100 * $plan->total_installment) + $amount;
        $perInstallment = $total_amount_payable/$plan->total_installment;

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

        notify($user, "LOAN_APPLIED", $shortcodes, null, true, null, [$pdfPath]);
        $notify[] = ['success', 'Loan application submitted successfully'];
        return to_route('user.loan.list')->withNotify($notify);
    }

    public function generateLoanPdf($user, $loan, $plan)
    {
        $pdf = new TCPDF();
        $pdf->SetCreator('Your App');
        $pdf->SetAuthor('Your App');
        $pdf->SetTitle('Loan Pre-Agreement Statement');
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->setFontSubsetting(true);
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        $templatePath = dirname(base_path()) . '/assets/loan/PRE-LOAN-ADVICE-PRE-AGREEMENT-STATEMENT.blade.php';
        $template = file_get_contents($templatePath);

        $logoPath = dirname(base_path()) . '/assets/images/logo_icon/logo.png';
        $template = str_replace('{{logo}}', $logoPath, $template);

        // Replace dynamic values
        $template = str_replace(
            [
                '{{first_name}}',
                '{{last_name}}',
                '{{email}}',
                '{{mobile}}',
                '{{address}}',
                '{{city}}',
                '{{state}}',
                '{{zip}}',
                '{{country}}',

                '{{loan_number}}',
                '{{plan_name}}',
                '{{amount}}',
                '{{total_installment}}',
                '{{installment_interval}}',
                '{{per_installment}}',
                '{{profit_percentage}}',
                '{{application_fixed_charge}}',
                '{{application_percent_charge}}',

//                '{{bank_name}}',
//                '{{bank_account}}',
//                '{{branch_code}}',

                '{{delay}}',
                '{{fixed_charge}}',
                '{{percent_charge}}',
                '{{site_currency}}'
            ],
            [
                $user->firstname,
                $user->lastname,
                $user->email,
                $user->mobile,
                $user->address,
                $user->city,
                $user->state,
                $user->zip,
                $user->country_name,

                $loan->loan_number,
                $plan->name,
                number_format($loan->amount, 2, '.', ''),
                $loan->total_installment,
                $plan->installment_interval,
                number_format($loan->per_installment, 2, '.', ''),
                $plan->per_installment,
                number_format($plan->application_fixed_charge, 2, '.', ''),
                ($plan->application_percent_charge/100) * $loan->amount,

//                $user->bank_name,
//                $user->bank_account,
//                $user->branch_code,

                $loan->delay_value,
                number_format($plan->fixed_charge, 2, '.', ''),
                $plan->percent_charge,
                config('app.currency', 'ZAR')
            ],
            $template
        );


        $pdf->writeHTML($template, true, false, true, false, '');

        // ✅ Save PDF
        $directory = storage_path('app/loan_pdfs');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = 'pre_' . $loan->loan_number . '.pdf';
        $filePath = $directory . '/' . $fileName;

        $pdf->Output($filePath, 'F');

        return $filePath;
    }

    public function installments($loanNumber) {
        $loan         = Loan::where('loan_number', $loanNumber)->where('user_id', auth()->id())->firstOrFail();
        $installments = $loan->installments()->paginate(getPaginate());
        $pageTitle    = 'Loan Installments';
        return view('Template::user.loan.installments', compact('pageTitle', 'installments', 'loan'));
    }

    public function pay_installment(Request $request){

        $installment = Installment::find($request->id);
        $loan = Loan::find($installment->loan_id);

        $total_installments = Installment::where('loan_id', $loan->id)->count();
        $paid_installments = Installment::where('loan_id', $loan->id)
            ->whereNotNull('given_at')
            ->count();
        $current_installment_number = $paid_installments + 1;

        if (auth()->user()->balance >= $loan->charge_per_installment + $installment->delay_charge){
            $installment->given_at = today();
            $installment->save();

            $user = auth()->user();
            $user->balance = auth()->user()->balance - ($loan->per_installment + $installment->delay_charge);
            $user->save();

            $shortCodes = $loan->shortCodes();
            $shortCodes['due_date'] = showDateTime($installment->installment_date, 'd M Y');
            $shortCodes['amount'] = showAmount($loan->per_installment + $installment->delay_charge,currencyFormat:false);
            $shortCodes['balance'] = showAmount(auth()->user()->balance,currencyFormat:false);
            $shortCodes['current_installment'] = $current_installment_number;
            $shortCodes['total_installment'] = $total_installments;

//            notify($user, 'Loan_Repayment_Received', $shortCodes);

            $allInstallments      = Installment::where('loan_id', $installment->loan_id)->count();
            $paidInstallments     = Installment::where('loan_id', $installment->loan_id)
                ->whereNotNull('given_at')
                ->count();

            if ($allInstallments > 0 && $allInstallments === $paidInstallments) {
                notify($user, 'Loan_Fully_Repaid', [
                    'loan_number' => $loan->loan_number,
                ]);
            }

            $notify[] = ['success', 'Installment Paid successfully'];
            return back()->withNotify($notify);
        }
        else{
            $notify[] = ['error', 'Balance is less than Installment Amount'];
            return back()->withNotify($notify);
        }
    }
}
