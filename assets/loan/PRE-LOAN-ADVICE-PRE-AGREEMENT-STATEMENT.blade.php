<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: dejavusans, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .header {
            width: 100%;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
            padding: 0;
            border: none;
        }

        .logo-cell {
            padding-right: 20px;
        }

        .page-break {
            page-break-after: always;
        }

        .header-logo {
            width: 100px;
            height: auto;
        }

        .company-name {
            font-size: 24pt;
            font-weight: bold;
            text-align: right;
        }

        .document-title {
            font-size: 10pt;
            text-align: right;
        }

        .document-subtitle {
            font-size: 9pt;
            font-style: italic;
            text-align: right;
        }

        .divider {
            border-top: 2px dashed #000;
            margin: 12px 0;
        }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            margin: 15px 0 8px 0;
        }

        table.info-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.info-table td {
            padding: 3px 5px 3px 0;
            vertical-align: top;
        }

        table.info-table .label {
            width: 180px;
            font-weight: bold;
        }

        p {
            margin: 0 0 8px 0;
            text-align: justify;
        }

        ul {
            margin: 8px 0 8px 20px;
        }

        ul li {
            margin-bottom: 5px;
        }

        .bullet-item {
            margin: 5px 0;
        }

        .footer {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="header">
    <table class="header-table">
        <tr>
            <td width="25%" class="logo-cell">
                <img src="{{logo}}" class="header-logo" alt="Pienaar Group Logo">
            </td>
            <td width="75%">
                <div class="company-name">PIENAAR GROUP</div>
                <div class="document-title">
                    PIENAAR GROUP PRE-LOAN ADVICE / PRE-AGREEMENT STATEMENT
                </div>
                <div class="document-subtitle">
                    (Commercial Credit – Exempt from NCA Section 4(1)(a)(i))
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="divider"></div>

<div class="section-title">1. Borrower Information</div>

<table class="info-table">
    <tr>
        <td class="label">Name:</td>
        <td>{{first_name}} {{last_name}}</td>
    </tr>
    <tr>
        <td class="label">Email:</td>
        <td>{{email}}</td>
    </tr>
    <tr>
        <td class="label">Mobile Number:</td>
        <td>{{mobile}}</td>
    </tr>
    <tr>
        <td class="label">Address:</td>
        <td>
            {{address}},<br>
            {{city}},<br>
            {{state}},<br>
            {{zip}},<br>
            {{country}}
        </td>
    </tr>
</table>

<div class="divider"></div>

<div class="section-title">2. Nature and Purpose of the Credit</div>

<p>
    This loan is provided strictly for business, commercial, or investment purposes.
    It is not intended for personal, household, or domestic use.
</p>

<p>
    By signing this Agreement, the Borrower expressly confirms that the credit is being
    obtained for a business-related purpose and therefore falls outside the scope of the
    National Credit Act 34 of 2005, in accordance with Section 4(1)(a)(i).
</p>

<p>
    The Borrower acknowledges and accepts that this is a commercial transaction, and
    the protections applicable to consumer credit agreements do not apply.
</p>

<div class="divider"></div>

<div class="section-title">3. Loan Product Summary</div>

<div class="bullet-item">• Loan Product / Plan: {{plan_name}}</div>
<div class="bullet-item">• Requested Loan Amount: R{{amount}}</div>
<div class="bullet-item">• Total Instalments: {{total_installment}}</div>
<div class="bullet-item">• Repayment Frequency: Every {{installment_interval}} days</div>
<div class="bullet-item">• Instalment Amount: R{{per_installment}}</div>


<div class="header">
    <table class="header-table">
        <tr>
            <td width="25%" class="logo-cell">
                <img src="{{logo}}" class="header-logo" alt="Pienaar Group Logo">
            </td>
            <td width="75%">
                <div class="company-name">PIENAAR GROUP</div>
                <div class="document-title">
                    PIENAAR GROUP PRE-LOAN ADVICE / PRE-AGREEMENT STATEMENT
                </div>
                <div class="document-subtitle">
                    (Commercial Credit – Exempt from NCA Section 4(1)(a)(i))
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="bullet-item">• Profit / Interest Percentage: {{profit_percentage}}%</div>
<div class="bullet-item">• Application Charge: R{{application_percent_charge}}</div>

<p>A complete repayment schedule will be generated upon approval.</p>

<div class="divider"></div>

<div class="section-title">4. Bank Details for Disbursement</div>

<p>If approved, funds will be disbursed to:</p>

<div class="bullet-item">• Bank: </div>
<div class="bullet-item">• Account Number: </div>
<div class="bullet-item">• Branch Code: </div>

<p>The Borrower confirms these details are correct.</p>

<div class="divider"></div>

<div class="section-title">5. Total Cost of Credit</div>

<p>The total cost of credit includes:</p>

<ul>
    <li>Loan principal</li>
    <li>Profit/interest</li>
    <li>Application fixed charge</li>
    <li>Percentage-based charge (if any)</li>
    <li>Any late penalty charges</li>
</ul>

<p>A full cost breakdown will appear in the Loan Agreement.</p>

<div class="divider"></div>

<div class="page-break"></div>

<div class="header">
    <table class="header-table">
        <tr>
            <td width="25%" class="logo-cell">
                <img src="{{logo}}" class="header-logo" alt="Pienaar Group Logo">
            </td>
            <td width="75%">
                <div class="company-name">PIENAAR GROUP</div>
                <div class="document-title">
                    PIENAAR GROUP PRE-LOAN ADVICE / PRE-AGREEMENT STATEMENT
                </div>
                <div class="document-subtitle">
                    (Commercial Credit – Exempt from NCA Section 4(1)(a)(i))
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="section-title">6. Late Payment Fees</div>

<p>
    If payment is delayed by more than {{delay}} days the following charges will apply:
</p>

<div class="bullet-item">• Fixed Late Charge: {{fixed_charge}} {{site_currency}}</div>
<div class="bullet-item">• Percentage Charge: {{percent_charge}}%</div>
<div class="bullet-item">• Interval: Applied each time an instalment becomes overdue</div>

<div class="divider"></div>

<div class="section-title">7. Borrower Acknowledgement</div>

<p>By receiving this Pre-Agreement Statement, the Borrower confirms:</p>

<ul>
    <li>They understand all proposed costs and repayment obligations.</li>
    <li>They acknowledge this is a business/commercial loan.</li>
    <li>They have not been coerced or misled.</li>
    <li>They understand that signing the Loan Agreement will create a binding contract.</li>
    <li>They received this Pre-Agreement Statement before signing the Loan Agreement.</li>
</ul>

<div class="divider"></div>

<div class="footer">
    <strong>Pienaar Group</strong><br>
    Loans@PienaarGroupExecutive.com
</div>

</body>
</html>
