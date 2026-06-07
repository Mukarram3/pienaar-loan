@extends('admin.layouts.app')
@section('panel')

    <div class="card box--shadow1">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                <div>
                    <h5 class="card-title mb-0">@lang('Lifecycle History')</h5>
                    <small class="text-muted">Loan: {{ $loan->loan_number }}</small>
                </div>
                <a href="{{ route('admin.loan.details', $loan->id) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Loan
                </a>
            </div>

            @if($events->count())
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                        <tr>
                            <th>@lang('When')</th>
                            <th>@lang('Event')</th>
                            <th>@lang('Notes')</th>
                            <th>@lang('Actor')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($events as $event)
                            <tr>
                                <td style="white-space:nowrap;">{{ $event->created_at->format('d M Y, H:i') }}</td>
                                <td><span class="badge bg-primary">{{ str_replace('_', ' ', strtoupper($event->event_type)) }}</span></td>
                                <td style="font-size:12px;">
                                    {{ $event->notes }}
                                    @if($event->metadata && is_array($event->metadata))
                                        <div style="font-size:10px; color:#777; margin-top:4px;">
                                            @foreach($event->metadata as $k => $v)
                                                <span class="me-2"><strong>{{ $k }}:</strong> {{ is_scalar($v) ? $v : json_encode($v) }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $event->actor?->name ?? 'System' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $events->links() }}
            @else
                <p class="text-muted">@lang('No lifecycle events recorded yet.')</p>
            @endif
        </div>
    </div>

@endsection
