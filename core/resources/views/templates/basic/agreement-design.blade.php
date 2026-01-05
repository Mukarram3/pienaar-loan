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

        /* Header */
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
            border: none;
            padding: 0;
        }

        .header-table td.logo-cell {
            padding-right: 20px;
        }

        .header-logo {
            width: 100px;
            height: auto;
        }

        .company-name {
            font-size: 24pt;
            font-weight: bold;
            color: #000;
            text-align: right;
            margin: 0;
            padding: 0;
            line-height: 1;
        }

        .document-title {
            font-size: 10pt;
            color: #333;
            text-align: right;
            margin: 3px 0 0 0;
            padding: 0;
        }

        .document-subtitle {
            font-size: 9pt;
            color: #666;
            font-style: italic;
            text-align: right;
            margin: 2px 0 0 0;
            padding: 0;
        }

        /* Section Title */
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            padding: 8px 0 5px 0;
            margin: 15px 0 10px 0;
        }

        /* Horizontal Divider */
        .divider {
            width: 100%;
            border-top: 2px dashed #000;
            margin: 12px 0;
        }

        /* Info Table */
        table.info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }

        table.info-table td {
            padding: 3px 5px 3px 0;
            border: none;
            vertical-align: top;
            line-height: 1.4;
        }

        table.info-table td.label {
            width: 180px;
            font-weight: bold;
        }

        /* Paragraphs */
        p {
            margin: 0 0 8px 0;
            text-align: justify;
            line-height: 1.5;
        }

        /* Bullet Items */
        .bullet-item {
            margin: 5px 0;
            padding-left: 0;
            line-height: 1.5;
        }

        /* Lists */
        ul {
            margin: 8px 0 8px 20px;
            padding: 0;
            list-style-type: disc;
        }

        ul li {
            margin-bottom: 5px;
            line-height: 1.4;
        }

        /* Strong/Bold */
        strong, b {
            font-weight: bold;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            font-size: 10pt;
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
                <div class="document-title">PIENAAR GROUP COMMERCIAL LOAN AGREEMENT</div>
                <div class="document-subtitle">(Commercial Credit – Exempt from NCA Section 4(1)(a)(i))</div>
            </td>
        </tr>
    </table>
</div>

<!-- Borrower Information -->
<div class="section-title">Borrower Information</div>

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
        <td>{{address}},<br />{{city}},<br />{{state}},<br />{{zip}},<br />{{country}}</td>
    </tr>
</table>

<div class="divider"></div>

<!-- 1. Nature and Purpose of the Credit -->
<div class="section-title">1. Nature and Purpose of the Credit</div>

<p>
    This loan is provided strictly for <strong>business, commercial, or investment purposes</strong>. It is not intended for personal, household, or domestic use.
</p>

<p>
    By signing this Agreement, the Borrower expressly confirms that the credit is being obtained for a business-related purpose and therefore falls outside the scope of the National Credit Act 34 of 2005, in accordance with Section 4(1)(a)(i).
</p>

<p>
    The Borrower acknowledges and accepts that this is a commercial transaction, and the protections applicable to consumer credit agreements do not apply.
</p>

<div class="divider"></div>

<!-- 2. Loan Product Summary -->
<div class="section-title">2. Loan Product Summary</div>

<div class="bullet-item"><strong>• Loan Product / Plan:</strong> {{plan_name}}</div>
<div class="bullet-item"><strong>• Requested Loan Amount:</strong> R{{amount}}</div>
<div class="bullet-item"><strong>• Total Instalments:</strong> {{total_installment}}</div>
<div class="bullet-item"><strong>• Repayment Frequency:</strong> Every {{installment_interval}} days</div>
<div class="bullet-item"><strong>• Instalment Amount:</strong> R{{per_installment}}</div>
<div class="bullet-item"><strong>• Profit / Interest Percentage:</strong> {{profit_percentage}}%</div>
<div class="bullet-item"><strong>• Application Fixed Charge:</strong> R{{application_fixed_charge}}</div>

<p style="margin-top: 10px;">A complete repayment schedule will be generated upon loan approval.</p>

<div class="divider"></div>

<!-- 3. Bank Details for Disbursement -->
<div class="section-title">3. Bank Details for Disbursement</div>

<p>If approved, funds will be disbursed to:</p>

<div class="bullet-item"><strong>• Bank:</strong> {{bank_name}}</div>
<div class="bullet-item"><strong>• Account Number:</strong> {{bank_account}}</div>
<div class="bullet-item"><strong>• Branch Code:</strong> {{branch_code}}</div>

<p style="margin-top: 10px;">The Borrower confirms these details are correct.</p>

<div class="divider"></div>

<!-- 4. Total Cost of Credit -->
<div class="section-title">4. Total Cost of Credit</div>

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

<!-- 5. Late Payment Fees -->
<div class="section-title">5. Late Payment Fees</div>

<p>If payment is delayed by more than <strong>{{delay}}</strong> days the following charges will apply:</p>

<div class="bullet-item"><strong>• Fixed Late Charge:</strong> {{fixed_charge}} {{site_currency}}</div>
<div class="bullet-item"><strong>• Percentage Charge:</strong> {{percent_charge}}%</div>
<div class="bullet-item"><strong>• Interval:</strong> Applied each time an instalment becomes overdue</div>

<div class="divider"></div>

<!-- 6. Borrower Acknowledgement -->
<div class="section-title">6. Borrower Acknowledgement</div>

<p>By receiving this Pre-Agreement Statement, the Borrower confirms:</p>

<ul>
    <li>They understand all proposed costs and repayment obligations.</li>
    <li>They acknowledge this is a <strong>business/commercial</strong> loan.</li>
    <li>They have not been coerced or misled.</li>
    <li>They understand that signing the Loan Agreement will create a binding contract.</li>
    <li>They received this Pre-Agreement Statement <strong>before</strong> signing the Loan Agreement.</li>
</ul>

<div class="divider"></div>

<div class="footer">
    <strong>Pienaar Group</strong><br />
    Loans@PienaarGroupExecutive.com
</div>

</body>
</html>
