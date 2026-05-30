@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="mb-4">
            <p>@lang('Loan')</p>
            <div class="d-flex justify-content-between">
                <h3>{{ __($pageTitle) }}</h3>
                {{-- REPORTS & DOCUMENTS DROPDOWN --}}
                <div class="dropdown d-inline-block bg-white">
                    <button class="btn btn-outline--primary dropdown-toggle" type="button"
                            id="reportsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-alt"></i> @lang('Reports & Documents')
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="reportsDropdown">
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('admin.loan.statement.pdf', $loan->id) }}" target="_blank">
                                <i class="fas fa-file-pdf text--primary"></i> @lang('Statement of Loan Account')
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('admin.loan.redemption.quote', $loan->id) }}" target="_blank">
                                <i class="fas fa-file-invoice-dollar text--success"></i> @lang('Early Redemption Quote')
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('admin.loan.payment.history.pdf', $loan->id) }}"
                               target="_blank">
                                <i class="fas fa-history text--info"></i> @lang('Payment History')
                            </a>
                        </li>
                        @if($loan->signed_agreement)
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('admin.loan.view.agreement', $loan->id) }}"
                                   target="_blank">
                                    <i class="fas fa-file-contract text--warning"></i> @lang('Loan Agreement')
                                </a>
                            </li>
                        @else
                            <li>
                        <span class="dropdown-item disabled text-muted">
                            <i class="fas fa-file-contract"></i> @lang('Loan Agreement') <small>(not uploaded)</small>
                        </span>
                            </li>
                        @endif
                    </ul>
                </div>
                <a href="{{ route('user.loan.list') }}" class="btn btn--base btn--sm"><i class="las la-list-alt"></i>
                    @lang('My Loan List')</a>
            </div>
            <p>@lang('Empowering dreams, one instalment at a time.')</p>
        </div>

        <div class="row gy-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            <div class="mb-2">
                                <h4 class="text--base value"><sup class="top-0 fw-light me-1">{{ $loan->loan_number }}
                                </h4>
                                <p class="fw-bold caption">@lang('Loan Number')</p>
                            </div>
                            <div class="mb-2">
                                <h4 class="text--base value"><sup class="top-0 fw-light me-1">{{ $loan->plan->name }}</h4>
                                <p class="fw-bold caption">@lang('Plan')</p>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            <div class="mb-2">
                                <h4 class="text--base value"><sup class="top-0 fw-light me-1">{{ showAmount($loan->amount) }}
                                </h4>
                                <p class="fw-bold caption">@lang('Loan Amount')</p>
                            </div>
                            <div class="mb-2">
                                <h4 class="text--base value"><sup class="top-0 fw-light me-1">{{ showAmount($loan->per_installment) }}
                                </h4>
                                <p class="fw-bold caption">@lang('Per Instalment')</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            <div class="mb-2">
                                <h4 class="text--base value"><sup class="top-0 fw-light me-1">{{ showAmount($loan->payable_amount) }}</h4>
                                <p class="fw-bold caption">@lang('Needs to Pay')</p>
                            </div>
                            <div class="mb-2">
                                <h4 class="text--base value"><sup class="top-0 fw-light me-1">{{ $loan->total_installment }}</h4>
                                <p class="fw-bold caption">@lang('Total No of Instalments')</p>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            @if (getAmount($loan->charge_per_installment))
                                <div class="mb-2">
                                    <h4 class="text--base value"><sup class="top-0 fw-light me-1">{{ showAmount($loan->charge_per_installment) }} / {{ $loan->delay_value }} @lang('Day')
                                    </h4>
                                    <p class="fw-bold caption">@lang('Delay Charge') <i class="las la-info-circle text--danger" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Charge will be applied if an installment delayed for') {{ $loan->delay_value }}
                                    @lang(' or more days')"></i>
                                    </p>

                                </div>
                            @endif
                            <div class="mb-2">
                                <h4 class="text--base value"><sup class="top-0 fw-light me-1">{{ $loan->given_installment }}
                                </h4>
                                <p class="fw-bold caption">@lang('No of Instalments Paid')</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            @include($activeTemplate . 'partials.installment_table')
        </div>
    </div>
@endsection
