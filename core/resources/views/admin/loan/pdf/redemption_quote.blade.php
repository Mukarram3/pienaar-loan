<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Early Redemption Quote — {{ $quote->quote_reference }}</title>
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
        .grid td.label { font-weight: bold; width: 55%; color: #555; }
        .amount { text-align: right; font-weight: bold; }
        .summary-box { background: #f7f9fc; padding: 14px; border-left: 4px solid #1a4d8c; margin: 16px 0; }
        .summary-box .row { display: table; width: 100%; padding: 4px 0; }
        .summary-box .row .lbl { display: table-cell; font-weight: bold; color: #555; }
        .summary-box .row .val { display: table-cell; text-align: right; font-weight: bold; }
        .penalty-box { background: #fef5f0; padding: 14px; border-left: 4px solid #c0392b; margin: 16px 0; }
        .highlight-box { background: #1a4d8c; color: #fff; padding: 18px; margin: 20px 0; text-align: center; border-radius: 4px; }
        .highlight-box .label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
        .highlight-box .big { font-size: 26px; font-weight: bold; margin-top: 6px; }
        .terms { font-size: 9px; color: #555; margin-top: 20px; padding: 12px; background: #f7f9fc; border-left: 3px solid #1a4d8c; }
        .footer { margin-top: 30px; padding-top: 12px; border-top: 1px solid #ccc; font-size: 9px; color: #777; text-align: center; }
        .expiry-warn { color: #c0392b; font-weight: bold; }
    </style>
</head>
<body>

{{-- HEADER (matches Statement) --}}
<div class="header">
    <table>
        <tr>
            <td class="logo">
                {{-- Replace with your actual logo from the statement blade --}}
                <a href="{{ route('admin.dashboard') }}" class="sidebar__main-logo"><img width="100" height="100" src="{{ siteLogo() }}" alt="image"></a>
            </td>
            <td class="meta">
                <strong>Quote Reference:</strong> {{ $quote->quote_reference }}<br>
                <strong>Issued:</strong> {{ $generatedAt->format('d M Y, H:i') }}<br>
                <strong class="expiry-warn">Expires:</strong> {{ $quote->expires_at->format('d M Y, H:i') }}
            </td>
        </tr>
    </table>
</div>

<div class="title">Early Redemption Quote</div>

{{-- CUSTOMER & LOAN REFERENCE --}}
<div class="section">
    <table class="grid">
        <tr>
            <td class="label">Loan Number</td>
            <td><strong>{{ $loan->loan_number }}</strong></td>
        </tr>
        <tr>
            <td class="label">Customer Name</td>
            <td>{{ trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? '')) ?: $user->username }}</td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <td class="label">Mobile</td>
            <td>{{ ($user->dial_code ? '+'.$user->dial_code.' ' : '') . $user->mobile }}</td>
        </tr>
    </table>
</div>

{{-- LOAN REDEMPTION SECTION --}}
<div class="section">
    <div class="title" style="font-size:12px;">Loan Redemption</div>
    <div class="summary-box">
        <div class="row">
            <span class="lbl">Original Loan Amount</span>
            <span class="val">{{ showAmount($quote->loan_amount) }}</span>
        </div>
        <div class="row">
            <span class="lbl">Total Amount Paid to Date</span>
            <span class="val">{{ showAmount($amountPaid) }}</span>
        </div>
        <div class="row">
            <span class="lbl">Instalments Remaining</span>
            <span class="val">{{ $remainingInstallments }} × {{ showAmount($loan->per_installment) }}</span>
        </div>
        <div class="row">
            <span class="lbl">Total Remaining Payments</span>
            <span class="val">{{ showAmount($totalRemainingPayments) }}</span>
        </div>
        <div class="row" style="border-top:1px solid #1a4d8c; padding-top:8px; margin-top:4px;">
            <span class="lbl" style="color:#1a4d8c;">Early Redemption Loan Figure (50% of remaining)</span>
            <span class="val" style="color:#1a4d8c; font-size:13px;">{{ showAmount($earlyRedemptionLoan) }}</span>
        </div>
        <div class="row" style="font-size:10px; color:#777;">
            <span class="lbl">Loan Discount Offered</span>
            <span class="val">{{ showAmount($loanDiscount) }}</span>
        </div>
    </div>
</div>

{{-- PENALTIES SECTION --}}
<div class="section">
    <div class="title" style="font-size:12px; color:#c0392b;">Outstanding Penalties &amp; Late Fees</div>
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
            <span class="val">{{ showAmount($totalPenalties) }}</span>
        </div>
        <div class="row" style="border-top:1px solid #c0392b; padding-top:8px; margin-top:4px;">
            <span class="lbl" style="color:#c0392b;">Discounted Penalty Settlement (75%)</span>
            <span class="val" style="color:#c0392b; font-size:13px;">{{ showAmount($discountedPenaltyPmt) }}</span>
        </div>
        <div class="row" style="font-size:10px; color:#777;">
            <span class="lbl">Penalty Discount Offered</span>
            <span class="val">{{ showAmount($penaltyDiscount) }}</span>
        </div>
    </div>
    <p style="font-size:9px; color:#777; margin-top:6px; font-style:italic;">
        Penalties are negotiated separately from the loan redemption amount.
    </p>
</div>

{{-- TOTAL SETTLEMENT --}}
<div class="highlight-box">
    <div class="label">Total Settlement Figure</div>
    <div class="big">{{ showAmount($totalSettlement) }}</div>
    <div style="font-size:10px; margin-top:8px; opacity:0.9;">
        Loan ({{ showAmount($earlyRedemptionLoan) }}) + Penalty ({{ showAmount($discountedPenaltyPmt) }})
    </div>
    <div style="font-size:10px; margin-top:6px;">Valid until {{ $quote->expires_at->format('d M Y, H:i') }}</div>
</div>

{{-- TERMS --}}
<div class="terms">
    <strong>Terms &amp; Conditions:</strong>
    <ol style="padding-left: 18px; margin: 6px 0;">
        <li>This quote is valid for 7 days from the date of issue and expires automatically thereafter.</li>
        <li>The loan redemption figure and penalty settlement may be negotiated independently.</li>
        <li>Settlement must be made in a single payment of the full agreed amount.</li>
        <li>Once settlement is received and confirmed, the loan account will be closed and marked as paid in full.</li>
        <li>Partial payments do not qualify as early settlement and will be applied to scheduled instalments.</li>
        <li>This quote does not constitute a waiver of any rights under the original loan agreement until settlement is received.</li>
        <li>All settlements are processed in {{ $general->cur_text ?? 'ZAR' }}.</li>
    </ol>
</div>

{{-- FOOTER (matches Statement) --}}
<div class="footer">
    <strong>System Generated Document</strong> &nbsp;|&nbsp;
    Generated: {{ $generatedAt->format('d M Y, H:i:s') }} &nbsp;|&nbsp;
    Quote Ref: {{ $quote->quote_reference }}<br>
    <span style="font-size:8px;">This quote is computer-generated and is binding only when accompanied by full settlement payment within the validity window.</span>
</div>

</body>
</html>
