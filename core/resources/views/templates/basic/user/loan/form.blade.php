{{--
    =============================================================
    File: resources/views/templates/basic/user/loan/form.blade.php
    =============================================================
--}}
@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="row gy-4">
            @php
                $userBalance = auth()->user()->balance;
                $percentCharge = ($amount * $plan->application_percent_charge) / 100;
                $applicationFee = $plan->application_fixed_charge + $percentCharge;
            @endphp

            <div class="col-lg-5">
                <div class="card custom--card">
                    <div class="card-body">
                        @php
                            $allocator = app(\App\Services\Loan\LoanPaymentAllocator::class);
                        @endphp

                        <h5 class="text-center">
                            @lang('Before applying for your loan')
                        </h5>
                        <p class="text-center text--danger">(@lang('Please review your repayments'))</p>

                        @if($quote)
                            {{-- Authoritative figures from the calculation engine.
                                 This block does NOT calculate anything itself (spec s.20). --}}
                            <ul class="caption-list-two">
                                <li>
                                    <span class="caption">@lang('Plan Name')</span>
                                    <span class="value">{{ __($plan->name) }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Loan Amount')</span>
                                    <span class="value">{{ showAmount($quote->principal) }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Repayment Frequency')</span>
                                    <span class="value">@lang('Every') {{ $quote->installmentIntervalDays }} @lang('days')</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Number of Instalments')</span>
                                    <span class="value">{{ $quote->totalInstallments }}</span>
                                </li>
                            </ul>

                            <hr>

                            <ul class="caption-list-two">
                                <li class="fw-bold">
                                    <span class="caption">@lang('Instalment')</span>
                                    <span class="value text--danger fw-bold">
                                        {{ showAmount($quote->totalPerInstallment) }}
                                    </span>
                                </li>
                                <li>
                                    <span class="caption ps-3">@lang('Capital Component')</span>
                                    <span class="value">{{ showAmount($quote->capitalPerInstallment) }}</span>
                                </li>
                                <li>
                                    <span class="caption ps-3">@lang('Profit Component')</span>
                                    <span class="value">{{ showAmount($quote->profitPerInstallment) }}</span>
                                </li>
                            </ul>

                            <hr>

                            <ul class="caption-list-two">
                                <li>
                                    <span class="caption">@lang('Total Capital Repayable')</span>
                                    <span class="value">{{ showAmount($quote->totalCapitalRepayable) }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Total Profit over Full Term')</span>
                                    <span class="value">{{ showAmount($quote->totalProfitOverTerm) }}</span>
                                </li>
                                <li class="fw-bold text--danger">
                                    <span class="caption">@lang('Total Contractual Repayment')</span>
                                    <span class="value">
                                        {{ showAmount($quote->totalContractualRepayment) }}<br>
                                        <small class="fw-normal">
                                            ({{ showAmount($quote->totalPerInstallment) }}
                                            @lang('every') {{ $quote->installmentIntervalDays }} @lang('days'))
                                        </small>
                                    </span>
                                </li>
                            </ul>

                            <small class="d-block mt-3 text-muted">
                                @lang('Each payment received is allocated')
                                {{ $allocator->pct($quote->terms->capitalRatio) }}%
                                @lang('towards the outstanding Capital Sum and')
                                {{ $allocator->pct($quote->terms->profitRatio) }}%
                                @lang('towards profit due under the Loan.')
                            </small>
                        @else
                            @php
                                $total_amount_payable = ($amount * $plan->per_installment / 100 * $plan->total_installment) + $amount;
                            @endphp
                            <ul class="caption-list-two">
                                <li>
                                    <span class="caption">@lang('Plan Name')</span>
                                    <span class="value">{{ __($plan->name) }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Loan Amount')</span>
                                    <span class="value">{{ showAmount($amount) }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Total No of Instalments')</span>
                                    <span class="value">{{ $plan->total_installment }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Per Instalment')</span>
                                    <span class="value">{{ showAmount($total_amount_payable / $plan->total_installment) }}</span>
                                </li>
                                <li class="fw-bold text--danger">
                                    <span class="caption">@lang('You\'ll Need To Pay')</span>
                                    <span class="value">
                                        {{ showAmount($total_amount_payable) }} <br>
                                        ( {{ showAmount($total_amount_payable / $plan->total_installment) }}
                                        @lang('every') {{ $plan->installment_interval }} @lang('days') )
                                    </span>
                                </li>
                            </ul>
                        @endif

                        <p class="px-2">
                            @if ($plan->delay_value && getAmount($plan->delay_charge))
                                <small class="text--danger d-block mb-3 mt-2">*
                                    @lang('If an instalment is delayed for')
                                    <span
                                        class="fw-bold">{{ $plan->delay_value }}</span> @lang('or more days then, an amount of')
                                    <span class="fw-bold">{{ showAmount(($plan->percent_charge / 100) * $amount) }}</span>
                                    @lang('will be applied for each day.')
                                </small>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="card custom--card mt-3">
                    <div class="card-body">
                        <h4>@lang('Application Fee'): {{ showAmount($applicationFee) }}</h4>
                        <p class="mt-2">@lang('The application fee will be deducted from your balance now. You should have sufficient balance on your account for applying.')</p>
                    </div>
                </div>

            </div>
            <div class="col-lg-7">
                <div class="card custom--card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Application Form')</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('user.loan.apply.confirm') }}" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            @if ($plan->instruction)
                                <div class="form-group">
                                    <p class="caption-list-two p-3 bg--light">
                                        @php echo $plan->instruction @endphp
                                    </p>
                                </div>
                            @endif

                            <x-viser-form identifier="id" identifierValue="{{ $plan->form_id }}"/>

                            <button type="submit" class="btn btn--base w-100"
                                {{--                                            @disabled($applicationFee > $userBalance)--}}
                            ><i class="las la-check-circle"></i> @lang('Apply')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
