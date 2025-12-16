<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use TCPDF;

class LoanController extends Controller
{
    public $pageTitle;

    public function index()
    {
        $this->pageTitle = 'All Loans';
        return $this->loanData();
    }

    public function runningLoans($userId = 0)
    {
        $this->pageTitle = 'Running Loans';
        return $this->loanData('running', $userId);
    }

    public function pendingLoans($userId = 0)
    {
        $this->pageTitle = 'Pending Loans';
        return $this->loanData('pending', $userId);
    }

    public function in_review_Loans($userId = 0)
    {
        $this->pageTitle = 'In-Review Loans';
        return $this->loanData('in_review', $userId);
    }

    public function approved_Loans($userId = 0)
    {
        $this->pageTitle = 'Approved Loans';
        return $this->loanData('approved', $userId);
    }

    public function paidLoans($userId = 0)
    {
        $this->pageTitle = 'Paid Loans';
        return $this->loanData('paid', $userId);
    }

    public function rejectedLoans($userId = 0)
    {
        $this->pageTitle = 'Rejected Loans';
        return $this->loanData("rejected", $userId);
    }

    public function dueInstallment()
    {
        $this->pageTitle = 'Due Installment Loans';
        return $this->loanData("due");
    }

    public function details($id)
    {
        $loan      = Loan::where('id', $id)->with('plan', 'user')->firstOrFail();
        $pageTitle = 'Loan Details';
        return view('admin.loan.details', compact('pageTitle', 'loan'));
    }

    public function review($id)
    {
        $loan              = Loan::with('user', 'plan')->findOrFail($id);
        $loan->status      = Status::LOAN_IN_REVIEW;
        $loan->save();
        $user = $loan->user;

        notify($user, "LOAN_IN_REVIEW", $loan->shortCodes(), null, true, null);
        $notify[] = ['success', 'Loan is now under Review successfully'];
        return back()->withNotify($notify);
    }

    public function approve($id)
    {
        $loan              = Loan::with('user', 'plan')->findOrFail($id);
        $loan->status      = Status::LOAN_APPROVED;
        $loan->approved_at = now();
        $loan->save();

        $user = $loan->user;
        $pdfPath = $this->generateLoanPdf($user, $loan, $loan->plan);

        notify($user, "LOAN_APPROVE", $loan->shortCodes(), null, true, null, [$pdfPath]);
        $notify[] = ['success', 'Loan approved successfully'];
        return back()->withNotify($notify);
    }

    public function release_funds($id)
    {
        $loan              = Loan::with('user', 'plan')->findOrFail($id);
        $loan->status      = Status::LOAN_RUNNING;
        $loan->approved_at = now();
        $loan->save();
        Installment::saveInstallments($loan, now()->addDays((int) $loan->installment_interval));

        $user = $loan->user;
        $user->balance += getAmount($loan->amount);
        $user->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $loan->amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge       = 0;
        $transaction->trx_type     = '+';
        $transaction->details      = 'Loan taken';
        $transaction->trx          = getTrx();
        $transaction->remark       = 'loan_taken';
        $transaction->save();

        $shortCodes                          = $loan->shortCodes();
        $shortCodes['next_installment_date'] = now()->addDays((int) $loan->installment_interval);

        notify($user, "FUNDS_RELEASED", $loan->shortCodes(), null, true, null, null);
        $notify[] = ['success', 'Funds Released successfully'];
        return back()->withNotify($notify);
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

        $templatePath = dirname(base_path()) . '/assets/loan/PIENAARBANK-COMMERCIAL-LOAN-AGREEMENT.blade.php';
        $template = file_get_contents($templatePath);

        $logoPath = dirname(base_path()) . '/assets/images/logo_icon/logo.png';
        $template = str_replace('{{logo}}', $logoPath, $template);

        // Replace dynamic values
        $template = str_replace([
            '{{first_name}}',
            '{{last_name}}',
            '{{email}}',
            '{{mobile}}',
            '{{address}}',
            '{{city}}',
            '{{state}}',
            '{{zip}}',
            '{{country}}',
            '{{plan_name}}',
            '{{amount}}',
            '{{total_installment}}',
            '{{installment_interval}}',
            '{{per_installment}}',
            '{{profit_percentage}}',
            '{{application_fixed_charge}}',
            '{{application_percent_charge}}',
            '{{bank_name}}',
            '{{bank_account}}',
            '{{branch_code}}',
            '{{delay}}',
            '{{loan_number}}',
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
                $plan->name,
                number_format($loan->amount, 2, '.', ''),
                $loan->total_installment,
                $plan->installment_interval,
                number_format($loan->per_installment, 2, '.', ''),
                $plan->percent_charge,
                number_format($plan->application_fixed_charge, 2, '.', ''),
                $plan->application_percent_charge,
                $user->bank_name,
                $user->bank_account,
                $user->branch_code,
                $loan->delay_value,
                $loan->loan_number,
                number_format($plan->fixed_charge, 2, '.', ''),
                $plan->percent_charge,
                config('app.currency', 'ZAR')
            ], $template);

        $pdf->writeHTML($template, true, false, true, false, '');

        // ✅ Save PDF
        $directory = storage_path('app/loan_pdfs');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = 'loan_' . $loan->loan_number . '.pdf';
        $filePath = $directory . '/' . $fileName;

        $pdf->Output($filePath, 'F');

        return $filePath;
    }

    public function viewAgreement($id)
    {
        $loan = Loan::findOrFail($id);

        if (!$loan->signed_agreement || !Storage::exists($loan->signed_agreement)) {
            abort(404, 'Agreement not found.');
        }

        return response()->file(
            storage_path('app/' . $loan->signed_agreement),
            ['Content-Type' => 'application/pdf']
        );
    }

    public function assign(Request $request){
        $user = Admin::find($request->admin_id);
        $loan = Loan::find($request->id);
        $loan->approved_by = $request->admin_id;
        $loan->save();

        $notify[] = ['success', 'Loan Assigned successfully'];
        return back()->withNotify($notify);
    }

    public function reject(Request $request)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);
        $loan                 = Loan::where('id', $request->id)->with('user', 'plan')->firstOrFail();
        $loan->status         = Status::LOAN_REJECTED;
        $loan->admin_feedback = $request->reason;
        $loan->save();

        notify($loan->user, "LOAN_REJECT", $loan->shortCodes());

        $notify[] = ['success', 'Loan rejected successfully'];
        return back()->withNotify($notify);
    }

    protected function loanData($scope = null, $id = 0)
    {
        if (Auth::guard('admin')->user()->id != '1'){
            $query = Loan::orderBy('id', 'DESC')
                ->where('approved_by', Auth::guard('admin')->user()->id);
        }
        else{
            $query = Loan::orderBy('id', 'DESC');
        }

        if ($scope) {
            $query->$scope();
        }

        if ($id) {
            $query = $query->where('user_id', $id);
        }

        $pageTitle = $this->pageTitle;
        $loans     = $query->searchable(['loan_number', 'user:username'])->dateFilter()->filter(['status'])->with('user:id,username', 'plan', 'plan.category', 'nextInstallment')->paginate(getPaginate());
        return view('admin.loan.index', compact('pageTitle', 'loans'));
    }

    public function installments($id)
    {
        $loan         = Loan::with('installments')->findOrFail($id);
        $installments = $loan->installments()->paginate(getPaginate());
        $pageTitle    = "Installments";
        return view('admin.loan.installments', compact('pageTitle', 'installments', 'loan'));
    }
}
