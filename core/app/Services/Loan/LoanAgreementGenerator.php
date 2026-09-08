<?php

// =============================================================
// File: app/Services/Loan/LoanAgreementGenerator.php
// =============================================================
declare(strict_types=1);

namespace App\Services\Loan;

use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\LoanPlan;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use TCPDF;

/**
 * THE single renderer for the PienaarBank Commercial Loan Agreement.
 *
 * Previously this logic existed as two byte-identical copies in
 * Admin\LoanController::generateLoanPdf() and User\LoanController::generateLoanPdf().
 * Both now delegate here. Adding a third copy for legacy loans would have made
 * the divergence worse (spec s.20).
 *
 * IMPORTANT — behaviour preservation:
 * The STANDARD loan path produces byte-identical output to the previous
 * implementation. The known defects in that path (notably {{profit_percentage}}
 * being fed the raw Per Instalment %, and the instalment interval being read
 * from the plan rather than the loan) are reproduced deliberately so that
 * re-generating an agreement for an existing loan cannot change it. Those are
 * fixed only on the LEGACY path, which is new and has no historic output to
 * preserve.
 *
 * The agreement template itself lives OUTSIDE the application root at
 * ../assets/loan/. It is not modified by this class.
 */
class LoanAgreementGenerator
{
    public const CONTEXT_APPLICATION = 'application';   // pre-agreement, user applies
    public const CONTEXT_APPROVAL    = 'approval';      // commercial agreement, admin approves
    public const CONTEXT_LEGACY      = 'legacy';        // re-issued for an imported loan

    public const DOCUMENT_TYPE_REISSUED = 'reissued_agreement';

    /**
     * Generate the commercial agreement and return the absolute file path.
     */
    public function generate(
        User $user,
        Loan $loan,
        LoanPlan $plan,
        string $context = self::CONTEXT_APPROVAL,
        ?string $reference = null
    ): string {
        $templatePath = $this->templatePath($context);

        if (!is_readable($templatePath)) {
            throw new RuntimeException(
                'Loan agreement template not found or not readable at: ' . $templatePath
            );
        }

        $template = file_get_contents($templatePath);
        $template = str_replace('{{logo}}', $this->logoPath(), $template);

        $replacements = $context === self::CONTEXT_LEGACY
            ? $this->legacyReplacements($user, $loan, $plan, $reference)
            : $this->standardReplacements($user, $loan, $plan);

        $template = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template
        );

