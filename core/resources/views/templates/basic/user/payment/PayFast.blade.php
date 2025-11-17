@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card custom--card">
                    <div class="card-header">
                        <h5>@lang('Stripe Hosted')</h5>
                    </div>
                    <div class="card-body">
{{--                        <form role="form" class="disableSubmission payment appPayment" id="payment-form" method="{{ $data->method }}" action="{{ $data->url }}">--}}
                        <form role="form" id="payment-form" method="{{ $data->method }}" action="{{ $data->url }}">
                            @foreach($data->fields as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <button class="btn btn--base w-100" type="submit">@lang('Submit')</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

    <script>
        // document.getElementById('payment-form').submit();
    </script>
@endpush
