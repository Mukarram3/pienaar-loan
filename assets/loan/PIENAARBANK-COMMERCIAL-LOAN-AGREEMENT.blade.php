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
            font-size: 12pt;
            color: #444;
            text-align: right;
        }

        .document-subtitle {
            font-size: 9pt;
            color: #777;
            text-align: right;
        }

        .section-title {
            font-size: 14pt;
            font-weight: bold;
            padding: 8px 0;
            border-bottom: 1px solid #ccc;
            margin-bottom: 10px;
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

<!-- HEADER -->
<div class="header">
    <table class="header-table">
        <tr>
            <td width="30%">
                <img src="{{logo}}" class="header-logo">
            </td>
            <td width="70%" style="text-align:right;">
                <div class="company-name">PIENAAR GROUP LIMITED</div>
                <div class="document-title">PIENAARBANK COMMERCIAL LOAN AGREEMENT</div>
                <div class="document-subtitle">(Exempt from NCA – Section 4(1)(a)(i))</div>
            </td>
        </tr>
    </table>
</div>

<!-- 1. PARTIES -->
<div class="section-title">1. THE PARTIES</div>

<p><strong>Lender:</strong><br>
    Pienaar Group Limited t/a PienaarBank<br>
    14–16 Averof Road,<br>
    Kamma Heights,<br>
    Port Elizabeth, 6070<br>
    South Africa<br><br>
    Email: loans@pienaarbank.com<br><br>

    Represented by:<br>
    <strong>Senior Advocate Sayed Abedin (BA, MBA, LLB, LLM)</strong>
</p>

<p><strong>Borrower:</strong></p>

<div class="info-line"><span class="label">Full Name:</span> {{first_name}} {{last_name}}</div>
<div class="info-line"><span class="label">Email:</span> {{email}}</div>
<div class="info-line"><span class="label">Mobile Number:</span> {{mobile}}</div>
<div class="info-line"><span class="label">Residential Address:</span> {{address}}, {{city}}, {{state}}, {{zip}}, {{country}}</div>
<div class="info-line"><span class="label">Loan ID:</span> {{loan_number}}</div>

<div class="section-title">2. BUSINESS PURPOSE DECLARATION</div>

<p>
    The Borrower confirms that this loan is being obtained for <strong>business, commercial, or investment purposes</strong>,
    and not for personal, household, or domestic use.
</p>

<p>
    This Agreement is therefore <strong>exempt from the National Credit Act 34 of 2005</strong>,
    in terms of <strong>Section 4(1)(a)(i)</strong>.
</p>

<p>
    The Borrower acknowledges the commercial nature and associated risks of this Agreement.
</p>

<div class="section-title">3. LOAN TERMS</div>

<div class="info-line"><span class="label">Loan Amount:</span> R{{amount}}</div>
<div class="info-line"><span class="label">Loan Plan:</span> {{plan_name}}</div>
<div class="info-line"><span class="label">Total Instalments:</span> {{total_installment}}</div>
<div class="info-line"><span class="label">Repayment Interval:</span> Every {{installment_interval}} Days</div>
<div class="info-line"><span class="label">Instalment Amount:</span> {{per_installment}}</div>
<div class="info-line"><span class="label">Profit Percentage:</span> {{profit_percentage}}%</div>
<div class="info-line"><span class="label">Application Fixed Charge:</span> {{application_fixed_charge}}</div>
<div class="info-line"><span class="label">Application % Charge:</span> {{application_percent_charge}}%</div>

<p>A full repayment schedule is available in the Borrower's online portal.</p>

<div class="section-title">4. DISBURSEMENT OF FUNDS</div>

<div class="info-line"><span class="label">Bank:</span> {{bank_name}}</div>
<div class="info-line"><span class="label">Account Number:</span> {{bank_account}}</div>
<div class="info-line"><span class="label">Branch Code:</span> {{branch_code}}</div>

<p>The Borrower confirms the banking details are correct.</p>

<div class="section-title">5. REPAYMENT TERMS</div>

<ul>
    <li>Instalments must be paid every {{installment_interval}} days.</li>
    <li>Payments must be made on or before the due date.</li>
    <li>Reference: {{first_name}} {{last_name}} / {{loan_number}}</li>
    <li>Early settlement is allowed without penalty.</li>
</ul>

<div class="section-title">6. LATE PAYMENT, PENALTIES & DEFAULT</div>

<p>If an instalment is delayed by more than <strong>{{delay}}</strong> days:</p>

<div class="info-line"><span class="label">Fixed Late Fee:</span> {{fixed_charge}}</div>
<div class="info-line"><span class="label">Percentage Charge:</span> {{percent_charge}}%</div>
<div class="info-line"><span class="label">Interval:</span> Per overdue instalment</div>

<h4>Default Events:</h4>

<ul>
    <li>Failure to pay within 7 days of due date</li>
    <li>Providing false or fraudulent information</li>
    <li>Breaching this Agreement</li>
    <li>Insolvency or liquidation</li>
</ul>

<h4>Consequences of Default:</h4>

<ul>
    <li>Immediate full settlement may be demanded</li>
    <li>Suspension of services</li>
    <li>Attorney-and-client scale legal fees apply</li>
    <li>Credit bureau reporting</li>
    <li>Civil recovery action</li>
</ul>

<div class="section-title">7. POPIA COMPLIANCE</div>

<p>
    The Borrower consents to lawful processing of personal and business information for:
    identity verification, fraud prevention, KYC, credit assessment, and loan administration.
</p>

<div class="section-title">8. DIGITAL SIGNATURE & ECTA</div>

<p>
    This Agreement may be signed electronically and is fully enforceable under the Electronic Communications and Transactions Act 25 of 2002.
</p>

<div class="section-title">9. GOVERNING LAW</div>

<p>Governed by the laws of South Africa. Jurisdiction: High Court (Eastern Cape Division).</p>

<div class="section-title">10. ENTIRE AGREEMENT</div>

<p>
    This Agreement constitutes the entire binding contract. Amendments must be signed in writing by both Parties.
</p>

<div class="section-title">SIGNATURES</div>

<p><strong>FOR PIENAAR GROUP LIMITED</strong><br><br><br>
    Senior Advocate Sayed Abedin (BA MBA LLB LLM)</p>

<br><br>

<p><strong>FOR BORROWER</strong></p>

<div class="info-line"><span class="label">Name:</span> </div>
<div class="info-line"><span class="label">ID/Company No.:</span> </div>
<div class="info-line"><span class="label">Signature:</span> </div>
<div class="info-line"><span class="label">Date:</span></div>

</body>
</html>