        return $this->writePdf($template, $this->filenameFor($loan, $context, $reference), $context);
    }

    /**
     * Generate a fresh agreement for an imported legacy loan, register it as a
     * loan document so there is an audit trail of every re-issue, and return
     * both the file path and the document record.
     *
     * The borrower signs this IN ADDITION TO their original agreement. The
     * original is never replaced or superseded by this operation.
     *
     * @return array{path:string, document:LoanDocument, reference:string, version:int}
     */
    public function reissueForLegacyLoan(Loan $loan): array
    {
        if (!$loan->is_legacy) {
            throw new RuntimeException(
                'Agreement re-issue is only available for imported legacy loans.'
            );
        }

        $loan->loadMissing(['user', 'plan']);

        if (!$loan->user || !$loan->plan) {
            throw new RuntimeException('Loan is missing its borrower or plan record.');
        }

        $version   = $this->nextVersion($loan);
        $reference = sprintf(
            'AGR-%s-L%d-V%d',
            strtoupper(Str::random(6)),
            $loan->id,
            $version
        );

        $absolutePath = $this->generate(
            $loan->user,
            $loan,
            $loan->plan,
            self::CONTEXT_LEGACY,
            $reference
        );

        $filename    = basename($absolutePath);
        $storagePath = 'loan_documents/' . $loan->id . '/' . $filename;

        Storage::disk('local')->put($storagePath, file_get_contents($absolutePath));

        $document = LoanDocument::create([
            'loan_id'           => $loan->id,
            'uploaded_by'       => auth('admin')->id(),
            'document_type'     => self::DOCUMENT_TYPE_REISSUED,
            'original_filename' => $filename,
            'file_path'         => $storagePath,
            'mime_type'         => 'application/pdf',
            'file_size'         => filesize($absolutePath) ?: null,
            'notes'             => sprintf(
                'Re-issued agreement %s (version %d) generated %s by %s. '
                . 'To be signed by the borrower in addition to the original agreement%s.',
                $reference,
                $version,
                now()->format('d M Y H:i'),
                auth('admin')->user()->name ?? 'system',
                $loan->original_agreement_ref ? ' ' . $loan->original_agreement_ref : ''
            ),
        ]);

        return [
            'path'      => $absolutePath,
            'document'  => $document,
            'reference' => $reference,
            'version'   => $version,
        ];
    }

    /**
     * How many re-issues already exist for this loan.
     */
    public function reissueCount(Loan $loan): int
    {
        return LoanDocument::where('loan_id', $loan->id)
            ->where('document_type', self::DOCUMENT_TYPE_REISSUED)
            ->count();
    }

    private function nextVersion(Loan $loan): int
    {
        return $this->reissueCount($loan) + 1;
    }

    // -----------------------------------------------------------------------
    // Placeholder maps
    // -----------------------------------------------------------------------

    /**
     * Standard path. Reproduces the previous implementation exactly.
     * Do not "fix" values here without a migration decision (spec s.21).
     */
    private function standardReplacements(User $user, Loan $loan, LoanPlan $plan): array
    {
        return [
            '{{first_name}}' => $user->firstname,
            '{{last_name}}'  => $user->lastname,
            '{{email}}'      => $user->email,
            '{{mobile}}'     => $user->mobile,
            '{{address}}'    => $user->address,
            '{{city}}'       => $user->city,
            '{{state}}'      => $user->state,
            '{{zip}}'        => $user->zip,
            '{{country}}'    => $user->country_name,

            '{{loan_number}}'                => $loan->loan_number,
            '{{plan_name}}'                  => $plan->name,
            '{{amount}}'                     => number_format((float) $loan->amount, 2, '.', ''),
            '{{total_installment}}'          => $loan->total_installment,
            '{{installment_interval}}'       => $plan->installment_interval,
            '{{per_installment}}'            => number_format((float) $loan->per_installment, 2, '.', ''),
            '{{profit_percentage}}'          => $plan->per_installment,
            '{{application_fixed_charge}}'   => number_format((float) $plan->application_fixed_charge, 2, '.', ''),
            '{{application_percent_charge}}' => ($plan->application_percent_charge / 100) * $loan->amount,

            '{{lender_signature}}' => asset('assets/images/Sayed-Abedin-Signature.png'),
            '{{company_seal}}'     => asset('assets/images/Pienaar-Group-Gold-Foil-Seal-Jagged-Edge.png'),

            '{{bank_name}}'      => $user->bank_name,
            '{{account_number}}' => $user->account_number,
            '{{branch_code}}'    => $user->branch_code,

            '{{delay}}'          => $loan->delay_value,
            '{{fixed_charge}}'   => number_format((float) $plan->fixed_charge, 2, '.', ''),
            '{{percent_charge}}' => $plan->percent_charge,
            '{{site_currency}}'  => config('app.currency', 'ZAR'),
        ];
    }

    /**
     * Legacy path.
     *
     * Every commercial figure is read from the STORED loan record — the values
     * the administrator entered at import — and never recalculated. A legacy
     * loan is already executed; re-deriving its terms here would contradict
     * spec s.21 and could contradict the borrower's original agreement.
     *
     * Differences from the standard path, all deliberate:
     *   - instalment interval comes from the LOAN, not the plan
     *   - application charges are ZERO (the advance has already been made, no
     *     new application fee is payable) — spec s.15, prevents double charging
     *   - profit % is derived from the stored total repayable, not from the
     *     Per Instalment % (which is a capital rate, not a finance charge)
     */
    private function legacyReplacements(
        User $user,
        Loan $loan,
        LoanPlan $plan,
        ?string $reference
    ): array {
        $principal      = (float) $loan->amount;
        $perInstallment = (float) $loan->per_installment;
        $count          = (int) $loan->total_installment;

        // Prefer the administrator-entered total repayable; fall back to the
        // stored schedule if the override was not captured.
        $totalRepayable = $loan->total_repayable_override !== null
            ? (float) $loan->total_repayable_override
            : $perInstallment * $count;

        $totalProfit  = max(0.0, $totalRepayable - $principal);
        $profitPct    = $principal > 0 ? ($totalProfit / $principal) * 100 : 0.0;

        $replacements = $this->standardReplacements($user, $loan, $plan);

        // Overrides
        $replacements['{{installment_interval}}']       = $loan->installment_interval;
        $replacements['{{profit_percentage}}']          = number_format($profitPct, 2, '.', '');
        $replacements['{{application_fixed_charge}}']   = number_format(0, 2, '.', '');
        $replacements['{{application_percent_charge}}'] = number_format(0, 2, '.', '');

        // Additional legacy-only tokens. str_replace is a no-op when a token is
        // absent from the template, so these are safe to send even though the
        // current template does not yet reference them. Add them to
        // PIENAARBANK-COMMERCIAL-LOAN-AGREEMENT.blade.php to surface them.
        $replacements['{{agreement_reference}}']      = $reference ?? '';
        $replacements['{{agreement_date}}']           = now()->format('d F Y');
        $replacements['{{is_legacy}}']                = 'Yes';
        $replacements['{{original_agreement_ref}}']   = $loan->original_agreement_ref ?? 'Not recorded';
        $replacements['{{original_loan_date}}']       = $loan->original_loan_date
            ? \Carbon\Carbon::parse($loan->original_loan_date)->format('d F Y')
            : 'Not recorded';
        $replacements['{{total_repayable}}']          = number_format($totalRepayable, 2, '.', '');
        $replacements['{{total_profit}}']             = number_format($totalProfit, 2, '.', '');
        $replacements['{{installments_paid}}']        = (int) $loan->given_installment;
        $replacements['{{installments_remaining}}']   = max(0, $count - (int) $loan->given_installment);

        // --- Payment allocation clause -----------------------------------
        // Rendered from the loan's CONTRACTED ratio, not a hard-coded 50/50,
        // so a plan on a different allocation produces a correct agreement.
        $allocator = app(LoanPaymentAllocator::class);
        $ratio     = $allocator->ratioFor($loan);

        $replacements['{{allocation_clause}}']       = $allocator->clauseText($loan);
        $replacements['{{capital_allocation_pct}}']  = $allocator->pct($ratio['capital']);
        $replacements['{{profit_allocation_pct}}']   = $allocator->pct($ratio['profit']);
        $replacements['{{capital_per_installment}}'] = number_format(
            round($perInstallment * $ratio['capital'], 2), 2, '.', ''
        );
        $replacements['{{profit_per_installment}}']  = number_format(
            round($perInstallment - round($perInstallment * $ratio['capital'], 2), 2), 2, '.', ''
        );

        return $replacements;
    }

    // -----------------------------------------------------------------------
    // Rendering
    // -----------------------------------------------------------------------

    private function templatePath(string $context): string
    {
        $file = $context === self::CONTEXT_APPLICATION
            ? 'PRE-LOAN-ADVICE-PRE-AGREEMENT-STATEMENT.blade.php'
            : 'PIENAARBANK-COMMERCIAL-LOAN-AGREEMENT.blade.php';

        return dirname(base_path()) . '/assets/loan/' . $file;
    }

    private function logoPath(): string
    {
        return dirname(base_path()) . '/assets/images/logo_icon/logo.png';
    }

    private function filenameFor(Loan $loan, string $context, ?string $reference): string
    {
        return match ($context) {
            self::CONTEXT_APPLICATION => 'pre_' . $loan->loan_number . '.pdf',
            self::CONTEXT_LEGACY      => 'Loan_Agreement_' . $loan->loan_number
                                          . '_' . ($reference ?? 'REISSUE') . '.pdf',
            default                   => 'loan_' . $loan->loan_number . '.pdf',
        };
    }

    private function writePdf(string $html, string $filename, string $context): string
    {
        $pdf = new TCPDF();
        $pdf->SetCreator('PienaarBank');
        $pdf->SetAuthor('PienaarBank');
        $pdf->SetTitle(
            $context === self::CONTEXT_APPLICATION
                ? 'Loan Pre-Agreement Statement'
                : 'Commercial Loan Agreement'
        );
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->setFontSubsetting(true);
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        $pdf->writeHTML($html, true, false, true, false, '');

        $directory = storage_path('app/loan_pdfs');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filePath = $directory . '/' . $filename;
        $pdf->Output($filePath, 'F');

        return $filePath;
    }
}
