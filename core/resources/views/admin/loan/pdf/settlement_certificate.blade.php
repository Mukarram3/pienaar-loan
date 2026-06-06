<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Settlement Certificate — {{ $settlement->certificate_reference }}</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 11px; color: #222; margin: 0; padding: 0; }
        .header { border-bottom: 3px solid #1a4d8c; padding-bottom: 12px; margin-bottom: 18px; }
        .header table { width: 100%; }
        .header .logo { font-size: 22px; font-weight: bold; color: #1a4d8c; }
        .header .meta { text-align: right; font-size: 10px; }
        .cert-title { text-align: center; font-size: 18px; font-weight: bold; color: #1a4d8c; margin: 20px 0 6px; text-transform: uppercase; letter-spacing: 1px; }
        .cert-subtitle { text-align: center; font-size: 10px; color: #777; margin-bottom: 20px; font-style: italic; }
        .title { font-size: 13px; font-weight: bold; color: #1a4d8c; margin: 18px 0 8px; text-transform: uppercase; border-bottom: 1px solid #1a4d8c; padding-bottom: 3px; }
        .section { margin-bottom: 16px; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid td { padding: 6px 8px; border-bottom: 1px solid #eee; vertical-align: top; }
        .grid td.label { font-weight: bold; width: 40%; color: #555; }
        .confirmation { background: #f7f9fc; padding: 14px; border-left: 4px solid #1a4d8c; margin: 14px 0; font-style: italic; color: #333; line-height: 1.6; }
        .status-box { background: #1e7e34; color: #fff; padding: 18px; margin: 20px 0; text-align: center; border-radius: 4px; }
        .status-box .big { font-size: 22px; font-weight: bold; letter-spacing: 2px; }
        .status-box .sub { font-size: 11px; margin-top: 6px; opacity: 0.9; }
        .release-list { padding-left: 0; list-style: none; }
        .release-list li { padding: 6px 0 6px 22px; position: relative; font-size: 10.5px; line-height: 1.5; }
        .release-list li:before { content: "✓"; position: absolute; left: 0; color: #1e7e34; font-weight: bold; font-size: 14px; }
        .good-standing { background: #eaf5ec; padding: 14px; border: 1px solid #1e7e34; margin: 14px 0; font-style: italic; color: #1e4624; line-height: 1.6; }
        .signature-block { margin-top: 30px; padding-top: 20px; border-top: 1px dashed #aaa; }
        .signature-block table { width: 100%; }
        .signature-block td { width: 50%; padding: 10px; vertical-align: top; font-size: 10px; }
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
                <strong>Certificate Ref:</strong> {{ $settlement->certificate_reference }}<br>
                <strong>Date Generated:</strong> {{ $generatedAt->format('d M Y, H:i') }}<br>
                <strong>Loan Ref:</strong> {{ $loan->loan_number }}
            </td>
        </tr>
    </table>
</div>

<div class="cert-title">Loan Settlement &amp; Account Closure Certificate</div>
<div class="cert-subtitle">Official confirmation of full loan settlement and account closure</div>

{{-- CUSTOMER DETAILS --}}
<div class="section">
    <div class="title">Customer Details</div>
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
            <td class="label">Loan Reference Number</td>
            <td><strong>{{ $loan->loan_number }}</strong></td>
        </tr>
    </table>
</div>

{{-- LOAN SUMMARY --}}
<div class="section">
    <div class="title">Loan Summary</div>
    <table class="grid">
        <tr>
            <td class="label">Original Loan Amount</td>
            <td>{{ showAmount($settlement->original_loan_amount) }}</td>
        </tr>
        <tr>
            <td class="label">Total Amount Repaid</td>
            <td>{{ showAmount($settlement->total_repaid) }}</td>
        </tr>
        <tr>
            <td class="label">Date of Final Settlement</td>
            <td>{{ $settlement->final_settlement_date ? $settlement->final_settlement_date->format('d M Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Settlement Reference Number</td>
            <td><strong>{{ $settlement->settlement_reference }}</strong></td>
        </tr>
    </table>
</div>

{{-- SETTLEMENT CONFIRMATION --}}
<div class="section">
    <div class="title">Settlement Confirmation</div>
    <div class="confirmation">
        This is to certify that the above loan account has been fully settled and redeemed. All amounts due under the Loan Agreement, including principal, interest, fees, charges and any agreed settlement amounts, have been paid and received in full.
    </div>
</div>

{{-- ACCOUNT STATUS --}}
<div class="status-box">
    <div class="big">ACCOUNT STATUS: CLOSED</div>
    <div class="sub">Effective Date of Closure: {{ $settlement->closure_effective_date->format('d M Y') }}</div>
</div>

{{-- RELEASE OF SECURITY --}}
<div class="section">
    <div class="title">Release of Security</div>
    <p style="font-size:10.5px; margin: 6px 0;">Upon confirmation of settlement:</p>
    <ul class="release-list">
        <li>The loan account is closed in full.</li>
        <li>All security pledged in favour of the lender is released.</li>
        <li>Any liens, encumbrances, charges, pledges or security interests registered against the borrower's assets in relation to this loan are discharged and lifted.</li>
        <li>Any documents, title deeds, certificates, guarantees or other security held by the lender shall be returned to the borrower, subject to internal verification procedures.</li>
        <li>The borrower is released from all future obligations arising from this loan agreement.</li>
    </ul>
</div>

{{-- GOOD STANDING --}}
<div class="section">
    <div class="title">Good Standing Confirmation</div>
    <div class="good-standing">
        The lender confirms that the borrower has satisfied all obligations under the loan agreement and that no further amounts are due or payable in respect of this account.
    </div>
</div>

{{-- SIGNATURE BLOCK --}}
<div class="signature-block">
    <table>
        <tr>
            <td>
                <strong>Authorised Signatory</strong><br>
                {{ $general->site_name ?? 'Rapid Lab' }}<br>
                <span style="color:#777; font-size:9px;">Date: {{ $generatedAt->format('d M Y') }}</span>
            </td>
            <td style="text-align:right;">
                <strong>Certificate Reference</strong><br>
                {{ $settlement->certificate_reference }}<br>
                <span style="color:#777; font-size:9px;">Issued: {{ $generatedAt->format('d M Y, H:i') }}</span>
            </td>
        </tr>
    </table>
</div>

{{-- FOOTER --}}
<div class="footer">
    <strong>System Generated Document</strong> &nbsp;|&nbsp;
    Generated: {{ $generatedAt->format('d M Y, H:i:s') }} &nbsp;|&nbsp;
    Cert Ref: {{ $settlement->certificate_reference }}<br>
    <span style="font-size:8px;">This is a system-generated Loan Settlement &amp; Account Closure Certificate and serves as confirmation that the account has been settled in full and closed on the lender's records.</span>
</div>

</body>
</html>
