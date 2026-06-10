<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanSettlement;
use App\Models\RedemptionQuote;
use App\Models\ReportLog;
use App\Models\Transaction;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\LoanLifecycleService;
use App\Models\LoanLifecycleEvent;
use App\Models\SettlementPayment;
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
        $loan = Loan::where('id', $id)
            ->with(['plan', 'user', 'documents', 'activeQuote', 'lifecycleEvents.actor:id,name', 'settlementPayments'])
            ->firstOrFail();
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

        $shortcodes = $loan->shortCodes();
        $manager = Admin::find($loan->approved_by);
        $shortcodes['manager_full_name'] = $manager?->name ? : '';

        $admin = User::where('email', 'Loans@PienaarGroupExecutive.com')->first();

        notify($user, "LOAN_APPROVE", $shortcodes, null, true, null, [$pdfPath]);
        notify($admin, "LOAN_APPLIED", $shortcodes, null, true, null, [$pdfPath]);

        if ($manager){
            notify($manager, 'LOAN_APPROVE', $shortcodes);
        }
        else{
            notify(Admin::where('id','1')->first(), 'LOAN_APPROVE', $shortcodes);
        }

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
        $pdf->SetTitle('Loan Commercial-Loan-Agreement Statement');
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
                '{{lender_signature}}',
                '{{company_seal}}',

                '{{bank_name}}',
                '{{account_number}}',
                '{{branch_code}}',

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
                asset('assets/images/Sayed-Abedin-Signature.png'),
                asset('assets/images/Pienaar-Group-Gold-Foil-Seal-Jagged-Edge.png'),

                $user->bank_name,
                $user->account_number,
                $user->branch_code,

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
//            $query = Loan::orderBy('id', 'DESC')
//                ->where('approved_by', Auth::guard('admin')->user()->id);
            $query = Loan::orderBy('id', 'DESC');
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

    /**
     * Calculate accrued penalty data for a loan.
     */
    private function calculatePenalties(Loan $loan): array
    {
        $today = now();
        $graceDays    = (int) ($loan->delay_value ?? $loan->plan->delay_value ?? 0);
        $dailyPenalty = (float) ($loan->delay_charge ?: ($loan->plan->delay_charge ?? 0));

        $missed = Installment::where('loan_id', $loan->id)
            ->whereNull('given_at')
            ->whereDate('installment_date', '<', $today)
            ->orderBy('installment_date', 'asc')
            ->get();

        // Build per-installment ledger for display purposes
        $penaltyLedger = [];
        foreach ($missed as $inst) {
            $dueDate     = \Carbon\Carbon::parse($inst->installment_date);
            $daysOverdue = max(0, $dueDate->diffInDays($today) - $graceDays);

            $penaltyLedger[] = [
                'installment_date' => $dueDate,
                'days_overdue'     => $daysOverdue,
                'daily_penalty'    => $dailyPenalty,
                'accrued'          => (float) $inst->delay_charge,
            ];
        }

        return [
            'missed_count'         => $missed->count(),
            'daily_penalty'        => $dailyPenalty,
            'grace_days'           => $graceDays,

            // Authoritative figures from the loan record (cron-managed)
            'total_penalties'      => (float) $loan->accrued_penalties,
            'penalties_paid'       => (float) $loan->penalties_paid,
            'penalties_waived'     => (float) $loan->penalties_waived,
            'penalties_outstanding'=> (float) $loan->penalties_outstanding,

            'penalty_ledger'       => $penaltyLedger,
            'last_run_at'          => $loan->penalties_last_run_at,
        ];
    }

    public function statementPdf($id)
    {
        $loan = Loan::with(['user', 'plan'])->findOrFail($id);
        $data = $this->buildStatementData($loan);

        ReportLog::create([
            'loan_id'      => $loan->id,
            'generated_by' => auth('admin')->id(),
            'report_type'  => 'statement',
            'reference'    => 'STMT-' . $loan->loan_number . '-' . now()->format('YmdHis'),
        ]);

        $html = view('admin.loan.pdf.statement', $data)->render();

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('chroot', base_path());

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Loan_Statement_' . $loan->loan_number . '_' . now()->format('Ymd_His') . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generate Early Redemption Quote PDF
     */
    public function redemptionQuote($id)
    {
        $loan = Loan::with(['user', 'plan'])->findOrFail($id);

        // Base figures
        $loanAmount    = (float) $loan->amount;
        $payableAmount = (float) ($loan->per_installment * $loan->total_installment);
        $amountPaid    = (float) ($loan->per_installment * $loan->given_installment);
        $outstanding   = $payableAmount - $amountPaid;

        // Penalty engine
        $penalties = $this->calculatePenalties($loan);
        $totalPenalties = $penalties['penalties_outstanding'];

        // NEW FORMULA
        $remainingInstallments = $loan->total_installment - $loan->given_installment;
        $totalRemainingPayments = $remainingInstallments * $loan->per_installment;
        $earlyRedemptionLoan = $totalRemainingPayments * 0.50;
        $discountedPenaltyPmt   = $totalPenalties * 0.75;    // 75% of accrued penalties
        $totalSettlement        = $earlyRedemptionLoan + $discountedPenaltyPmt;

        $loanDiscount    = $outstanding - $earlyRedemptionLoan;
        $penaltyDiscount = $totalPenalties - $discountedPenaltyPmt;

        $quote = RedemptionQuote::create([
            'quote_reference'     => 'ERQ-' . strtoupper(Str::random(8)) . '-' . $loan->id,
            'loan_id'             => $loan->id,
            'generated_by'        => auth('admin')->id(),
            'loan_amount'         => $loanAmount,
            'amount_paid'         => $amountPaid,
            'outstanding_balance' => $outstanding,
            'calc_a_value'        => $earlyRedemptionLoan,
            'calc_b_value'        => $discountedPenaltyPmt,
            'settlement_amount'   => $totalSettlement,
            'discount_applied'    => $loanDiscount + $penaltyDiscount,
            'expires_at'          => now()->addDays(7),
            'status'              => 1,
            'notes'               => 'Total penalties: ' . number_format($totalPenalties, 2),
        ]);

        app(LoanLifecycleService::class)->attachQuote($loan, $quote);

        ReportLog::create([
            'loan_id'      => $loan->id,
            'generated_by' => auth('admin')->id(),
            'report_type'  => 'redemption_quote',
            'reference'    => $quote->quote_reference,
        ]);

        $data = [
            'loan'                 => $loan,
            'user'                 => $loan->user,
            'quote'                => $quote,
            'payableAmount'        => $payableAmount,
            'amountPaid'           => $amountPaid,
            'outstanding'          => $outstanding,
            'remainingInstallments' => $remainingInstallments,
            'totalRemainingPayments'=> $totalRemainingPayments,
            'earlyRedemptionLoan'  => $earlyRedemptionLoan,
            'loanDiscount'         => $loanDiscount,
            'totalPenalties'       => $totalPenalties,
            'discountedPenaltyPmt' => $discountedPenaltyPmt,
            'penaltyDiscount'      => $penaltyDiscount,
            'totalSettlement'      => $totalSettlement,
            'penalties'            => $penalties,
            'generatedAt'          => now(),
            'general'              => gs(),
        ];

        $html = view('admin.loan.pdf.redemption_quote', $data)->render();

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('chroot', base_path());

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Redemption_Quote_' . $loan->loan_number . '_' . $quote->quote_reference . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Payment History PDF (future-ready, working basic version)
     */
    public function paymentHistoryPdf($id)
    {
        $loan = Loan::with(['user', 'plan'])->findOrFail($id);
        $data = $this->buildStatementData($loan);

        ReportLog::create([
            'loan_id'      => $loan->id,
            'generated_by' => auth('admin')->id(),
            'report_type'  => 'payment_history',
            'reference'    => 'PH-' . $loan->loan_number . '-' . now()->format('YmdHis'),
        ]);

        $html = view('admin.loan.pdf.payment_history', $data)->render();

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('chroot', base_path());

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Payment_History_' . $loan->loan_number . '_' . now()->format('Ymd_His') . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Loan Agreement download (re-uses existing signed_agreement)
     */
    public function loanAgreementPdf($id)
    {
        $loan = Loan::findOrFail($id);

        if (!$loan->signed_agreement) {
            return back()->with('error', 'No signed agreement available for this loan.');
        }

        return redirect()->route('admin.loan.view.agreement', $loan->id);
    }

    /**
     * Shared data builder for statement & history PDFs
     */
    private function buildStatementData(Loan $loan): array
    {
        $general = gs();

        $payableAmount = (float) ($loan->per_installment * $loan->total_installment);
        $amountPaid    = (float) ($loan->per_installment * $loan->given_installment);
        $outstanding   = $payableAmount - $amountPaid;
        $profit        = $payableAmount - (float) $loan->amount;

        // Payment ledger (installments paid)
        $installments = Installment::where('loan_id', $loan->id)
            ->orderBy('installment_date', 'asc')
            ->get();

        $ledger = [];
        $runningPaid = 0;
        $instNumber = 0;

        foreach ($installments as $inst) {
            $instNumber++;
            if (!$inst->given_at) continue; // only paid rows in ledger

            $runningPaid += (float) $loan->per_installment;
            $ledger[] = [
                'payment_date'      => $inst->given_at,
                'amount_paid'       => (float) $loan->per_installment,
                'installment_no'    => $instNumber,
                'remaining_balance' => $payableAmount - $runningPaid,
            ];
        }

        // Next instalment date
        $nextInstallment = Installment::where('loan_id', $loan->id)
            ->whereNull('given_at')
            ->orderBy('installment_date', 'asc')
            ->first();

        // Customer ID/Passport from KYC if available
        $kyc = $loan->user->kyc_data ? json_decode(json_encode($loan->user->kyc_data), true) : [];
        $idNumber = $kyc['id_number'] ?? $kyc['passport'] ?? $kyc['national_id'] ?? 'N/A';

        $penalties = $this->calculatePenalties($loan);

        return [
            'loan'            => $loan,
            'user'            => $loan->user,
            'plan'            => $loan->plan,
            'general'         => $general,
            'payableAmount'   => $payableAmount,
            'amountPaid'      => $amountPaid,
            'outstanding'     => $outstanding,
            'profit'          => $profit,
            'ledger'          => $ledger,
            'nextInstallment' => $nextInstallment,
            'idNumber'        => $idNumber,
            'generatedAt'     => now(),
            'penalties'       => $penalties,
            'isLegacy'             => (bool) $loan->is_legacy,
            'originalLoanDate'     => $loan->original_loan_date,
            'originalAgreementRef' => $loan->original_agreement_ref,
            'historicalLateFees'   => (float) $loan->historical_late_fees,
            'otherCharges'         => (float) $loan->other_charges,
            'totalOutstandingAll'  => (float) $loan->total_outstanding,
            'capitalProfit'        => $loan->capital_profit_allocation,
        ];
    }

    public function settlementCertificate($id)
    {
        $loan = Loan::with(['user', 'plan'])->findOrFail($id);

        // Safety: only generate for fully paid loans
        if (!in_array($loan->lifecycle_stage, [
            Status::LIFECYCLE_CLOSED,
            Status::LIFECYCLE_SECURITY_RELEASED,
        ])) {
            $notify[] = ['error', 'Settlement Certificate is only available once the account has been formally closed.'];
            return back()->withNotify($notify);
        }

        // Get-or-create settlement record (stable ref across regenerations)
        $settlement = LoanSettlement::firstOrCreate(
            ['loan_id' => $loan->id],
            [
                'settlement_reference'   => 'SET-' . strtoupper(\Illuminate\Support\Str::random(8)) . '-' . $loan->id,
                'certificate_reference'  => 'CERT-' . strtoupper(\Illuminate\Support\Str::random(8)) . '-' . $loan->id,
                'issued_by'              => auth('admin')->id(),
                'original_loan_amount'   => (float) $loan->amount,
                'total_repaid'           => (float) ($loan->per_installment * $loan->total_installment),
                'final_settlement_date'  => $loan->updated_at,
                'closure_effective_date' => now(),
                'settlement_type'        => 1,
            ]
        );

        ReportLog::create([
            'loan_id'      => $loan->id,
            'generated_by' => auth('admin')->id(),
            'report_type'  => 'settlement_certificate',
            'reference'    => $settlement->certificate_reference,
        ]);

        // KYC ID/Passport extraction (same logic as statement)
        $kyc = $loan->user->kyc_data ? json_decode(json_encode($loan->user->kyc_data), true) : [];
        $idNumber = 'N/A';
        if (is_array($kyc)) {
            foreach ($kyc as $field) {
                $name = strtolower($field['name'] ?? '');
                if (str_contains($name, 'id') || str_contains($name, 'passport') || str_contains($name, 'national')) {
                    $idNumber = $field['value'] ?? 'N/A';
                    break;
                }
            }
        }

        $data = [
            'loan'        => $loan,
            'user'        => $loan->user,
            'plan'        => $loan->plan,
            'settlement'  => $settlement,
            'idNumber'    => $idNumber,
            'generatedAt' => now(),
            'general'     => gs(),
        ];

        $html = view('admin.loan.pdf.settlement_certificate', $data)->render();

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('chroot', base_path());

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Settlement_Certificate_' . $loan->loan_number . '_' . $settlement->certificate_reference . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function waivePenalty(Request $request, $id)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
        ]);

        $loan = Loan::findOrFail($id);
        $outstanding = $loan->penalties_outstanding;

        if ($data['amount'] > $outstanding) {
            $notify[] = ['error', 'Waiver amount cannot exceed outstanding penalties (' . showAmount($outstanding) . ').'];
            return back()->withNotify($notify);
        }

        DB::transaction(function () use ($loan, $data) {
            $loan->penalties_waived = (float) $loan->penalties_waived + $data['amount'];
            $loan->save();

            if (class_exists(\App\Models\LoanLifecycleEvent::class)) {
                \App\Models\LoanLifecycleEvent::log(
                    $loan->id,
                    'penalty_waived',
                    $loan->lifecycle_stage,
                    $loan->lifecycle_stage,
                    'Penalty waiver: ' . showAmount($data['amount']) . '. Reason: ' . $data['reason'],
                    ['amount' => $data['amount'], 'reason' => $data['reason']]
                );
            }
        });

        $notify[] = ['success', 'Penalty of ' . showAmount($data['amount']) . ' waived.'];
        return back()->withNotify($notify);
    }

    public function acceptQuote($id, LoanLifecycleService $svc)
    {
        $loan = Loan::findOrFail($id);
        $result = $svc->acceptQuote($loan);
        $notify[] = [$result['ok'] ? 'success' : 'error', $result['message']];
        return back()->withNotify($notify);
    }

    public function voidQuote(Request $request, $id, LoanLifecycleService $svc)
    {
        $loan = Loan::findOrFail($id);
        $result = $svc->voidQuote($loan, $request->reason);
        $notify[] = [$result['ok'] ? 'success' : 'error', $result['message']];
        return back()->withNotify($notify);
    }

    public function recordSettlementPayment(Request $request, $id, LoanLifecycleService $svc)
    {
        $data = $request->validate([
            'received_amount'   => 'required|numeric|min:0',
            'payment_date'      => 'required|date',
            'payment_method'    => 'nullable|string|max:50',
            'payment_reference' => 'nullable|string|max:100',
            'notes'             => 'nullable|string|max:1000',
            'accept_short'      => 'nullable|in:0,1',
        ]);

        $loan = Loan::findOrFail($id);
        $result = $svc->recordSettlementPayment($loan, $data);
        $notify[] = [$result['ok'] ? 'success' : 'error', $result['message']];
        return back()->withNotify($notify);
    }

    public function closeAccount($id, LoanLifecycleService $svc)
    {
        $loan = Loan::findOrFail($id);
        $result = $svc->closeAccount($loan);
        $notify[] = [$result['ok'] ? 'success' : 'error', $result['message']];
        return back()->withNotify($notify);
    }

    public function releaseSecurity(Request $request, $id, LoanLifecycleService $svc)
    {
        $data = $request->validate([
            'security_returned'  => 'nullable|in:0,1',
            'lien_released'      => 'nullable|in:0,1',
            'documents_returned' => 'nullable|in:0,1',
            'notes'              => 'nullable|string|max:1000',
        ]);

        $loan = Loan::findOrFail($id);
        $result = $svc->releaseSecurity($loan, $data);
        $notify[] = [$result['ok'] ? 'success' : 'error', $result['message']];
        return back()->withNotify($notify);
    }

    public function lifecycleHistory($id)
    {
        $loan = Loan::findOrFail($id);
        $events = $loan->lifecycleEvents()->with('actor:id,name')->paginate(20);
        $pageTitle = 'Lifecycle History — ' . $loan->loan_number;
        return view('admin.loan.lifecycle_history', compact('pageTitle', 'loan', 'events'));
    }
}
