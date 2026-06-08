<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment History — {{ $loan->loan_number }}</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 11px; color: #222; margin: 0; padding: 0; }
        .header { border-bottom: 3px solid #1a4d8c; padding-bottom: 12px; margin-bottom: 18px; }
        .header table { width: 100%; }
        .header .logo { font-size: 22px; font-weight: bold; color: #1a4d8c; }
        .header .meta { text-align: right; font-size: 10px; }
        .title { font-size: 16px; font-weight: bold; color: #1a4d8c; margin: 18px 0 10px; text-transform: uppercase; }
        .section { margin-bottom: 18px; }
        .summary-grid { width: 100%; border-collapse: collapse; }
        .summary-grid td { padding: 8px 10px; border: 1px solid #ddd; vertical-align: top; width: 25%; }
        .summary-grid .lbl { font-size: 9px; color: #777; text-transform: uppercase; }
        .summary-grid .val { font-size: 13px; font-weight: bold; color: #1a4d8c; margin-top: 4px; }
        .ledger { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .ledger th { background: #1a4d8c; color: #fff; padding: 8px; text-align: left; font-size: 10px; }
        .ledger td { padding: 6px 8px; border-bottom: 1px solid #ddd; font-size: 10px; }
        .ledger tr:nth-child(even) { background: #f7f9fc; }
        .ledger tfoot td { background: #eef3f9; font-weight: bold; border-top: 2px solid #1a4d8c; }
        .amount { text-align: right; font-weight: bold; }
        .penalty-box { background: #fef5f0; padding: 12px; border-left: 4px solid #c0392b; margin: 16px 0; font-size: 10px; }
        .penalty-box .row { display: table; width: 100%; padding: 3px 0; }
        .penalty-box .row .lbl { display: table-cell; color: #555; }
        .penalty-box .row .val { display: table-cell; text-align: right; font-weight: bold; }
        .footer { margin-top: 30px; padding-top: 12px; border-top: 1px solid #ccc; font-size: 9px; color: #777; text-align: center; }
    </style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
    <table>
        <tr>
            <td class="logo">
                {{-- Replace with your actual logo from the statement blade --}}
                <a href="{{ route('admin.dashboard') }}" class="sidebar__main-logo"><img width="100" height="100" src="{{ siteLogo() }}" alt="image"></a>
            </td>
            <td class="meta">
                <strong>Date Generated:</strong> {{ $generatedAt->format('d M Y, H:i') }}<br>
                <strong>Loan Ref:</strong> {{ $loan->loan_number }}
            </td>
        </tr>
    </table>
</div>

<div class="title">Payment History</div>

{{-- SUMMARY BLOCK --}}
<div class="section">
    <table class="summary-grid">
        <tr>
            <td>
                <div class="lbl">Original Loan Amount</div>
                <div class="val">{{ showAmount($loan->amount) }}</div>
            </td>
            <td>
                <div class="lbl">Current Loan Balance</div>
                <div class="val">{{ showAmount($outstanding) }}</div>
            </td>
            <td>
                <div class="lbl">Instalments Paid</div>
                <div class="val">{{ $loan->given_installment }} / {{ $loan->total_installment }}</div>
            </td>
            <td>
                <div class="lbl">Remaining Instalments</div>
                <div class="val">{{ $loan->total_installment - $loan->given_installment }}</div>
            </td>
        </tr>
    </table>

    @if($isLegacy && $capitalProfit)
        <table class="summary-grid" style="margin-top:10px;">
            <tr>
                <td>
                    <div class="lbl">Capital Repaid</div>
                    <div class="val" style="color:#1e7e34;">{{ showAmount($capitalProfit['capital_repaid']) }}</div>
                </td>
                <td>
                    <div class="lbl">Capital Outstanding</div>
                    <div class="val" style="color:#c0392b;">{{ showAmount($capitalProfit['capital_outstanding']) }}</div>
                </td>
                <td>
                    <div class="lbl">Profit Received</div>
                    <div class="val" style="color:#1e7e34;">{{ showAmount($capitalProfit['profit_received']) }}</div>
                </td>
                <td>
                    <div class="lbl">Profit Outstanding</div>
                    <div class="val" style="color:#c0392b;">{{ showAmount($capitalProfit['profit_outstanding']) }}</div>
                </td>
            </tr>
        </table>
    @endif
</div>

{{-- CUSTOMER MINI --}}
<div class="section" style="font-size:10px; color:#555;">
    <strong>Customer:</strong> {{ trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? '')) ?: $user->username }}
    &nbsp;|&nbsp; <strong>Email:</strong> {{ $user->email }}
    &nbsp;|&nbsp; <strong>Plan:</strong> {{ $plan->name ?? 'N/A' }}
</div>

{{-- PAYMENT LEDGER --}}
<div class="section">
    <div class="title" style="font-size:12px;">Payment Ledger</div>
    @if(count($ledger))
        <table class="ledger">
            <thead>
            <tr>
                <th>Payment Date</th>
                <th>Instalment #</th>
                <th style="text-align:right;">Amount Paid</th>
                <th style="text-align:right;">Remaining Loan Balance</th>
            </tr>
            </thead>
            <tbody>
            @php $totalPaid = 0; @endphp
            @foreach($ledger as $row)
                @php $totalPaid += $row['amount_paid']; @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row['payment_date'])->format('d M Y') }}</td>
                    <td>{{ $row['installment_no'] }}</td>
                    <td class="amount">{{ showAmount($row['amount_paid']) }}</td>
                    <td class="amount">{{ showAmount($row['remaining_balance']) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2">Total Paid to Date</td>
                <td class="amount">{{ showAmount($totalPaid) }}</td>
                <td class="amount">{{ showAmount($outstanding) }}</td>
            </tr>
            </tfoot>
        </table>
    @else
        <p style="color:#777; font-style:italic;">No payments recorded yet.</p>
    @endif
</div>

{{-- PENALTIES SECTION --}}
@if($penalties['total_penalties'] > 0 || $penalties['missed_count'] > 0)
    <div class="section">
        <div class="title" style="font-size:12px; color:#c0392b;">Outstanding Penalties &amp; Charges</div>
        <div class="penalty-box">
            <div class="row">
                <span class="lbl">Number of Missed Payments</span>
                <span class="val">{{ $penalties['missed_count'] }}</span>
            </div>
            <div class="row">
                <span class="lbl">Daily Penalty Rate</span>
                <span class="val">{{ showAmount($penalties['daily_penalty']) }}</span>
            </div>
            <div class="row">
                <span class="lbl">Total Penalties Accrued</span>
                <span class="val">{{ showAmount($penalties['total_penalties']) }}</span>
            </div>
            @if($penalties['penalties_paid'] > 0)
                <div class="row">
                    <span class="lbl" style="color:#1e7e34;">Penalties Paid from Balance</span>
                    <span class="val" style="color:#1e7e34;">{{ showAmount($penalties['penalties_paid']) }}</span>
                </div>
            @endif
            <div class="row" style="border-top:1px solid #c0392b; padding-top:6px; margin-top:4px;">
                <span class="lbl" style="color:#c0392b; font-weight:bold;">Outstanding Penalties</span>
                <span class="val" style="color:#c0392b; font-size:12px;">{{ showAmount($penalties['penalties_outstanding']) }}</span>
            </div>
        </div>
    </div>
@endif

{{-- FOOTER --}}
<div class="footer">
    <strong>System Generated Document</strong> &nbsp;|&nbsp;
    Generated: {{ $generatedAt->format('d M Y, H:i:s') }} &nbsp;|&nbsp;
    Ref: PH-{{ $loan->loan_number }}<br>
    <span style="font-size:8px;">This payment history is computer-generated and does not require a signature.</span>
</div>

</body>
</html>
