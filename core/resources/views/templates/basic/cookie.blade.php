@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="container section">
        <div class="container">
            @php
                echo @$cookie->data_values->description;
            @endphp
        </div>
    </section>
@endsection
