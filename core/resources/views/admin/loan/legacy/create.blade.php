@extends('admin.layouts.app')
@section('panel')

    <form action="{{ route('admin.loan.legacy.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row gy-4">

            {{-- BORROWER & PLAN --}}
            <div class="col-12">
                <div class="card box--shadow1">
                    <div class="card-body">
                        <h5 class="card-title border-bottom pb-2">@lang('Borrower & Loan Plan')</h5>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>@lang('Borrower') <span class="text--danger">*</span></label>
                                <select name="user_id" class="form-control" required>
                                    <option value="" disabled selected>@lang('Select Borrower')</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                            {{ $u->firstname }} {{ $u->lastname }} ({{ $u->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>@lang('Legacy Loan Plan') <span class="text--danger">*</span></label>
                                <select name="plan_id" id="plan_select" class="form-control" required>
                                    <option value="" disabled selected>@lang('Select Plan')</option>
                                    @foreach($legacyPlans as $plan)
                                        <option value="{{ $plan->id }}"
                                                data-capital="{{ number_format($plan->capital_ratio * 100, 2) }}"
                                                data-profit="{{ number_format($plan->profit_ratio * 100, 2) }}"
                                            {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }} ({{ number_format($plan->capital_ratio * 100, 0) }}% Cap / {{ number_format($plan->profit_ratio * 100, 0) }}% Profit)
                                        </option>
                                    @endforeach
                                </select>
                                @if($legacyPlans->isEmpty())
                                    <small class="text--danger">No legacy plans found. Create one in Plans → set "Plan Type" = Legacy Fixed Term Loan.</small>
                                @endif
                                <div id="plan_allocation_preview" class="mt-2" style="display:none;">
                                    <small class="text--info">
                                        <i class="fas fa-info-circle"></i>
                                        Each instalment allocates <strong><span id="prev_cap">50</span>%</strong> Capital and <strong><span id="prev_prof">50</span>%</strong> Profit
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ORIGINAL LOAN INFO --}}
            <div class="col-12">
                <div class="card box--shadow1">
                    <div class="card-body">
                        <h5 class="card-title border-bottom pb-2">@lang('Original Loan Information')</h5>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>@lang('Original Loan Amount') <span class="text--danger">*</span></label>
                                <input type="number" step="0.01" name="original_loan_amount" id="original_loan_amount" class="form-control" value="{{ old('original_loan_amount') }}" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>@lang('Total Repayable Amount') <span class="text--danger">*</span></label>
                                <input type="number" step="0.01" name="total_repayable_amount" id="total_repayable_amount" class="form-control" value="{{ old('total_repayable_amount') }}" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>@lang('Original Loan Date') <span class="text--danger">*</span></label>
                                <input type="date" name="original_loan_date" class="form-control" value="{{ old('original_loan_date') }}" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>@lang('Instalment Amount') <span class="text--danger">*</span></label>
                                <input type="number" step="0.01" name="installment_amount" id="installment_amount" class="form-control" value="{{ old('installment_amount') }}" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>@lang('Total Number of Instalments') <span class="text--danger">*</span></label>
                                <input type="number" name="total_installments" id="total_installments" class="form-control" value="{{ old('total_installments') }}" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>@lang('Instalment Interval (days)') <span class="text--danger">*</span></label>
                                <input type="number" name="installment_interval" class="form-control" value="{{ old('installment_interval', 7) }}" required>
                            </div>
                            <div class="col-md-12 form-group">
                                <label>@lang('Original Agreement Reference (if applicable)')</label>
                                <input type="text" name="original_agreement_ref" class="form-control" value="{{ old('original_agreement_ref') }}" maxlength="80">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CURRENT LOAN POSITION --}}
            <div class="col-12">
                <div class="card box--shadow1">
                    <div class="card-body">
                        <h5 class="card-title border-bottom pb-2">@lang('Current Loan Position')</h5>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>@lang('Instalments Paid') <span class="text--danger">*</span></label>
                                <input type="number" name="installments_paid" id="installments_paid" class="form-control" value="{{ old('installments_paid', 0) }}" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>@lang('Instalments Remaining')</label>
                                <input type="number" id="installments_remaining" class="form-control" readonly value="0" style="background:#f7f9fc;">
                                <small class="text-muted">@lang('Auto-calculated')</small>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>@lang('Missed Instalments') <span class="text--danger">*</span></label>
                                <input type="number" name="missed_installments" class="form-control" value="{{ old('missed_installments', 0) }}" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>@lang('Number of Days Late') <span class="text--danger">*</span></label>
                                <input type="number" name="days_late" class="form-control" value="{{ old('days_late', 0) }}" required>
                            </div>

                            <div class="col-md-3 form-group">
                                <label>@lang('Daily Late Fee') <span class="text--danger">*</span></label>
                                <input type="number" step="0.01" name="daily_late_fee" class="form-control" value="{{ old('daily_late_fee', 0) }}" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>@lang('Grace Period (days)')</label>
                                <input type="number" name="grace_period_days" class="form-control" value="{{ old('grace_period_days', 0) }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>@lang('Total Late Fees Accrued') <span class="text--danger">*</span></label>
                                <input type="number" step="0.01" name="total_late_fees_accrued" class="form-control" value="{{ old('total_late_fees_accrued', 0) }}" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>@lang('Other Charges / Legal Fees') <span class="text--danger">*</span></label>
                                <input type="number" step="0.01" name="other_charges" class="form-control" value="{{ old('other_charges', 0) }}" required>
                            </div>

                            <div class="col-md-6 form-group">
                                <label>@lang('Current Outstanding Balance')</label>
                                <input type="number" step="0.01" name="current_outstanding_balance" id="current_outstanding_balance" class="form-control" value="{{ old('current_outstanding_balance') }}">
                                <small class="text-muted">@lang('Optional reference figure from your records. The system will compute its own based on instalments.')</small>
                                <div id="balance_mismatch_warning" class="alert alert-warning py-2 mt-2" style="display:none; font-size:11px;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    System-computed balance: <strong><span id="computed_balance">0.00</span></strong>.
                                    Difference: <strong><span id="balance_diff">0.00</span></strong>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>@lang('Next Instalment Due Date')</label>
                                <input type="date" name="next_installment_date" class="form-control" value="{{ old('next_installment_date') }}">
                                <small class="text-muted">@lang('When the next instalment should be collected. Defaults to original-date + intervals if blank.')</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SUPPORTING DOCUMENTATION --}}
            <div class="col-12">
                <div class="card box--shadow1">
                    <div class="card-body">
                        <h5 class="card-title border-bottom pb-2">@lang('Supporting Documentation')</h5>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>@lang('Original Loan Agreement (PDF)')</label>
                                <input type="file" name="original_agreement" class="form-control" accept="application/pdf">
                                <small class="text-muted">@lang('Max 10MB. PDF only.')</small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>@lang('Supporting Documents (optional, multiple)')</label>
                                <input type="file" name="supporting_documents[]" class="form-control" multiple>
                                <small class="text-muted">@lang('Max 10MB per file.')</small>
                            </div>
                            <div class="col-md-12 form-group">
                                <label>@lang('Admin Notes')</label>
                                <textarea name="notes" class="form-control" rows="3" maxlength="2000">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SUBMIT --}}
            <div class="col-12">
                <button type="submit" class="btn btn--primary w-100 h-45">
                    <i class="fas fa-file-import"></i> @lang('Import Legacy Loan')
                </button>
            </div>
        </div>
    </form>

