@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-xl-4 mb-30">
            <div class="card overflow-hidden box--shadow1">
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
                            <span class="fw-bold">@lang('Amount')</span>
                            <span class="fw-bold text--warning">{{ showAmount($loan->amount) }}</span>
                        </li>

                        <li class="list-group-item">
                            <span class="fw-bold">@lang('Per Instalment')</span>
                            <span>{{ showAmount($loan->per_installment) }}</span>
                        </li>

                        <li class="list-group-item">
                            <span class="fw-bold">@lang('Total No of Instalments')</span>
                            <span>{{ $loan->total_installment }}</span>
                        </li>

                        <li class="list-group-item">
                            <span class="fw-bold">@lang('No of Instalments Paid')</span>
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
                        <h6 class="mt-3">
                            <i class="fa fa-info-circle text--danger" aria-hidden="true"></i>
                            @lang('Reason of Rejection')
                        </h6>
                        <p class="mt-2">{{ $loan->admin_feedback }}</p>
                    @else
                        <form action="{{ route('admin.loan.assign') }}" method="POST" class="mt-2">
                            @csrf
                            <input type="hidden" name="id" value="{{ $loan->id }}">
                            <div class="form-group">
                                <label>@lang('Assign Loan')</label>
                                <select name="admin_id" class="form-control" required>
                                    <option value="" disabled selected>@lang('Select One')</option>
                                    @foreach (\App\Models\Admin::where('status', 1)->where('id', '!=', 1)->get() as $admin)
                                        <option value="{{ $admin->id }}" {{ isset($loan->approved_by) && $loan->approved_by == $admin->id ? 'selected' : '' }}>
                                            {{ $admin->name }}
                                        </option>
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

            {{-- LOAN LIFECYCLE PANEL --}}
            <div class="card box--shadow1 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 flex-wrap gap-2">
                        <h5 class="card-title mb-0">
                            @lang('Loan Lifecycle')
                        </h5>
                        <div>
                            {!! $loan->lifecycle_stage_badge !!}
                            <a href="{{ route('admin.loan.lifecycle.history', $loan->id) }}" class="btn btn-sm btn-outline-secondary ms-1">
                                <i class="fas fa-history"></i> @lang('History')
                            </a>
                        </div>
                    </div>

                    {{-- Stage progress indicator --}}
                    <div class="d-flex flex-wrap gap-2 mb-3" style="font-size:11px;">
                        @php
                            $stages = [
                                \App\Constants\Status::LIFECYCLE_ACTIVE              => 'Active',
                                \App\Constants\Status::LIFECYCLE_REDEMPTION_OFFERED  => 'Redemption Offered',
                                \App\Constants\Status::LIFECYCLE_REDEMPTION_ACCEPTED => 'Pending Payment',
                                \App\Constants\Status::LIFECYCLE_SETTLED             => 'Settled',
                                \App\Constants\Status::LIFECYCLE_CLOSED              => 'Closed',
                                \App\Constants\Status::LIFECYCLE_SECURITY_RELEASED   => 'Security Released',
                            ];
                        @endphp
                        @foreach($stages as $stageId => $stageLabel)
                            <span class="badge {{ $loan->lifecycle_stage >= $stageId ? 'bg-success' : 'bg-light text-dark' }}" style="padding:6px 10px;">
                    {{ $loop->iteration }}. {{ $stageLabel }}
                </span>
                        @endforeach
                    </div>

                    {{-- Active quote info if present --}}
                    @if($loan->activeQuote && $loan->lifecycle_stage == \App\Constants\Status::LIFECYCLE_REDEMPTION_OFFERED)
                        <div class="alert alert-warning py-2 mb-3" style="font-size:12px;">
                            <strong>Active Redemption Offer:</strong>
                            {{ $loan->activeQuote->quote_reference }} —
                            Amount: <strong>{{ showAmount($loan->activeQuote->settlement_amount) }}</strong> —
                            Expires: <strong>{{ $loan->activeQuote->expires_at->format('d M Y, H:i') }}</strong>
                            @if($loan->activeQuote->isExpired())
                                <span class="badge bg-danger ms-1">EXPIRED</span>
                            @endif
                        </div>
                    @endif

                    {{-- Pending payment info --}}
                    @if($loan->lifecycle_stage == \App\Constants\Status::LIFECYCLE_REDEMPTION_ACCEPTED)
                        <div class="alert alert-info py-2 mb-3" style="font-size:12px;">
                            <strong>Awaiting Settlement Payment:</strong>
                            {{ showAmount($loan->activeQuote->settlement_amount) }}
                            — accepted {{ $loan->quote_accepted_at->diffForHumans() }}
                        </div>
                    @endif

                    {{-- Stage-specific actions --}}
                    <div class="d-flex flex-wrap gap-2">
                        @if($loan->canAcceptQuote())
                            <form action="{{ route('admin.loan.quote.accept', $loan->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Accept this redemption offer? The settlement amount will be locked.')">
                                    <i class="fas fa-check-circle"></i> @lang('Accept Redemption Offer')
                                </button>
                            </form>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#voidQuoteModal">
                                <i class="fas fa-times"></i> @lang('Void Quote')
                            </button>
                        @endif

                        @if($loan->canRecordSettlementPayment())
                            <button type="button" class="btn btn--primary" data-bs-toggle="modal" data-bs-target="#settlementPaymentModal">
                                <i class="fas fa-money-bill-wave"></i> @lang('Record Settlement Payment')
                            </button>
                        @endif

                        @if($loan->canCloseAccount())
                            <form action="{{ route('admin.loan.close.account', $loan->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-dark" onclick="return confirm('Close this account permanently? Settlement Certificate will be generated.')">
                                    <i class="fas fa-lock"></i> @lang('Close Account')
                                </button>
                            </form>
                        @endif

                        @if($loan->canReleaseSecurity())
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#releaseSecurityModal">
                                <i class="fas fa-key"></i> @lang('Release Security')
                            </button>
                        @endif

                        @if($loan->lifecycle_stage == \App\Constants\Status::LIFECYCLE_SECURITY_RELEASED)
                            <span class="badge bg-success py-2 px-3">
                    <i class="fas fa-check-double"></i> @lang('Lifecycle Complete')
                </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card box--shadow1">
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-2">@lang('Loan Form Submitted by User')</h5>
                    <x-view-form-data :data="$loan->application_form"/>

                    @if (($loan->status == Status::LOAN_APPROVED || $loan->status == Status::LOAN_RUNNING) && $loan->signed_agreement)
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
                        <div class="col-md-12 d-flex flex-wrap gap-2">
                            @if ($loan->status == Status::LOAN_PENDING)
                                <button class="btn btn-outline--warning confirmationBtn"
                                        data-action="{{ route('admin.loan.review', $loan->id) }}"
                                        data-question="@lang('Are you sure to Review this loan?')">
                                    <i class="fas la-check"></i> @lang('Review')
                                </button>
                            @endif

                            @if ($loan->status == Status::LOAN_IN_REVIEW)
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

                            @if ($loan->status != Status::LOAN_RUNNING)
                                <button class="btn btn-outline--danger rejectBtn"
                                        data-action="{{ route('admin.loan.reject', $loan->id) }}">
                                    <i class="fas fa-ban"></i> @lang('Reject')
                                </button>
                            @endif

                            {{-- REPORTS & DOCUMENTS DROPDOWN --}}
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-outline--primary dropdown-toggle" type="button"
                                        id="reportsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-file-alt"></i> @lang('Reports & Documents')
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="reportsDropdown">
                                    <li>
                                        <a class="dropdown-item"
                                           href="{{ route('admin.loan.statement.pdf', $loan->id) }}"
                                           target="_blank">
                                            <i class="fas fa-file-pdf text--primary"></i>
                                            @lang('Statement of Loan Account')
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                           href="{{ route('admin.loan.redemption.quote', $loan->id) }}"
                                           target="_blank">
                                            <i class="fas fa-file-invoice-dollar text--success"></i>
                                            @lang('Early Redemption Quote')
                                        </a>
                                    </li>

                                    {{-- SETTLEMENT CERTIFICATE — only if loan fully paid --}}
                                    @if(in_array($loan->lifecycle_stage, [Status::LIFECYCLE_CLOSED, Status::LIFECYCLE_SECURITY_RELEASED]))
                                        <li>
                                            <a class="dropdown-item"
                                               href="{{ route('admin.loan.settlement.certificate', $loan->id) }}"
                                               target="_blank">
                                                <i class="fas fa-certificate text--warning"></i>
                                                @lang('Settlement Certificate')
                                                <span class="badge bg-success ms-1" style="font-size:9px;">CLOSED</span>
                                            </a>
                                        </li>
                                    @endif

                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>

                                    <li>
                                        <a class="dropdown-item"
                                           href="{{ route('admin.loan.payment.history.pdf', $loan->id) }}"
                                           target="_blank">
                                            <i class="fas fa-history text--info"></i>
                                            @lang('Payment History')
                                        </a>
                                    </li>

                                    @if ($loan->signed_agreement)
                                        <li>
                                            <a class="dropdown-item"
                                               href="{{ route('admin.loan.view.agreement', $loan->id) }}"
                                               target="_blank">
                                                <i class="fas fa-file-contract text--warning"></i>
                                                @lang('Loan Agreement')
                                            </a>
                                        </li>
                                    @else
                                        <li>
                                            <span class="dropdown-item disabled text-muted">
                                                <i class="fas fa-file-contract"></i>
                                                @lang('Loan Agreement') <small>(not uploaded)</small>
                                            </span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- LOAN DOCUMENTS (full width) --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card box--shadow1">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 flex-wrap gap-2">
                        <h5 class="card-title mb-0">
                            @lang('Loan Documents')
                            @if ($loan->is_legacy)
                                <span class="badge bg-info ms-1" style="font-size:9px;">LEGACY</span>
                            @endif
                        </h5>
                        <button type="button" class="btn btn-sm btn--primary"
                                data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                            <i class="fas fa-upload"></i> @lang('Upload')
                        </button>
                    </div>

                    @if ($loan->documents->count())
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                <tr>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Filename')</th>
                                    <th>@lang('Size')</th>
                                    <th>@lang('Uploaded')</th>
                                    <th class="text-end">@lang('Actions')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($loan->documents as $doc)
                                    <tr>
                                        <td>
                                            @if ($doc->document_type == 'original_agreement')
                                                <span class="badge bg-primary">Original Agreement</span>
                                            @elseif ($doc->document_type == 'supporting')
                                                <span class="badge bg-secondary">Supporting</span>
                                            @else
                                                <span class="badge bg-light text-dark">Other</span>
                                            @endif
                                        </td>
                                        <td style="word-break:break-all;">{{ $doc->original_filename }}</td>
                                        <td>{{ $doc->file_size_formatted }}</td>
                                        <td>{{ $doc->created_at->format('d M Y') }}</td>
                                        <td class="text-end text-nowrap">
                                            <a href="{{ route('admin.loan.documents.download', $doc->id) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <form action="{{ route('admin.loan.documents.delete', $doc->id) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Delete this document?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">@lang('No documents uploaded yet.')</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Upload Modal --}}
    <div class="modal fade" id="uploadDocModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.loan.documents.upload', $loan->id) }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Upload Document')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Document Type')</label>
                            <select name="document_type" class="form-control" required>
                                <option value="original_agreement">Original Loan Agreement</option>
                                <option value="supporting">Supporting Document</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('File') <span class="text--danger">*</span></label>
                            <input type="file" name="document" class="form-control" required>
                            <small class="text-muted">Max 10MB.</small>
                        </div>
                        <div class="form-group">
                            <label>@lang('Notes')</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100">@lang('Upload')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <x-confirmation-modal/>

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

    <div class="modal fade" id="settlementPaymentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('admin.loan.settlement.payment.record', $loan->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Record Settlement Payment')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal"><i class="las la-times"></i></button>
                    </div>
                    <div class="modal-body">
                        @if($loan->activeQuote)
                            <div class="alert alert-info py-2 mb-3">
                                <strong>Expected:</strong> {{ showAmount($loan->activeQuote->settlement_amount) }}
                                (Quote {{ $loan->activeQuote->quote_reference }})
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>@lang('Amount Received') <span class="text--danger">*</span></label>
                                <input type="number" step="0.01" name="received_amount" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>@lang('Payment Date') <span class="text--danger">*</span></label>
                                <input type="date" name="payment_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>@lang('Payment Method')</label>
                                <select name="payment_method" class="form-control">
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cash">Cash</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="card">Card</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>@lang('Payment Reference')</label>
                                <input type="text" name="payment_reference" class="form-control" maxlength="100">
                            </div>
                            <div class="col-12 form-group">
                                <label>@lang('Notes')</label>
                                <textarea name="notes" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12 form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="accept_short" value="1" id="acceptShort">
                                    <label class="form-check-label" for="acceptShort">
                                        Accept short payment if received amount is less than expected
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100">@lang('Record Payment & Mark Settled')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="voidQuoteModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.loan.quote.void', $loan->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Void Redemption Quote')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal"><i class="las la-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <p>Voiding will return the loan to Active status. The quote PDF remains in archive.</p>
                        <div class="form-group">
                            <label>@lang('Reason')</label>
                            <textarea name="reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger w-100">@lang('Void Quote')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="releaseSecurityModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.loan.release.security', $loan->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Release Security')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal"><i class="las la-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">@lang('Confirm each item that has been actioned:')</p>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="security_returned" value="1" id="secRet">
                            <label class="form-check-label" for="secRet">Security Returned</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="lien_released" value="1" id="lienRel">
                            <label class="form-check-label" for="lienRel">Lien / Encumbrances Released</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="documents_returned" value="1" id="docsRet">
                            <label class="form-check-label" for="docsRet">Documents / Title Deeds Returned</label>
                        </div>
                        <div class="form-group mt-3">
                            <label>@lang('Notes')</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning w-100">@lang('Confirm Release')</button>
                    </div>
                </div>
            </form>
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
        (function ($) {
            "use strict";
            $('.rejectBtn').on('click', function () {
                var modal = $('#rejectModal');
                modal.find('form')[0].action = $(this).data('action');
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
