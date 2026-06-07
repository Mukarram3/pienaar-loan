<?php

namespace App\Constants;

class Status{

    const ENABLE = 1;
    const DISABLE = 0;

    const YES = 1;
    const NO = 0;

    const VERIFIED = 1;
    const UNVERIFIED = 0;

    const PAYMENT_INITIATE = 0;
    const PAYMENT_SUCCESS = 1;
    const PAYMENT_PENDING = 2;
    const PAYMENT_REJECT = 3;

    CONST TICKET_OPEN = 0;
    CONST TICKET_ANSWER = 1;
    CONST TICKET_REPLY = 2;
    CONST TICKET_CLOSE = 3;

    CONST PRIORITY_LOW = 1;
    CONST PRIORITY_MEDIUM = 2;
    CONST PRIORITY_HIGH = 3;

    const USER_ACTIVE = 1;
    const USER_BAN = 0;

    const KYC_UNVERIFIED = 0;
    const KYC_PENDING = 2;
    const KYC_VERIFIED = 1;

    const GOOGLE_PAY = 5001;

    const CUR_BOTH = 1;
    const CUR_TEXT = 2;
    const CUR_SYM = 3;

    const LOAN_PENDING  = 0;
    const LOAN_RUNNING  = 1;
    const LOAN_PAID     = 2;
    const LOAN_REJECTED = 3;
    const LOAN_IN_REVIEW = 4;
    const LOAN_APPROVED = 5;

    const DEPOSIT_SUCCESS = 1;
    const DEPOSIT_PENDING = 2;
    const DEPOSIT_CANCEL  = 3;

    const WITHDRAW_SUCCESS = 1;
    const WITHDRAW_PENDING = 2;
    const WITHDRAW_CANCEL  = 3;

    // Loan Lifecycle Stages
    const LIFECYCLE_ACTIVE              = 1;
    const LIFECYCLE_REDEMPTION_OFFERED  = 2;
    const LIFECYCLE_REDEMPTION_ACCEPTED = 3;
    const LIFECYCLE_SETTLED             = 4;
    const LIFECYCLE_CLOSED              = 5;
    const LIFECYCLE_SECURITY_RELEASED   = 6;

// Arrears State
    const ARREARS_ACTIVE            = 1;
    const ARREARS_IN_ARREARS        = 2;
    const ARREARS_DEFAULTED         = 3;
    const ARREARS_LEGAL_COLLECTIONS = 4;

// Redemption Quote statuses
    const QUOTE_ACTIVE   = 1;
    const QUOTE_EXPIRED  = 2;
    const QUOTE_ACCEPTED = 3;
    const QUOTE_REJECTED = 4;
    const QUOTE_SETTLED  = 5;
    const QUOTE_VOID     = 6;

// Settlement Payment statuses
    const PAYMENT_FULL  = 1;
    const PAYMENT_SHORT = 2;
    const PAYMENT_REJECTED_INSUFFICIENT = 3;

}
