@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="notice"></div>
        @if ($user->loans->where('status', Status::LOAN_RUNNING)->count() == 1)
            <div class="alert border border--warning" role="alert">
                <div class="alert__icon d-flex align-items-center text--success"><i class="fas fa-check"></i></div>
                <p class="alert__message">
                    <span class="fw-bold">@lang('First Loan')</span><br>
                    <small><i><span class="fw-bold">@lang('Congratulations!')</span> @lang('You\'ve made your first loan successfully. Go to') <a href="{{ route('user.loan.plans') }}" class="link-color">@lang('Loan List')</a>
                            @lang('for see your next installment date at near.')</i></small>
                </p>
            </div>
        @endif

        @if ($pendingWithdrawals)
            <div class="alert border border--primary" role="alert">
                <div class="alert__icon d-flex align-items-center text--primary"><i class="fas fa-spinner"></i></div>
                <p class="alert__message">
                    <span class="fw-bold">@lang('Withdrawal Pending')</span><br>
                    <small><i>@lang('Total') {{ showAmount($pendingWithdrawals) }}
                            @lang('withdrawal request is pending. Please wait for admin approval. The amount will send to the account which you\'ve provided. See') <a href="{{ route('user.withdraw.history') }}" class="link-color">@lang('withdrawal history')</a></i></small>
                </p>
            </div>
        @endif

        @if ($pendingDeposits)
            <div class="alert border border--primary" role="alert">
                <div class="alert__icon d-flex align-items-center text--primary"><i class="fas fa-spinner"></i></div>
                <p class="alert__message">
                    <span class="fw-bold">@lang('Deposit Pending')</span><br>
                    <small><i>@lang('Total') {{ showAmount($pendingDeposits) }}
                            @lang('deposit request is pending. Please wait for admin approval. See') <a href="{{ route('user.deposit.history') }}" class="link-color">@lang('deposit history')</a></i></small>
                </p>
            </div>
        @endif

        @if (!$user->ts)
            <div class="alert border border--warning" role="alert">
                <div class="alert__icon d-flex align-items-center text--warning"><i class="fas fa-user-lock"></i></div>
                <p class="alert__message">
                    <span class="fw-bold">@lang('2FA Authentication')</span><br>
                    <small><i>@lang('To keep safe your account, Please enable') <a href="{{ route('user.twofactor') }}" class="link-color">@lang('2FA')</a> @lang('security').</i> @lang('It will make secure your account and balance.')</small>
                </p>
            </div>
        @endif

        @php
            $kyc = getContent('kyc.content', true);
        @endphp

        @if (auth()->user()->kv == Status::KYC_UNVERIFIED && auth()->user()->kyc_rejection_reason)
            <div class="alert border border--danger" role="alert">
                <div class="alert__icon d-flex align-items-center text--danger"><i class="fas fa-file-signature"></i></div>
                <p class="alert__message">
                    <span class="fw-bold">@lang('KYC Documents Rejected')</span><br>
                    <small><i>{{ __(@$kyc->data_values->reject) }}
                            <a href="javascript::void(0)" class="link-color" data-bs-toggle="modal" data-bs-target="#kycRejectionReason">@lang('Click here')</a> @lang('to show the reason').

                            <a href="{{ route('user.kyc.form') }}" class="link-color">@lang('Click Here')</a> @lang('to Re-submit Documents'). <br>
                            <a href="{{ route('user.kyc.data') }}" class="link-color">@lang('See KYC Data')</a>
                        </i></small>
                </p>
            </div>
        @elseif ($user->kv == Status::KYC_UNVERIFIED)
            <div class="alert border border--info" role="alert">
                <div class="alert__icon d-flex align-items-center text--info"><i class="fas fa-file-signature"></i></div>
                <p class="alert__message">
                    <span class="fw-bold">@lang('KYC Verification Required')</span><br>
                    <small><i>{{ __(@$kyc->data_values->required) }} <a href="{{ route('user.kyc.form') }}" class="link-color">@lang('Click here')</a> @lang('to submit KYC information').</i></small>
                </p>
            </div>
        @elseif($user->kv == Status::KYC_PENDING)
            <div class="alert border border--warning" role="alert">
                <div class="alert__icon d-flex align-items-center text--warning"><i class="fas fa-user-check"></i></div>
                <p class="alert__message">
                    <span class="fw-bold">@lang('KYC Verification Pending')</span><br>
                    <small><i>{{ __(@$kyc->data_values->pending) }} <a href="{{ route('user.kyc.data') }}" class="link-color">@lang('Click here')</a> @lang('to see your submitted information')</i></small>
                </p>
            </div>
        @endif

        <div class="row g-3 mt-4">
            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Successful Deposits')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ showAmount($successfulDeposits) }}</h3>
                    <div class="widget-lists">
                        <div class="row">
                            <div class="col-4">
                                <p class="fw-bold">@lang('Submitted')</p>
                                <small>{{ showAmount($submittedDeposits) }}</small>
                            </div>
                            <div class="col-4">
                                <p class="fw-bold">@lang('Pending')</p>
                                <small>{{ showAmount($pendingDeposits) }}</small>
                            </div>
                            <div class="col-4">
                                <p class="fw-bold">@lang('Rejected')</p>
                                <small>{{ showAmount($rejectedDeposits) }}</small>
                            </div>
                        </div>
                        <hr>
                        <p><small><i>@lang('You\'ve requested to deposit') {{ showAmount($requestedDeposits) }}.
                                    @lang('Where') {{ showAmount($initiatedDeposits) }}
                                    @lang('is just initiated but not submitted.')</i></small></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Successful Withdrawals')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ showAmount($successfulWithdrawals) }}
                    </h3>
                    <div class="widget-lists">
                        <div class="row">
                            <div class="col-4">
                                <p class="fw-bold">@lang('Submitted')</p>
                                <small>{{ showAmount($submittedWithdrawals) }}</small>
                            </div>
                            <div class="col-4">
                                <p class="fw-bold">@lang('Pending')</p>
                                <small>{{ showAmount($pendingWithdrawals) }}</small>
                            </div>
                            <div class="col-4">
                                <p class="fw-bold">@lang('Rejected')</p>
                                <small>{{ showAmount($rejectedWithdrawals) }}</small>
                            </div>
                        </div>
                        <hr>
                        <p><small><i>@lang('You\'ve requested to withdraw') {{ showAmount($requestedWithdrawals) }}.
                                    @lang('Where') {{ showAmount($initiatedWithdrawals) }}
                                    @lang('is just initiated but not submitted.')</i></small></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Total Loan')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ showAmount($totalLoan) }}</h3>
                    <div class="widget-lists">
                        <div class="row">
                            <div class="col-3">
                                <p class="fw-bold">@lang('Pending')</p>
                                <small>{{ showAmount($pendingLoans) }}</small>
                            </div>
                            <div class="col-3">
                                <p class="fw-bold">@lang('Running')</p>
                                <small>{{ showAmount($runningLoans) }}</small>
                            </div>

                            <div class="col-3">
                                <p class="fw-bold">@lang('Completed')</p>
                                <small>{{ showAmount($paidLoans) }}</small>
                            </div>
                            <div class="col-3">
                                <p class="fw-bold">@lang('Rejected')</p>
                                <small>{{ showAmount($rejectedLoans) }}</small>
                            </div>
                        </div>

                        <hr>
                        <p><small><i>@lang('You\'ve') {{ getAmount($totalLoans) }} @lang('Loans').
                                    @lang('Which is') {{ getAmount($totalRunningLoans) }} @lang('Running') &
                                    {{ getAmount($totalPendingLoans) }}
                                    @lang('is Pending') & {{ getAmount($totalRejectedLoans) }} @lang('is Rejected') &
                                    {{ getAmount($totalPaidLoans) }} @lang('is Completed')</i></small>.</p>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="my-4"> @lang('My Running Loans')</h3>
        <div class="card custom--card ">
            <div class="table-responsive">
                <table class="table table--responsive--md">
                    <thead>
                        <tr>
                            <th>@lang('Loan Number')</th>
                            <th>@lang('Plan Name')</th>
                            <th>@lang('Loan Amount')</th>
                            <th>@lang('Installment')</th>
                            <th>@lang('Installment Amount')</th>
                            <th>@lang('Next Installment')</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($userRunningLoans as $loan)
                            <tr>
                                <td>
                                    <span class="text--primary"> {{ __($loan->loan_number) }}</span>
                                </td>
                                <td>
                                    {{ __($loan->plan->name) }}
                                </td>
                                <td>
                                    <p>
                                        <b>{{ showAmount($loan->amount) }}</b><br>
                                        <small class="text--base">
                                            {{ showAmount($loan->payable_amount) }}
                                            <small>(@lang('Need to pay'))</small>
                                        </small>
                                    </p>
                                </td>
                                <td>
                                    <span> @lang('Total') : {{ __($loan->total_installment) }}</span>
                                    <br>
                                    <small class="text--base">
                                        @lang('Given') : {{ __($loan->given_installment) }}
                                    </small>
                                </td>
                                <td>
                                    <span>{{ showAmount($loan->per_installment) }}</span>
                                    <br>
                                    <small class="text--base">
                                        @lang('In Every') {{ __($loan->installment_interval) }}
                                        @lang('Days')
                                    </small>
                                </td>
                                <td>
                                    @if ($loan->nextInstallment)
                                        <b> {{ showDateTime($loan->nextInstallment->installment_date, 'd M, Y') }}</b>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>
        @if ($totalRunningLoans > 5)
            <span class="d-flex justify-content-center">
                <a href="{{ route('user.loan.list') }}?status={{ Status::LOAN_RUNNING }}" class="btn btn--base my-2">
                    @lang('See All')</a>
            </span>
        @endif

    </div>

    {{-- @if (auth()->user()->kv == Status::KYC_UNVERIFIED && auth()->user()->kyc_rejection_reason) --}}
    <div class="modal fade" id="kycRejectionReason">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('KYC Document Rejection Reason')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ auth()->user()->kyc_rejection_reason }} Lorem ipsum, dolor sit amet consectetur adipisicing elit. Porro, ipsa?</p>
                </div>
            </div>
        </div>
    </div>
    {{-- @endif --}}
@endsection
