{{--
    =============================================================
    File: resources/views/admin/plans/loan/form.blade.php
    =============================================================
--}}
@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ route('admin.plans.loan.save', $plan->id ?? 0) }}" method="POST">
                @csrf
                <div class="card  mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Plan Name')</label>
                                    <input class="form-control" name="name" type="text" value="{{ old('name', @$plan->name)  }}"
                                        required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Title')</label>
                                    <input class="form-control" name="title" type="text" value="{{ old('title', @$plan->title) }}"
                                        required />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Minimum Amount')</label>
                                    <div class="input-group">
                                        @php $minAmount = isset($plan) ? getAmount($plan->minimum_amount) : null; @endphp
                                        <input class="form-control" name="minimum_amount" type="number"
                                            value="{{ old('minimum_amount', $minAmount) }}" step="any" required />
                                        <span class="input-group-text"> {{ __(gs('cur_text')) }} </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Maximum Amount')</label>
                                    <div class="input-group">
                                        @php $maxAmount = isset($plan) ? getAmount($plan->maximum_amount) : null; @endphp
                                        <input class="form-control" name="maximum_amount" type="number"
                                            value="{{ old('maximum_amount', $maxAmount) }}" step="any" required />
                                        <span class="input-group-text"> {{ __(gs('cur_text')) }} </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Category')</label>
                                    <select name="category_id" class="form-control select2" data-minimum-results-for-search="-1"
                                    required>
                                        <option value="" disabled selected>@lang('Select One')</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected(@$category->id == @$plan->category_id)>
                                                {{ __($category->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Plan Type') <span class="text--danger">*</span></label>
                                    <select name="is_legacy" id="is_legacy_toggle" class="form-control" required>
                                        <option value="0" {{ old('is_legacy', $plan->is_legacy ?? 0) == 0 ? 'selected' : '' }}>Standard RapidLab Plan</option>
                                        <option value="1" {{ old('is_legacy', $plan->is_legacy ?? 0) == 1 ? 'selected' : '' }}>Legacy Fixed Term Loan</option>
                                    </select>
                                    <small class="text-muted">@lang('Legacy plans do not recalculate using weekly percentage charges.')</small>
                                </div>
                            </div>

                            {{-- Calculation engine selector (spec s.24) --}}
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>@lang('Calculation Type') <span class="text--danger">*</span></label>
                                    <select name="calculation_type" id="calculation_type" class="form-control">
                                        <option value="standard"
                                            @selected(old('calculation_type', $plan->calculation_type ?? 'standard') === 'standard')>
                                            @lang('Standard — instalment as configured')
                                        </option>
                                        <option value="fixed_capital"
                                            @selected(old('calculation_type', $plan->calculation_type ?? 'standard') === 'fixed_capital')>
                                            @lang('Fixed Capital Repayment — Per Instalment % is a capital rate')
                                        </option>
                                    </select>
                                    <small class="text-muted">
                                        @lang('On a Fixed Capital Repayment plan the Per Instalment % is the percentage of ORIGINAL CAPITAL repaid each interval. The full instalment is derived as capital divided by the Capital Allocation.')
                                    </small>
                                </div>
                            </div>

                            {{-- Live worked example so the administrator can see the effect --}}
                            <div class="row" id="calc_preview_block" style="display:none;">
                                <div class="col-12 form-group">
                                    <div class="alert alert-info py-2 mb-0" style="font-size:13px;">
                                        <strong>@lang('Worked example on a')
                                            <span id="calc_preview_amount">R100,000</span> @lang('advance'):</strong>
                                        <div id="calc_preview_body" class="mt-1"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Capital / Profit allocation — only relevant for legacy plans --}}
                            <div id="legacy_allocation_block" class="row" style="display: {{ (old('is_legacy', $plan->is_legacy ?? 0) == 1 || old('calculation_type', $plan->calculation_type ?? 'standard') === 'fixed_capital') ? 'flex' : 'none' }};">
                                <div class="col-md-6 form-group">
                                    <label>@lang('Capital Allocation (%)') <span class="text--danger">*</span></label>
                                    <input type="number" step="0.01" min="0" max="100" name="capital_ratio_pct" id="capital_ratio_pct"
                                           class="form-control"
                                           value="{{ old('capital_ratio_pct', isset($plan) ? number_format($plan->capital_ratio * 100, 2) : 50) }}">
                                    <small class="text-muted">@lang('Portion of each instalment allocated to capital. Default 50%.')</small>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>@lang('Profit Allocation (%)') <span class="text--danger">*</span></label>
                                    <input type="number" step="0.01" min="0" max="100" name="profit_ratio_pct" id="profit_ratio_pct"
                                           class="form-control"
                                           value="{{ old('profit_ratio_pct', isset($plan) ? number_format($plan->profit_ratio * 100, 2) : 50) }}">
                                    <small class="text-muted">@lang('Portion of each instalment allocated to profit. Should sum to 100% with capital.')</small>
                                </div>
                                <div class="col-12">
                                    <div id="allocation_warning" class="alert alert-warning py-2" style="display:none; font-size:12px;">
                                        <i class="fas fa-exclamation-triangle"></i> Capital + Profit should sum to 100%.
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Per Instalment')</label>
                                    <div class="input-group">
                                        @php $perInstallment = isset($plan) ? getAmount($plan->per_installment) : null; @endphp
                                        <input class="form-control" name="per_installment" type="number"
                                            value="{{ old('per_installment', $perInstallment) }}" step="any" required />
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Installment Interval')</label>
                                    <div class="input-group">
                                        @php $installmentInterval = isset($plan) ? getAmount($plan->installment_interval) : null; @endphp
                                        <input class="form-control" name="installment_interval" type="number"
                                            value="{{ old('installment_interval', $installmentInterval) }}" required />
                                        <span class="input-group-text">@lang('Days')</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Total Installments')</label>
                                    <div class="input-group">
                                        @php $totalInstallment = isset($plan) ? getAmount($plan->total_installment) : null; @endphp
                                        <input class="form-control" name="total_installment" type="number"
                                            value="{{ old('total_installment', $totalInstallment) }}" required />
                                        <span class="input-group-text">@lang('Times')</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Profit in Percentage')</label>
                                    <div class="input-group">
                                        @php $installmentInterval = isset($plan) ? getAmount($plan->installment_interval) : null; @endphp
                                        <input class="form-control admins_profit" type="number" disabled />
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Application Fixed Charge')</label>
                                    <div class="input-group">
                                        @php $applicationFixedCharge = isset($plan) ? getAmount($plan->application_fixed_charge) : null; @endphp
                                        <input class="form-control" name="application_fixed_charge" type="number"
                                            value="{{ old('application_fixed_charge', $applicationFixedCharge) }}"
                                            step="any" required />
                                        <span class="input-group-text"> {{ __(gs('cur_text')) }} </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Application Percent Charge')</label>
                                    <div class="input-group">
                                        @php $applicationPercentCharge = isset($plan) ? getAmount($plan->application_percent_charge) : null; @endphp
                                        <input class="form-control" name="application_percent_charge" type="number"
                                            value="{{ old('application_percent_charge', $applicationPercentCharge) }}"
                                            step="any" required />
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Featured')</label>
                                    <select name="is_featured" class="form-control select2" data-minimum-results-for-search="-1"
                                    >
                                        <option value="0" @selected(0 == @$plan->is_featured)>@lang('Non Featured')</option>
                                        <option value="1" @selected(1 == @$plan->is_featured)>@lang('Featured')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <label>@lang('Instruction')</label>
                                <div class="form-group">
                                    <textarea class="form-control border-radius-5 nicEdit" name="instruction" rows="8">@php echo @$plan->instruction @endphp</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card  mb-3">
                    <div class="card-header">
                        <h5 class="card-title text-center">
                            @lang('Installment Delay Charge') <i class="fa fa-info-circle text--primary" title="@lang('This charge will be apply for each delayed installment. The user needs to pay the charge with the installment amount.')"></i>
                        </h5>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label>@lang('Charge Will Apply If Delay')</label>
                                <div class="input-group">
                                    <input class="form-control" name="delay_value" type="number"
                                        value="{{ old('delay_value', @$plan->delay_value) }}" required>
                                    <span class="input-group-text">@lang('Day')</span>
                                </div>
                            </div>

                            <div class="form-group col-lg-4">
                                <label>@lang('Fixed Charge')</label>
                                <div class="input-group">
                                    <input class="form-control" name="fixed_charge" type="number"
                                        value="{{ @$plan ? getAmount(@$plan->fixed_charge) : old('fixed_charge') }}" step="any" required>
                                    <span class="input-group-text">@lang(gs('cur_text'))</span>
                                </div>
                            </div>

                            <div class="form-group col-lg-4">
                                <label>@lang('Percent Charge')</label>
                                <div class="input-group">
                                    <input class="form-control" name="percent_charge" type="number"
                                        value="{{ @$plan  ? getAmount(@$plan->percent_charge) : old('percent_charge') }}" step="any"
                                        required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">

                        <div class="card border--primary mt-3">
                            <div class="card-header d-flex justify-content-between">
                                <h5>@lang('Loan Application Form Fields')</h5>
                                <button type="button" class="btn btn-sm btn-outline--primary float-end form-generate-btn"> <i class="la la-fw la-plus"></i>@lang('Add New')</button>
                            </div>
                            <div class="card-body">
                                <x-generated-form :form="@$form" />
                            </div>
                        </div>
                    </div>
                </div>

                <button class="btn btn--primary w-100 h-45 mt-3" type="submit">@lang('Submit')</button>

            </form>
        </div><!-- card end -->
    </div>
    <x-form-generator-modal />
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.plans.loan.index') }}" />
@endpush


@push('script')
    <script>
        "use strict";
        (function($) {

            $('[name=per_installment], [name=total_installment]').on('input ', (e) => displayProfit());

            function displayProfit() {
                let totalInstallment = parseFloat($('[name=total_installment]').val());
                let perInstallment = parseFloat($('[name=per_installment]').val());
                let profit = (totalInstallment * perInstallment).toFixed(2);
                profit -= 100;
                $('.admins_profit').val(profit);
                if (profit <= 0) {
                    $('.admins_profit').css('border-color', 'red');
                    $('.admins_profit').siblings('.input-group-text').css('border-color', 'red');
                } else {
                    $('.admins_profit').removeAttr('style');
                    $('.admins_profit').siblings('.input-group-text').removeAttr('style');
                }
            }
            displayProfit();

            const toggle = document.getElementById('is_legacy_toggle');
            const block  = document.getElementById('legacy_allocation_block');
            const capInp = document.getElementById('capital_ratio_pct');
            const profInp = document.getElementById('profit_ratio_pct');
            const warn   = document.getElementById('allocation_warning');

            const calcType = document.getElementById('calculation_type');
            const preview  = document.getElementById('calc_preview_block');
            const prevBody = document.getElementById('calc_preview_body');

            function isFixedCapital() {
                return calcType && calcType.value === 'fixed_capital';
            }

            function showAllocationBlock() {
                if (!block) return;
                const legacyOn = toggle && toggle.value === '1';
                block.style.display = (legacyOn || isFixedCapital()) ? 'flex' : 'none';
            }

            // Mirrors LoanCalculator exactly. Display only -- the server is
            // always authoritative (spec s.20).
            function renderPreview() {
                if (!preview) return;

                if (!isFixedCapital()) {
                    preview.style.display = 'none';
                    return;
                }

                const principal = 100000;
                const perPct    = parseFloat($('[name=per_installment]').val() || 0);
                const n         = parseInt($('[name=total_installment]').val() || 0, 10);
                const capRatio  = parseFloat(capInp ? capInp.value : 50) / 100;
                const profRatio = parseFloat(profInp ? profInp.value : 50) / 100;

                if (!perPct || !n || !capRatio || Math.abs((capRatio + profRatio) - 1) > 0.0001) {
                    prevBody.innerHTML = '<em>Enter a valid Per Instalment %, Total Instalments and a Capital + Profit Allocation summing to 100% to see the example.</em>';
                    preview.style.display = 'block';
                    return;
                }

                const money = v => 'R' + v.toLocaleString('en-ZA', {minimumFractionDigits: 2, maximumFractionDigits: 2});

                const capitalPer = Math.round(principal * perPct) / 100;
                const totalPer   = Math.round(capitalPer * 100 / capRatio) / 100;
                const profitPer  = Math.round((totalPer - capitalPer) * 100) / 100;
                const totalCap   = Math.round(principal * perPct * n) / 100;
                const totalRepay = Math.round(totalCap * 100 / capRatio) / 100;
                const totalProf  = Math.round((totalRepay - totalCap) * 100) / 100;
                const recovery   = perPct * n;

                let html = 'Instalment <strong>' + money(totalPer) + '</strong> '
                         + '(capital ' + money(capitalPer) + ' + profit ' + money(profitPer) + ')'
                         + ' x ' + n + ' = <strong>' + money(totalRepay) + '</strong> total repayment'
                         + '<br>Total capital ' + money(totalCap) + ' &nbsp;|&nbsp; total profit ' + money(totalProf);

                if (Math.abs(recovery - 100) > 0.01) {
                    html += '<br><span style="color:#b00;"><strong>Warning:</strong> '
                          + perPct + '% x ' + n + ' = ' + recovery.toFixed(2)
                          + '% of capital (expected 100%). The borrower is scheduled to repay '
                          + (recovery > 100 ? 'MORE' : 'LESS') + ' than the capital advanced.</span>';
                }

                prevBody.innerHTML = html;
                preview.style.display = 'block';
            }

            function checkSum() {
                if (!capInp || !profInp || !warn) return;
                const total = parseFloat(capInp.value || 0) + parseFloat(profInp.value || 0);
                warn.style.display = Math.abs(total - 100) > 0.01 ? 'block' : 'none';
            }

            if (toggle) {
                toggle.addEventListener('change', function () {
                    showAllocationBlock();
                    renderPreview();
                });
            }

            if (calcType) {
                calcType.addEventListener('change', function () {
                    showAllocationBlock();
                    renderPreview();
                });
            }

            if (capInp && profInp) {
                capInp.addEventListener('input', function () { checkSum(); renderPreview(); });
                profInp.addEventListener('input', function () { checkSum(); renderPreview(); });
            }

            $('[name=per_installment], [name=total_installment]').on('input', renderPreview);

            showAllocationBlock();
            checkSum();
            renderPreview();

        })(jQuery);
    </script>
@endpush
