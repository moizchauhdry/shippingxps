@extends('email')

@section('content')
<div class="container">
    <p>
        Dear Customer, <br>
        <a href="{{$data['verification_url']}}">
            Custom verification link
        </a>
    </p>
</div>
@endsection