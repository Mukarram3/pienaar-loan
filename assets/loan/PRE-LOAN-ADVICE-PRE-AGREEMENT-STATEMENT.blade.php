<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIENAAR GROUP PRE-LOAN ADVICE / PRE-AGREEMENT STATEMENT</title>
    <style>
        @page {
            margin: 20mm 15mm;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 0;
        }

        /* Header Section */
        .header {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
            display: table;
        }

        .header-left {
            display: table-cell;
            width: 25%;
            vertical-align: middle;
        }

        .header-logo {
            width: 110px;
            height: auto;
        }

        .header-right {
            display: table-cell;
            width: 75%;
            text-align: right;
            vertical-align: middle;
        }

        .company-name {
            font-size: 28pt;
            font-weight: bold;
            color: #1a1a1a;
            letter-spacing: 2px;
            margin: 0;
            line-height: 1.2;
        }

        .document-title {
            font-size: 11pt;
            color: #333;
            margin: 5px 0;
            font-weight: 600;
        }

        .document-subtitle {
            font-size: 9pt;
            color: #666;
            font-style: italic;
            margin: 0;
        }

        .divider {
            width: 100%;
            height: 2px;
            background: repeating-linear-gradient(
                    to right,
                    #000 0,
                    #000 5px,
                    transparent 5px,
                    transparent 10px
            );
            margin: 25px 0;
        }

        /* Section Titles */
        .section-title {
            font-size: 13pt;
            font-weight: bold;
            color: #000;
            padding: 10px 0 5px 0;
            margin: 20px 0 15px 0;
        }

        /* Content Paragraphs */
        p {
            margin: 0 0 12px 0;
            text-align: justify;
        }

        /* Info Block */
        .info-block {
            margin: 15px 0;
        }

        .info-line {
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .label {
            font-weight: bold;
            display: inline-block;
            width: 220px;
            color: #000;
        }

        .value {
            display: inline;
            color: #333;
        }

        /* Lists */
        ul {
            margin: 10px 0 15px 0;
            padding-left: 0;
            list-style: none;
        }

        ul li {
            padding-left: 25px;
            margin-bottom: 8px;
            position: relative;
            line-height: 1.5;
        }

        ul li:before {
            content: "•";
            position: absolute;
            left: 10px;
            font-weight: bold;
            font-size: 14pt;
        }

        /* Emphasis */
        strong {
            font-weight: bold;
            color: #000;
        }

        /* Page Break */
        .page-break {
            page-break-after: always;
        }

        /* Page Number */
        .page-number {
            text-align: center;
            color: #777;
            font-size: 9pt;
            margin-top: 30px;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            font-size: 10pt;
            color: #555;
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }
    </style>
</head>
<body>
<div class="container">

    <!-- ====================================== PAGE 1 ====================================== -->

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <img src="{{logo}}" class="header-logo" alt="PIENAAR GROUP Logo">
        </div>
        <div class="header-right">
            <div class="company-name">PIENAAR GROUP</div>
            <div class="document-title">📄 PIENAAR GOUP PRE-LOAN ADVICE / PRE-AGREEMENT STATEMENT</div>
            <div class="document-subtitle">(Commercial Credit – Exempt from NCA Section 4(1)(a)(i))</div>
        </div>
    </div>

    <div class="divider"></div>

    <!-- BORROWER INFORMATION -->
    <div class="section-title">Borrower Information</div>

    <div class="info-block">
        <div class="info-line">
            <span class="label">Name:</span>
            <span class="value">{{first_name}} {{last_name}}</span>
        </div>
        <div class="info-line">
            <span class="label">Email:</span>
            <span class="value">{{email}}</span>
        </div>
        <div class="info-line">
            <span class="label">Mobile Number:</span>
            <span class="value">{{mobile}}</span>
        </div>
        <div class="info-line">
            <span class="label">Address:</span>
            <span class="value">{{address}}, {{city}}, {{state}}, {{zip}}, {{country}}</span>
        </div>
    </div>

    <div class="divider"></div>

    <!-- 1. NATURE AND PURPOSE OF THE CREDIT -->
    <div class="section-title">1. Nature and Purpose of the Credit</div>

    <p>
        This loan is provided strictly for <strong>business, commercial, or investment purposes</strong>.
        It is not intended for personal, household, or domestic use.
    </p>

    <p>
        By signing this Agreement, the Borrower expressly confirms that the credit is being obtained for a
        business-related purpose and therefore falls outside the scope of the <strong>National Credit Act 34 of 2005</strong>,
        in accordance with <strong>Section 4(1)(a)(i)</strong>.
    </p>

    <p>
        The Borrower acknowledges and accepts that this is a commercial transaction, and the protections
        applicable to consumer credit agreements do not apply.
    </p>

    <!-- 2. LOAN PRODUCT SUMMARY -->
    <div class="section-title">2. Loan Product Summary</div>

    <ul>
        <li><strong>Loan Product / Plan:</strong> {{plan_name}}</li>
        <li><strong>Requested Loan Amount:</strong> R{{amount}}</li>
        <li><strong>Total Instalments:</strong> {{total_installment}}</li>
        <li><strong>Repayment Frequency:</strong> Every {{installment_interval}} days</li>
        <li><strong>Instalment Amount:</strong> R{{per_installment}}</li>
        <li><strong>Profit / Interest Percentage:</strong> {{profit_percentage}}%</li>
        <li><strong>Application Fixed Charge:</strong> R{{application_fixed_charge}}</li>
    </ul>

    <p>A complete repayment schedule will be generated upon approval.</p>

    <div class="page-break"></div>

    <!-- ====================================== PAGE 2 ====================================== -->

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <img src="{{logo}}" class="header-logo" alt="PIENAAR GROUP Logo">
        </div>
        <div class="header-right">
            <div class="company-name">PIENAAR GROUP</div>
            <div class="document-title">📄 PIENAAR GOUP PRE-LOAN ADVICE / PRE-AGREEMENT STATEMENT</div>
            <div class="document-subtitle">(Commercial Credit – Exempt from NCA Section 4(1)(a)(i))</div>
        </div>
    </div>

    <div class="divider"></div>

    <!-- 3. BANK DETAILS FOR DISBURSEMENT -->
    <div class="section-title">3. Bank Details for Disbursement</div>

    <p>If approved, funds will be disbursed to:</p>

    <ul>
        <li><strong>Bank:</strong> {{bank_name}}</li>
        <li><strong>Account Number:</strong> {{bank_account}}</li>
        <li><strong>Branch Code:</strong> {{branch_code}}</li>
    </ul>

    <p>The Borrower confirms these details are correct.</p>

    <!-- 4. TOTAL COST OF CREDIT -->
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

    <!-- 5. LATE PAYMENT FEES -->
    <div class="section-title">5. Late Payment Fees</div>

    <p>If payment is delayed by more than <strong>{{delay}}</strong> days the following charges will apply:</p>

    <ul>
        <li><strong>Fixed Late Charge:</strong> {{fixed_charge}} {{site_currency}}</li>
        <li><strong>Percentage Charge:</strong> {{percent_charge}}%</li>
        <li><strong>Interval:</strong> Applied each time an instalment becomes overdue</li>
    </ul>

    <div class="page-break"></div>

    <!-- ====================================== PAGE 3 ====================================== -->

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <img src="{{logo}}" class="header-logo" alt="PIENAAR GROUP Logo">
        </div>
        <div class="header-right">
            <div class="company-name">PIENAAR GROUP</div>
            <div class="document-title">📄 PIENAAR GOUP PRE-LOAN ADVICE / PRE-AGREEMENT STATEMENT</div>
            <div class="document-subtitle">(Commercial Credit – Exempt from NCA Section 4(1)(a)(i))</div>
        </div>
    </div>

    <div class="divider"></div>

    <!-- 6. BORROWER ACKNOWLEDGEMENT -->
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

    <!-- FOOTER -->
    <div class="footer">
        <p style="margin: 0;">
            <strong>Pienaar Group</strong><br>
            Loans@PienaarGroupExecutive.com
        </p>
        <p style="margin-top: 15px; font-size: 9pt; color: #999;">
            14-16 Averof Road, Kamma Heights, Port Elizabeth, 6070, South Africa<br>
            This Pre-Agreement Statement must be provided to the Borrower before the Loan Agreement is signed.
        </p>
    </div>

</div>
</body>
</html>