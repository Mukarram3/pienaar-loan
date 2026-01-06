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

        /* Subsection Headers */
        h4 {
            font-size: 10pt;
            font-weight: bold;
            margin: 12px 0 8px 0;
            padding: 0;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 20px;
        }

        table.signature-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            margin-top: 15px;
        }

        table.signature-table td {
            border: 2px solid #000;
            padding: 12px;
            vertical-align: top;
        }

        table.signature-table .header-cell {
            background-color: #FFD700;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            font-size: 10pt;
            color: #000;
        }

        .signature-content {
            min-height: 140px;
        }

        .signature-header {
            font-weight: bold;
            text-align: center;
        }

        .signature-images {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-images td {
            border: none;
            vertical-align: middle;
            text-align: center;
        }

        /* Page Break */
        .page-break {
            page-break-after: always;
        }

        /* Page Number */
        .page-number {
            text-align: center;
            font-size: 9pt;
            color: #666;
            margin-top: 15px;
        }

        /* Utilities */
        .text-center {
            text-align: center;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .mb-10 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<!-- PAGE 1 -->
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

<div class="divider" style="margin-top: 12px;"></div>

<!-- 1. THE PARTIES -->
<div class="section-title">1. THE PARTIES</div>

<p><strong>Lender:</strong></p>

<p style="margin: 10px 0 5px 25px;">
    <strong>Pienaar Limited T/A Pienaar Group</strong><br />
    14–16 Averof Road,<br />
    Kamma Heights,<br />
    Port Elizabeth,<br />
    6070,<br />
    South Africa
</p>

<p style="margin: 10px 0 5px 25px;">
    Email: Loans@PienaarGroupExecutive.com
</p>

<p style="margin: 10px 0 5px 25px;">
    <strong>Represented by:</strong><br />
    <strong>Senior Advocate Sayed Abedin (BA, MBA, LLB, LLM)</strong>
</p>

<div class="divider"></div>

<p><strong>Borrower:</strong></p>

<table class="info-table">
    <tr>
        <td class="label">Full Name:</td>
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
        <td class="label">Residential Address:</td>
        <td>{{address}},<br />{{city}},<br />{{state}},<br />{{zip}},<br />{{country}}</td>
    </tr>
    <tr>
        <td class="label">Loan ID:</td>
        <td>{{loan_number}}</td>
    </tr>
</table>

<div class="divider"></div>

<!-- 2. BUSINESS PURPOSE DECLARATION -->
<div class="section-title">2. BUSINESS PURPOSE DECLARATION (NCA EXEMPTION)</div>

<p>
    The Borrower confirms that this loan is being obtained for <strong>business, commercial, or investment purposes</strong>,
    and <strong>not</strong> for personal, household, or domestic consumption.
</p>

<p>
    Accordingly, this Agreement is <strong>exempt from the National Credit Act 34 of 2005</strong> in terms of
    <strong>Section 4(1)(a)(i)</strong>.
</p>

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

<p>
    The Borrower acknowledges full understanding of the commercial nature and associated risks of this Agreement.
</p>

<div class="divider"></div>

<!-- 3. LOAN TERMS -->
<div class="section-title">3. LOAN TERMS</div>

<div class="bullet-item"><strong>• Loan Amount:</strong> R{{amount}}</div>
<div class="bullet-item"><strong>• Loan Product / Plan:</strong> {{plan_name}}</div>
<div class="bullet-item"><strong>• Total Instalments:</strong> {{total_installment}}</div>
<div class="bullet-item"><strong>• Repayment Interval:</strong> Every {{installment_interval}} Days</div>
<div class="bullet-item"><strong>• Instalment Amount:</strong> R{{per_installment}}</div>
<div class="bullet-item"><strong>• Percentage Charge:</strong> {{profit_percentage}}%</div>
<div class="bullet-item"><strong>• Application Fixed Charge:</strong> R{{application_percent_charge}}</div>

<p style="margin-top: 10px;">A detailed payment schedule is available in the Borrower's PienaarBank online portal.</p>

<div class="divider"></div>

<!-- 4. DISBURSEMENT OF FUNDS -->
<div class="section-title">4. DISBURSEMENT OF FUNDS</div>

<p>Loan proceeds will be disbursed to the Borrower's nominated bank account:</p>

<table class="info-table">
    <tr>
        <td class="label">Bank:</td>
        <td></td>
    </tr>
    <tr>
        <td class="label">Account Number:</td>
        <td></td>
    </tr>
    <tr>
        <td class="label">Branch Code:</td>
        <td></td>
    </tr>
</table>

<p>
    The Borrower confirms that these details are correct and indemnifies the Lender against all losses or
    delays caused by incorrect banking information.
</p>

<div class="page-break"></div>

<!-- PAGE 3 -->
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

<!-- 5. REPAYMENT TERMS -->
<div class="section-title">5. REPAYMENT TERMS</div>

<p><strong>5.1</strong> Instalments are payable every <strong>{{installment_interval}}</strong> days for the duration of the loan term.</p>

<p><strong>5.2</strong> All payments must be made on or before the due date reflected in the Borrower's repayment schedule.</p>

<p><strong>5.3</strong> Payments must reference:</p>
<p style="margin-left: 15px;"><strong>{{first_name}} {{last_name}} / {{loan_number}}</strong></p>

<p><strong>5.4</strong> Early settlement is allowed at any time without penalty.</p>

<p><strong>5.5</strong> Instalments consist of capital, profit, and charges in accordance with the loan plan settings.</p>

<div class="divider"></div>

<!-- 6. LATE PAYMENT, PENALTIES & DEFAULT -->
<div class="section-title">6. LATE PAYMENT, PENALTIES & DEFAULT</div>

<h4>6.1 Late Payment Charges (Dynamic Fields)</h4>

<p>If an instalment is delayed by more than <strong>{{delay}}</strong> days the following penalties apply:</p>

<div class="bullet-item"><strong>• Fixed Late Charge:</strong> {{fixed_charge}} {{site_currency}}</div>
<div class="bullet-item"><strong>• Percentage Charge:</strong> {{percent_charge}}%</div>
<div class="bullet-item"><strong>• Interval:</strong> Applied each time an instalment becomes overdue</div>

<p style="margin-top: 10px;">These fees are automatically applied by the system.</p>

<div class="divider"></div>

<h4>6.2 Default Events</h4>

<p>The Borrower will be in default if they:</p>

<ul>
    <li>Fail to pay any instalment within 7 days of its due date</li>
    <li>Provide false, misleading, or fraudulent information</li>
    <li>Breach any term of this Agreement</li>
    <li>Commit an act of insolvency</li>
    <li>Become subject to liquidation or debt review proceedings</li>
</ul>

<!-- PAGE 4 -->
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

<h4>6.3 Consequences of Default</h4>

<p>If the Borrower defaults, the Lender may:</p>

<ul>
    <li>Demand <strong>immediate full settlement</strong> of the outstanding balance (acceleration clause)</li>
    <li>Suspend access to PienaarBank financial services</li>
    <li>Engage attorneys to collect outstanding amounts (attorney-and-client scale)</li>
    <li>Report the Borrower to relevant credit bureaux</li>
    <li>Take civil enforcement action</li>
</ul>

<p>
    All additional legal, tracing, and administrative fees will be added to the Borrower's account.
</p>

<div class="divider"></div>

<!-- 7. POPIA COMPLIANCE -->
<div class="section-title">7. POPIA COMPLIANCE</div>

<p>
    The Borrower consents to the Lender verifying, storing, and lawfully processing personal and business
    information for:
</p>

<ul>
    <li>Identity verification</li>
    <li>Fraud prevention</li>
    <li>KYC compliance</li>
    <li>Credit assessment</li>
    <li>Loan administration</li>
</ul>

<p>
    All processing complies with the <strong>Protection of Personal Information Act 4 of 2013 (POPIA)</strong>.
</p>

<div class="divider"></div>

<!-- 8. DIGITAL SIGNATURE & ECTA COMPLIANCE -->
<div class="section-title">8. DIGITAL SIGNATURE & ECTA COMPLIANCE</div>

<p>This Agreement may be signed electronically.</p>

<p>Under the <strong>Electronic Communications and Transactions Act 25 of 2002 (ECTA)</strong>:</p>

<ul>
    <li>Electronic signatures have full legal force</li>
    <li>A digitally signed agreement is binding</li>
    <li>The Borrower acknowledges receipt of the Pre-Agreement Statement prior to signing</li>
</ul>

<!-- PAGE BREAK -->
<div class="page-break"></div>

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

<p>The Borrower accepts all terms upon digital signature.</p>

<div class="divider"></div>

<!-- 9. GOVERNING LAW -->
<div class="section-title">9. GOVERNING LAW</div>

<p>
    This Agreement is governed by the laws of the Republic of South Africa.<br />
    Parties consent to the jurisdiction of the <strong>High Court of South Africa, Eastern Cape Division</strong>.
</p>

<div class="divider"></div>

<!-- 10. ENTIRE AGREEMENT -->
<div class="section-title">10. ENTIRE AGREEMENT</div>

<p>
    This Agreement constitutes the full and final contract between the Parties.
</p>

<p>
    No amendment will be valid unless reduced to writing and signed by both Parties.
</p>

<div class="divider"></div>

<!-- SIGNATURES -->
<div class="signature-section">
    <div class="section-title text-center">SIGNATURES</div>

    <table class="signature-table">
        <tr>
            <td class="signature-header" width="50%">
                FOR AND ON BEHALF OF PIENAAR GROUP
            </td>
            <td class="signature-header" width="50%">
                FOR AND ON BEHALF OF BORROWER
            </td>
        </tr>
        <tr>
            <td style="padding: 20px !important">
                <table class="signature-images">
                    <tr>
                        <td width="50%">
                            <img src="{{lender_signature}}" style="max-width:160px;">
                        </td>
                        <td width="50%">
                            <img src="{{company_seal}}" style="max-width:140px;">
                        </td>
                    </tr>
                </table>

                <p style="margin-top:20px;">
                    Senior Advocate Sayed Abedin (BA MBA LLB LLM)<br>
                    For and on behalf of PIENAAR GROUP
                </p>
            </td>
            <td style="padding: 20px !important">
                <p>Name: {{first_name}} {{last_name}}</p>
                <p>Signature: </p>
                <p>Date: </p>
            </td>
        </tr>
    </table>
</div>

<div class="page-number">5</div>

</body>
</html>