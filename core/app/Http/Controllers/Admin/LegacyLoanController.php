<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\LoanPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LegacyLoanController extends Controller
{
    public function create()
    {
        $pageTitle = 'Import Legacy Fixed Term Loan';
        $legacyPlans = LoanPlan::legacy()->where('status', 1)->get();
        $users = User::where('status', 1)->orderBy('firstname')->get(['id', 'firstname', 'lastname', 'username', 'email']);
        return view('admin.loan.legacy.create', compact('pageTitle', 'legacyPlans', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Borrower
            'user_id'                   => 'required|exists:users,id',
            'plan_id'                   => 'required|exists:loan_plans,id',

            // Original loan info
            'original_loan_amount'      => 'required|numeric|min:0',
            'total_repayable_amount'    => 'required|numeric|min:0',
            'original_loan_date'        => 'required|date|before_or_equal:today',
            'installment_amount'        => 'required|numeric|min:0',
            'total_installments'        => 'required|integer|min:1',
            'installment_interval'      => 'required|integer|min:1',
            'original_agreement_ref'    => 'nullable|string|max:80',

            // Current position
            'installments_paid'         => 'required|integer|min:0',
            'missed_installments'       => 'required|integer|min:0',
            'days_late'                 => 'required|integer|min:0',
            'daily_late_fee'            => 'required|numeric|min:0',
            'total_late_fees_accrued'   => 'required|numeric|min:0',
            'other_charges'             => 'required|numeric|min:0',
            'grace_period_days'         => 'nullable|integer|min:0',
            'current_outstanding_balance' => 'nullable|numeric|min:0',
            'next_installment_date'     => 'nullable|date|after_or_equal:today',

            // Documents
            'original_agreement'        => 'nullable|file|mimes:pdf|max:10240',
            'supporting_documents.*'    => 'nullable|file|max:10240',
            'notes'                     => 'nullable|string|max:2000',
        ]);

        if ($validated['installments_paid'] > $validated['total_installments']) {
            return back()->withErrors(['installments_paid' => 'Paid instalments cannot exceed total instalments.'])->withInput();
        }

        $plan = LoanPlan::findOrFail($validated['plan_id']);
        if (!$plan->is_legacy) {
            return back()->withErrors(['plan_id' => 'Selected plan is not a Legacy Fixed Term Loan plan.'])->withInput();
        }

        DB::beginTransaction();

        try {
            $remaining = $validated['total_installments'] - $validated['installments_paid'];
            $status = $remaining > 0 ? Status::LOAN_RUNNING : Status::LOAN_PAID;

            $loan = new Loan();
            $loan->loan_number              = 'LGCY-' . strtoupper(uniqid());
            $loan->user_id                  = $validated['user_id'];
            $loan->plan_id                  = $validated['plan_id'];
            $loan->is_legacy                = 1;

            $loan->amount                   = $validated['original_loan_amount'];
            $loan->per_installment          = $validated['installment_amount'];
            $loan->total_installment        = $validated['total_installments'];
            $loan->given_installment        = $validated['installments_paid'];
            $loan->installment_interval     = $validated['installment_interval'];

            $loan->delay_value              = $validated['grace_period_days'] ?? 0;
            $loan->delay_charge             = $validated['daily_late_fee'];
            $loan->charge_per_installment   = 0;

            $loan->original_loan_date       = $validated['original_loan_date'];
            $loan->original_agreement_ref   = $validated['original_agreement_ref'];
            $loan->total_repayable_override = $validated['total_repayable_amount'];
            $loan->historical_late_fees     = $validated['total_late_fees_accrued'];
            $loan->other_charges            = $validated['other_charges'];
            $loan->historical_missed_count  = $validated['missed_installments'];
            $loan->historical_days_late     = $validated['days_late'];
            $loan->next_installment_date    = $validated['next_installment_date'] ?? null;

            $loan->application_form = [
                (object)['name' => 'import_source',          'type' => 'text', 'value' => 'legacy_migration'],
                (object)['name' => 'imported_by',            'type' => 'text', 'value' => auth('admin')->user()->name ?? 'system'],
                (object)['name' => 'imported_at',            'type' => 'text', 'value' => now()->toDateTimeString()],
                (object)['name' => 'original_agreement_ref', 'type' => 'text', 'value' => $validated['original_agreement_ref'] ?? null],
                (object)['name' => 'original_loan_date',     'type' => 'text', 'value' => $validated['original_loan_date']],
                (object)['name' => 'admin_notes',            'type' => 'text', 'value' => $validated['notes'] ?? null],
            ];

            $loan->status      = $status;
            $loan->approved_by = auth('admin')->id();
            $loan->reviewed_by = auth('admin')->id();
            $loan->approved_at = Carbon::parse($validated['original_loan_date']);
            $loan->created_at  = Carbon::parse($validated['original_loan_date']);
            $loan->updated_at  = now();
            $loan->save();

            // Generate installments — uses next_installment_date if provided
            $this->generateLegacyInstallments($loan, $validated['next_installment_date'] ?? null);

            if ($request->hasFile('original_agreement')) {
                $this->storeDocument($loan, $request->file('original_agreement'), 'original_agreement');
            }

            if ($request->hasFile('supporting_documents')) {
                foreach ($request->file('supporting_documents') as $file) {
                    $this->storeDocument($loan, $file, 'supporting');
                }
            }

            DB::commit();

            $notify[] = ['success', 'Legacy loan imported successfully. Loan #' . $loan->loan_number];
            return redirect()->route('admin.loan.details', $loan->id)->withNotify($notify);

        } catch (\Throwable $e) {
            DB::rollBack();
            $notify[] = ['error', 'Failed to import: ' . $e->getMessage()];
            return back()->withNotify($notify)->withInput();
        }
    }

    /**
     * Generate installment ledger for a legacy loan.
     * Paid installments backdated from original_loan_date.
     * Future installments forward from today.
     */
    protected function generateLegacyInstallments(Loan $loan, ?string $nextDueDate = null): void
    {
        $startDate          = Carbon::parse($loan->original_loan_date);
        $interval           = (int) $loan->installment_interval;
        $totalInstallments  = (int) $loan->total_installment;
        $paidCount          = (int) $loan->given_installment;

        $installments = [];
        $cursor = $startDate->copy();

        for ($i = 0; $i < $totalInstallments; $i++) {
            $inst = new Installment();

            if ($i < $paidCount) {
                // Historical instalments — dated from original_loan_date forward
                $inst->installment_date = $cursor->copy()->format('Y-m-d');
                $inst->given_at = $cursor->copy();
            } else {
                // First unpaid instalment — use next_installment_date if provided
                if ($i === $paidCount && $nextDueDate) {
                    $cursor = Carbon::parse($nextDueDate);
                }
                $inst->installment_date = $cursor->copy()->format('Y-m-d');
            }

            $installments[] = $inst;
            $cursor->addDays($interval);
        }

        $loan->installments()->saveMany($installments);
    }

    /**
     * Persist an uploaded document.
     */
    protected function storeDocument(Loan $loan, $file, string $documentType): LoanDocument
    {
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        $path = 'loan_documents/' . $loan->id . '/' . $filename;

        Storage::disk('local')->put($path, file_get_contents($file));

        return LoanDocument::create([
            'loan_id'           => $loan->id,
            'uploaded_by'       => auth('admin')->id(),
            'document_type'     => $documentType,
            'original_filename' => $file->getClientOriginalName(),
            'file_path'         => $path,
            'mime_type'         => $file->getClientMimeType(),
            'file_size'         => $file->getSize(),
        ]);
    }

    public function edit($id)
    {
        $loan = Loan::with('user', 'plan', 'documents')->findOrFail($id);
        if (!$loan->is_legacy) {
            $notify[] = ['error', 'This is not a legacy loan.'];
            return redirect()->route('admin.loan.details', $loan->id)->withNotify($notify);
        }
        $pageTitle = 'Edit Legacy Loan';
        return view('admin.loan.legacy.edit', compact('pageTitle', 'loan'));
    }

    public function update(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        if (!$loan->is_legacy) {
            $notify[] = ['error', 'This is not a legacy loan.'];
            return back()->withNotify($notify);
        }

        $validated = $request->validate([
            'historical_late_fees' => 'required|numeric|min:0',
            'other_charges'        => 'required|numeric|min:0',
            'notes'                => 'nullable|string|max:2000',
        ]);

        $loan->historical_late_fees = $validated['historical_late_fees'];
        $loan->other_charges        = $validated['other_charges'];
        $loan->save();

        $notify[] = ['success', 'Legacy loan updated.'];
        return redirect()->route('admin.loan.details', $loan->id)->withNotify($notify);
    }
}
