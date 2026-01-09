@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-xl-4 mb-30">
            <div class="card  overflow-hidden box--shadow1">

                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <span class="fw-bold">@lang('Plan')</span>
                            <span>{{ __(@$loan->plan->name) }}</span>
                        </li>

                        <li class="list-group-item">
                            <span class="fw-bold">@lang('Date of Application')</span>
                            <span>{{ showDateTime($loan->created_at, 'd M, Y, h:i A') }}</span>
                        </li>

                        <li class="list-group-item">
                            <span class="fw-bold">@lang('Loan Number')</span>
                            <span>{{ $loan->loan_number }}</span>
                        </li>

                        <li class="list-group-item">
                            <span class="fw-bold">@lang('Amount') </span>
                            <span class="fw-bold text--warning">{{ showAmount($loan->amount) }}</span>
                        </li>

                        <li class="list-group-item">
                            <span class="fw-bold">@lang('Per Installment')</span>
                            <span>{{ showAmount($loan->per_installment) }}</span>
                        </li>

                        <li class="list-group-item">
                            <span class="fw-bold">@lang('Total Installment')</span>
                            <span>{{ $loan->total_installment }}</span>
                        </li>

                        <li class="list-group-item">
                            <span class="fw-bold">@lang('Given Installment')</span>
                            <span>{{ $loan->given_installment }}</span>
                        </li>

                        <li class="list-group-item">
                            <span class="fw-bold">@lang('Total Payable')</span>
                            <span>{{ showAmount($loan->payable_amount) }}</span>
                        </li>

                        @php $profit = $loan->payable_amount - $loan->amount; @endphp

                        <li class="list-group-item">
                            <span class="fw-bold">@lang('Profit')</span>
                            <span class="fw-bold {{ $profit < 0 ? 'text--danger' : 'text--success' }}">
                                {{ showAmount($profit) }}
                            </span>
                        </li>

                        <li class="list-group-item">
                            <span class="fw-bold">@lang('Status')</span>
                            @php echo $loan->status_badge; @endphp
                        </li>
                    </ul>

                    @if ($loan->status == Status::LOAN_REJECTED && $loan->admin_feedback)
                        <h6 class="mt-3"> <i class="fa fa-info-circle text--danger" aria-hidden="true"></i>
                            @lang('Reason of Rejection')</h6>
                        <p class="mt-2">{{ $loan->admin_feedback }}</p>

                    @else

                        <form action="{{ route('admin.loan.assign') }}" method="POST" class="mt-2">
                            @csrf
                            <input type="hidden" name="id" value="{{ $loan->id }}">
                            <div class="form-group">
                                <label>@lang('Assign Loan')</label>
                                <select name="admin_id" class="form-control" required>
                                    <option value="" disabled selected>@lang('Select One')</option>
                                    @foreach (\App\Models\Admin::where('status',1)->where('id', '!=', 1)->get() as $admin)
                                        <option value="{{ $admin->id }}" {{ isset($loan->approved_by) && $loan->approved_by == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                            </div>

                        </form>

                    @endif

                </div>
            </div>
        </div>

        <div class="col-xl-8 mb-30">

            <div class="card  overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-2">@lang('Loan Form Submitted by User')</h5>
                    <x-view-form-data :data="$loan->application_form" />

                    @if( ($loan->status == Status::LOAN_APPROVED || $loan->status == Status::LOAN_RUNNING) && $loan->signed_agreement)
                        <h5 class="card-title border-bottom pb-2">@lang('Loan Agreement Submitted by User')</h5>
                        <a href="{{ route('admin.loan.view.agreement', $loan->id) }}"
                           class="btn btn-primary"
                           target="_blank">
                            View Signed Agreement
                        </a>
                    @else
                        <span class="text-muted">No agreement uploaded</span>
                    @endif

                        <div class="row mt-4">
                            <div class="col-md-12">
                                @if ($loan->status == Status::LOAN_PENDING)
                                        <button class="btn btn-outline--warning confirmationBtn"
                                                data-action="{{ route('admin.loan.review', $loan->id) }}"
                                                data-question="@lang('Are you sure to Review this loan?')">
                                            <i class="fas la-check"></i> @lang('Review')
                                        </button>
                                    @endif
                                    @if($loan->status == Status::LOAN_IN_REVIEW)
                                        <button class="btn btn-outline--success confirmationBtn"
                                                data-action="{{ route('admin.loan.approve', $loan->id) }}"
                                                data-question="@lang('Are you sure to approve this loan?')">
                                            <i class="fas la-check"></i> @lang('Approve')
                                        </button>
                                    @endif
                                    @if ($loan->status == Status::LOAN_APPROVED)
                                        <button class="btn btn-outline--warning confirmationBtn"
                                                data-action="{{ route('admin.loan.release_funds', $loan->id) }}"
                                                data-question="@lang('Are you sure to Release Funds for this loan?')">
                                            <i class="fas la-check"></i> @lang('Release Funds')
                                        </button>
                                    @endif
                                @if($loan->status != STATUS::LOAN_RUNNING)
                                        <button class="btn btn-outline--danger ms-1 rejectBtn"
                                                data-action="{{ route('admin.loan.reject', $loan->id) }}">
                                            <i class="fas fa-ban"></i> @lang('Reject')
                                        </button>
                                    @endif
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />

    <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirmation Alert')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Reason of Rejection')</label>
                            <textarea name="reason" maxlength="255" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .list-group-item {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.rejectBtn').on('click', function() {
                var modal = $('#rejectModal');
                modal.find('form')[0].action = $(this).data('action');
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
