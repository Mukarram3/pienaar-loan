<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Loan Statement — {{ $loan->loan_number }}</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 11px; color: #222; margin: 0; padding: 0; }
        .header { border-bottom: 3px solid #1a4d8c; padding-bottom: 12px; margin-bottom: 18px; }
        .header table { width: 100%; }
        .header .logo { font-size: 22px; font-weight: bold; color: #1a4d8c; }
        .header .meta { text-align: right; font-size: 10px; }
        .title { font-size: 16px; font-weight: bold; color: #1a4d8c; margin: 18px 0 10px; text-transform: uppercase; }
        .section { margin-bottom: 18px; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid td { padding: 6px 8px; border-bottom: 1px solid #eee; vertical-align: top; }
        .grid td.label { font-weight: bold; width: 35%; color: #555; }
        .ledger { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .ledger th { background: #1a4d8c; color: #fff; padding: 8px; text-align: left; font-size: 10px; }
        .ledger td { padding: 6px 8px; border-bottom: 1px solid #ddd; font-size: 10px; }
        .ledger tr:nth-child(even) { background: #f7f9fc; }
        .amount { font-weight: bold; }
        .footer { margin-top: 30px; padding-top: 12px; border-top: 1px solid #ccc; font-size: 9px; color: #777; text-align: center; }
        .badge { display: inline-block; padding: 3px 8px; background: #e8f0fa; color: #1a4d8c; border-radius: 3px; font-size: 10px; }
    </style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
    <table>
        <tr>
            <td class="logo">
                {{-- Replace with actual logo when available --}}
                <a href="{{ route('admin.dashboard') }}" class="sidebar__main-logo"><img width="100" height="100" src="{{ siteLogo() }}" alt="image"></a>
            </td>
            <td class="meta">
                <strong>Date Generated:</strong> {{ $generatedAt->format('d M Y, H:i') }}<br>
                <strong>Loan Ref:</strong> {{ $loan->loan_number }}
            </td>
        </tr>
    </table>
</div>

<div class="title">Statement of Loan Account</div>

@if($isLegacy ?? false)
    <div style="background:#fff3cd; padding:8px 12px; border-left:4px solid #f0ad4e; margin-bottom:14px; font-size:10px;">
        <strong>LEGACY LOAN</strong> — Imported from historical loan book.
        @if($originalAgreementRef) Original Agreement Ref: <strong>{{ $originalAgreementRef }}</strong> @endif
        @if($originalLoanDate) | Loan Date: <strong>{{ \Carbon\Carbon::parse($originalLoanDate)->format('d M Y') }}</strong> @endif
    </div>
@endif

{{-- CUSTOMER DETAILS --}}
<div class="section">
    <div class="title" style="font-size:12px;">Customer Details</div>
    <table class="grid">
        <tr>
            <td class="label">Full Name</td>
            <td>{{ trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? '')) ?: $user->username }}</td>
        </tr>
        <tr>
            <td class="label">ID / Passport Number</td>
            <td>{{ $idNumber }}</td>
        </tr>
        <tr>
            <td class="label">Mobile</td>
            <td>{{ ($user->dial_code ? '+'.$user->dial_code.' ' : '') . $user->mobile }}</td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td>{{ $user->email }}</td>
        </tr>
    </table>
</div>

{{-- LOAN DETAILS --}}
<div class="section">
    <div class="title" style="font-size:12px;">Loan Details</div>
    <table class="grid">
        <tr>
            <td class="label">Loan Plan</td>
            <td>{{ $plan->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Original Loan Amount</td>
            <td class="amount">{{ showAmount($loan->amount) }}</td>
        </tr>
        <tr>
            <td class="label">Total Repayable</td>
            <td class="amount">{{ showAmount($payableAmount) }}</td>
        </tr>
        <tr>
            <td class="label">Total Interest / Profit</td>
            <td class="amount">{{ showAmount($profit) }}</td>
        </tr>
        <tr>
            <td class="label">Instalment Amount</td>
            <td class="amount">{{ showAmount($loan->per_installment) }}</td>
        </tr>
        <tr>
            <td class="label">Instalment Frequency</td>
            <td>Every {{ $loan->installment_interval }} day(s)</td>
        </tr>
        <tr>
            <td class="label">Loan Start Date</td>
            <td>{{ $loan->approved_at ? \Carbon\Carbon::parse($loan->approved_at)->format('d M Y') : ($loan->created_at ? $loan->created_at->format('d M Y') : 'N/A') }}</td>
        </tr>
        <tr>
            <td class="label">Next Instalment Date</td>
            <td>{{ $nextInstallment ? \Carbon\Carbon::parse($nextInstallment->installment_date)->format('d M Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Current Outstanding Balance</td>
            <td class="amount" style="color:#c0392b;">{{ showAmount($outstanding) }}</td>
        </tr>
        <tr>
            <td class="label">Total Instalments</td>
            <td>{{ $loan->total_installment }}</td>
        </tr>
        <tr>
            <td class="label">Paid Instalments</td>
            <td>{{ $loan->given_installment }}</td>
        </tr>
        <tr>
            <td class="label">Remaining Instalments</td>
            <td>{{ $loan->total_installment - $loan->given_installment }}</td>
        </tr>
    </table>
</div>

{{-- CAPITAL / PROFIT ALLOCATION (Legacy Loans Only) --}}
@if($isLegacy && $capitalProfit)
    <div class="section">
        <div class="title" style="font-size:12px;">Capital &amp; Profit Allocation</div>
        <p style="font-size:9px; color:#777; margin: 0 0 8px 0; font-style:italic;">
            Each instalment is allocated {{ number_format($capitalProfit['capital_ratio'] * 100, 0) }}% Capital
            and {{ number_format($capitalProfit['profit_ratio'] * 100, 0) }}% Profit.
        </p>

        <table class="grid" style="margin-bottom: 8px;">
            <tr style="background:#f7f9fc;">
                <td colspan="2" style="font-weight:bold; color:#1a4d8c; padding: 8px;">Capital Position</td>
            </tr>
            <tr>
                <td class="label">Total Capital</td>
                <td class="amount">{{ showAmount($capitalProfit['total_capital']) }}</td>
            </tr>
            <tr>
                <td class="label">Capital Repaid</td>
                <td class="amount" style="color:#1e7e34;">{{ showAmount($capitalProfit['capital_repaid']) }}</td>
            </tr>
            <tr>
                <td class="label"><strong>Capital Outstanding</strong></td>
                <td class="amount" style="color:#c0392b;"><strong>{{ showAmount($capitalProfit['capital_outstanding']) }}</strong></td>
            </tr>
        </table>

        <table class="grid">
            <tr style="background:#f7f9fc;">
                <td colspan="2" style="font-weight:bold; color:#1a4d8c; padding: 8px;">Profit Position</td>
            </tr>
            <tr>
                <td class="label">Total Profit</td>
                <td class="amount">{{ showAmount($capitalProfit['total_profit']) }}</td>
            </tr>
            <tr>
                <td class="label">Profit Received</td>
                <td class="amount" style="color:#1e7e34;">{{ showAmount($capitalProfit['profit_received']) }}</td>
            </tr>
            <tr>
                <td class="label"><strong>Profit Outstanding</strong></td>
                <td class="amount" style="color:#c0392b;"><strong>{{ showAmount($capitalProfit['profit_outstanding']) }}</strong></td>
            </tr>
        </table>
    </div>
@endif

{{-- LATE FEES & PENALTIES --}}
<div class="section">
    <div class="title" style="font-size:12px;">Late Fees &amp; Penalties</div>
    <table class="grid">
        <tr>
            <td class="label">Number of Missed Payments</td>
            <td>{{ $penalties['missed_count'] }}</td>
        </tr>
        <tr>
            <td class="label">Daily Penalty Rate</td>
            <td class="amount">{{ showAmount($penalties['daily_penalty']) }}</td>
        </tr>
        <tr>
            <td class="label">Grace Period</td>
            <td>{{ $penalties['grace_days'] }} day(s)</td>
        </tr>
        <tr>
            <td class="label" style="color:#c0392b;"><strong>Total Penalties Accrued</strong></td>
            <td class="amount" style="color:#c0392b;"><strong>{{ showAmount($penalties['total_penalties']) }}</strong></td>
        </tr>
        @if($isLegacy ?? false)
            <tr>
                <td class="label">Historical Late Fees Imported</td>
                <td class="amount">{{ showAmount($historicalLateFees ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label">Other Charges / Legal Fees</td>
                <td class="amount">{{ showAmount($otherCharges ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label" style="color:#c0392b;"><strong>Total Outstanding (incl. all charges)</strong></td>
                <td class="amount" style="color:#c0392b;"><strong>{{ showAmount($totalOutstandingAll ?? 0) }}</strong></td>
            </tr>
        @endif
    </table>
    <p style="font-size:9px; color:#777; margin-top:6px; font-style:italic;">
        Penalties are tracked separately from the loan balance and are not included in the Current Outstanding Balance above.
    </p>
</div>

{{-- PAYMENT LEDGER --}}
<div class="section">
    <div class="title" style="font-size:12px;">Payment Ledger</div>
    @if(count($ledger))
        <table class="ledger">
            <thead>
            <tr>
                <th>Payment Date</th>
                <th>Amount Paid</th>
                <th>Instalment #</th>
                <th>Remaining Balance</th>
            </tr>
            </thead>
            <tbody>
            @foreach($ledger as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row['payment_date'])->format('d M Y') }}</td>
                    <td class="amount">{{ showAmount($row['amount_paid']) }}</td>
                    <td>{{ $row['installment_no'] }}</td>
                    <td class="amount">{{ showAmount($row['remaining_balance']) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p style="color:#777; font-style:italic;">No payments recorded yet.</p>
    @endif
</div>

{{-- FOOTER --}}
<div class="footer">
    <strong>System Generated Document</strong> &nbsp;|&nbsp;
    Generated: {{ $generatedAt->format('d M Y, H:i:s') }} &nbsp;|&nbsp;
    Ref: STMT-{{ $loan->loan_number }}<br>
    <span style="font-size:8px;">This statement is computer-generated and does not require a signature.</span>
</div>

</body>
</html>
