<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: dejavusans, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
        }

        .header {
            width: 100%;
            border-bottom: 1px solid #888;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header-table {
            width: 100%;
        }

        .header-logo {
            width: 110px;
        }

        .company-name {
            font-size: 22pt;
            font-weight: bold;
            color: #444;
            text-align: right;
        }

        .document-title {
            font-size: 11pt;
            color: #444;
            text-align: right;
        }

        .document-subtitle {
            font-size: 9pt;
            color: #777;
            text-align: right;
        }

        .section-title {
            font-size: 13pt;
            font-weight: bold;
            padding: 10px 0 5px 0;
            border-bottom: 1px solid #ccc;
            margin-bottom: 10px;
            color: #333;
        }

        .info-line {
            margin-bottom: 6px;
        }

        .label {
            font-weight: bold;
            width: 180px;
            display: inline-block;
        }

        ul {
            margin-left: 20px;
            margin-bottom: 10px;
        }

        .page-break {
            page-break-after: always;
        }

        .page-number {
            text-align: center;
            color: #777;
            font-size: 9pt;
            margin-top: 20px;
        }

        .footer {
            margin-top: 35px;
            font-size: 10pt;
            color: #555;
        }

    </style>
</head>
<body>

<!-- PAGE 1 HEADER -->
<div class="header">
    <table class="header-table">
        <tr>
            <td width="30%">
                <img src="{{logo}}" class="header-logo">
            </td>
            <td width="70%" style="text-align:right;">
                <div class="company-name">PIENAAR GROUP LIMITED</div>
                <div class="document-title">PIENAARBANK PRE-LOAN ADVICE / PRE-AGREEMENT STATEMENT</div>
                <div class="document-subtitle">(Commercial Credit – Exempt: NCA Section 4(1)(a)(i))</div>
            </td>
        </tr>
    </table>
</div>

<!-- Borrower Information -->
<div class="section-title">Borrower Information</div>

<div class="info-line"><span class="label">Name:</span> {{first_name}} {{last_name}}</div>
<div class="info-line"><span class="label">Email:</span> {{email}}</div>
<div class="info-line"><span class="label">Mobile Number:</span> {{mobile}}</div>
<div class="info-line"><span class="label">Address:</span> {{address}}</div>
<div class="info-line"><span class="label">City:</span> {{city}}</div>
<div class="info-line"><span class="label">State:</span> {{state}}</div>
<div class="info-line"><span class="label">Postal Code:</span> {{zip}}</div>
<div class="info-line"><span class="label">Country:</span> {{country}}</div>

<div class="section-title">1. Nature and Purpose of the Credit</div>

<p>This proposed loan is intended strictly for <strong>business, commercial, or investment purposes</strong>. It is not a personal or household credit agreement.</p>

<p>The Borrower acknowledges that this agreement is <strong>exempt from the National Credit Act 34 of 2005</strong> in terms of <strong>Section 4(1)(a)(i)</strong>.</p>

<div class="section-title">2. Loan Product Summary</div>

<div class="info-line"><span class="label">Loan Product / Plan:</span> {{plan_name}}</div>
<div class="info-line"><span class="label">Requested Loan Amount:</span> {{amount}}</div>
<div class="info-line"><span class="label">Total Instalments:</span> {{total_installment}}</div>
<div class="info-line"><span class="label">Repayment Frequency:</span> Every {{installment_interval}} days</div>
<div class="info-line"><span class="label">Instalment Amount:</span> {{per_installment}}</div>
<div class="info-line"><span class="label">Profit / Interest %:</span> {{profit_percentage}}%</div>
<div class="info-line"><span class="label">Application Fixed Charge:</span> {{application_fixed_charge}}</div>
<div class="info-line"><span class="label">Application Percentage Charge:</span> {{application_percent_charge}}%</div>

<p>A complete repayment schedule will be generated upon loan approval.</p>

<div class="page-number">1</div>
<div class="page-break"></div>

<!-- PAGE 2 HEADER -->
<div class="header">
    <table class="header-table">
        <tr>
            <td width="30%">
                <img src="{{logo}}" class="header-logo">
            </td>
            <td width="70%" style="text-align:right;">
                <div class="company-name">PIENAAR GROUP LIMITED</div>
                <div class="document-title">PIENAARBANK PRE-LOAN ADVICE / PRE-AGREEMENT STATEMENT</div>
                <div class="document-subtitle">(Commercial Credit – Exempt: NCA Section 4(1)(a)(i))</div>
            </td>
        </tr>
    </table>
</div>

<div class="section-title">3. Bank Details for Disbursement</div>

<div class="info-line"><span class="label">Bank:</span> {{bank_name}}</div>
<div class="info-line"><span class="label">Account Number:</span> {{bank_account}}</div>
<div class="info-line"><span class="label">Branch Code:</span> {{branch_code}}</div>

<p>The Borrower confirms the accuracy of the above banking details.</p>

<div class="section-title">4. Total Cost of Credit</div>

<p>The total cost of credit will include:</p>

<ul>
    <li>Loan principal amount</li>
    <li>Profit / interest</li>
    <li>Application fixed charge</li>
    <li>Percentage-based charge</li>
    <li>Late penalty charges (if applicable)</li>
</ul>

<p>A complete breakdown will appear in the final Loan Agreement.</p>

<div class="section-title">5. Late Payment Fees</div>

<p>If payment is delayed by more than <strong>{{delay}}</strong> days, the following charges apply:</p>

<div class="info-line"><span class="label">Fixed Late Fee:</span> {{fixed_charge}}</div>
<div class="info-line"><span class="label">Percentage Charge:</span> {{percent_charge}}%</div>
<div class="info-line"><span class="label">Charge Interval:</span> Each overdue instalment</div>

<div class="page-number">2</div>
<div class="page-break"></div>

<!-- PAGE 3 HEADER -->
<div class="header">
    <table class="header-table">
        <tr>
            <td width="30%">
                <img src="{{logo}}" class="header-logo">
            </td>
            <td width="70%" style="text-align:right;">
                <div class="company-name">PIENAAR GROUP LIMITED</div>
                <div class="document-title">PIENAARBANK PRE-LOAN ADVICE / PRE-AGREEMENT STATEMENT</div>
                <div class="document-subtitle">(Commercial Credit – Exempt: NCA Section 4(1)(a)(i))</div>
            </td>
        </tr>
    </table>
</div>

<div class="section-title">6. Borrower Acknowledgement</div>

<p>By receiving this Pre-Agreement Statement, the Borrower confirms:</p>

<ul>
    <li>They understand all proposed costs and repayment obligations.</li>
    <li>They acknowledge that this is a business/commercial loan.</li>
    <li>They have not been misled or coerced into the agreement.</li>
    <li>They understand the loan agreement will be legally binding.</li>
    <li>They received this statement before signing the final agreement.</li>
</ul>

<div class="footer">
    <strong>PienaarBank Credit Department</strong><br>
    loans@pienaarbank.com
</div>

<div class="page-number">3</div>

</body>
</html>
