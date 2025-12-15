<div class="row gy-3">
    <div class="col-lg-12">
        <div class="card custom--card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--responsive--md">
                        <thead>
                            <tr>
                                <th>@lang('S.N.')</th>
                                <th>@lang('Installment Amount')</th>
                                <th>@lang('Installment Date')</th>
                                <th>@lang('Given On')</th>
                                <th>@lang('Delay')</th>
                                <th>@lang('Delay Amount')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($installments as $installment)
                                <tr>
                                    <td>{{ __($loop->index + $installments->firstItem()) }}</td>
                                    <td>{{ $installment->loan->per_installment }}</td>
                                    <td
                                        class="{{ !$installment->given_at && $installment->installment_date < today() ? 'text--danger' : '' }}">
                                        {{ showDateTime($installment->installment_date, 'd M, Y') }}
                                    </td>
                                    <td>
                                        @if ($installment->given_at)
                                            {{ showDateTime($installment->given_at, 'd M, Y') }}
                                        @else
                                            <small>@lang('Not yet')</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($installment->given_at)
                                            @php
                                                $delay = $installment->given_at->gt($installment->installment_date)
                                                    ? $installment->given_at->diffInDays($installment->installment_date)
                                                    : 0;
                                            @endphp
                                            {{ $delay }} @lang('Day')
                                        @else
                                            ...
                                        @endif
                                    </td>
                                    <td>
                                        {{ $installment->delay_charge }}
                                    </td>
                                    <td>
{{--                                        @if($installment->installment_date > today() && is_null($installment->given_at))--}}
                                        @if(is_null($installment->given_at))
                                            <form action="{{ route('user.loan.pay_installment') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $installment->id }}">
                                                <button type="submit" class="btn btn--base btn--smd">Pay Now</button>
                                            </form>
                                        @else
                                            <button class="btn btn--base btn--smd" disabled="">Paid</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @if ($installments->hasPages())
            <div class="text-center py-3">
                {{ paginateLinks($installments) }}
            </div>
        @endif
    </div>
</div>

@push('style')
    <style>
        .list-group {
            gap: 0.8rem;
        }
        .list-group-item {
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
            border: 0;
            padding: 0;
        }
        .caption {
            font-size: 0.8rem;
            color: #b1b1b1;
            line-height: 1;
        }
        .value {
            color: #686a81;
            font-weight: 500;
            line-height: 1.8;
        }
    </style>
@endpush