@endsection

@push('script')
    <script>
        (function () {
            'use strict';

            const planSelect       = document.getElementById('plan_select');
            const totalInstInp     = document.getElementById('total_installments');
            const paidInstInp      = document.getElementById('installments_paid');
            const remainingInp     = document.getElementById('installments_remaining');
            const instAmountInp    = document.getElementById('installment_amount');
            const outstandingInp   = document.getElementById('current_outstanding_balance');
            const computedBalSpan  = document.getElementById('computed_balance');
            const balanceDiffSpan  = document.getElementById('balance_diff');
            const balanceWarn      = document.getElementById('balance_mismatch_warning');
            const allocPreview     = document.getElementById('plan_allocation_preview');
            const prevCap          = document.getElementById('prev_cap');
            const prevProf         = document.getElementById('prev_prof');

            function calcRemaining() {
                const total = parseInt(totalInstInp.value || 0, 10);
                const paid  = parseInt(paidInstInp.value || 0, 10);
                const remaining = Math.max(0, total - paid);
                remainingInp.value = remaining;
                return remaining;
            }

            function calcOutstandingDiff() {
                const total = parseInt(totalInstInp.value || 0, 10);
                const paid  = parseInt(paidInstInp.value || 0, 10);
                const amt   = parseFloat(instAmountInp.value || 0);
                const userBalance = parseFloat(outstandingInp.value || 0);
                const computed = (total - paid) * amt;

                computedBalSpan.textContent = computed.toFixed(2);

                if (outstandingInp.value === '' || !userBalance) {
                    balanceWarn.style.display = 'none';
                    return;
                }

                const diff = Math.abs(computed - userBalance);
                balanceDiffSpan.textContent = diff.toFixed(2);
                balanceWarn.style.display = diff > 0.01 ? 'block' : 'none';
            }

            function showAllocation() {
                const opt = planSelect.options[planSelect.selectedIndex];
                if (!opt || !opt.dataset.capital) {
                    allocPreview.style.display = 'none';
                    return;
                }
                prevCap.textContent  = opt.dataset.capital;
                prevProf.textContent = opt.dataset.profit;
                allocPreview.style.display = 'block';
            }

            [totalInstInp, paidInstInp].forEach(el => el.addEventListener('input', () => {
                calcRemaining();
                calcOutstandingDiff();
            }));
            [instAmountInp, outstandingInp].forEach(el => el.addEventListener('input', calcOutstandingDiff));
            planSelect.addEventListener('change', showAllocation);

            // Initial state on page load
            calcRemaining();
            calcOutstandingDiff();
            showAllocation();
        })();
    </script>
@endpush
