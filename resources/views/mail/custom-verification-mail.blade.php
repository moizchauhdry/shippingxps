@extends('email')

@section('styles')
<style>
    .container p {
        font-size: 16px;
        color: #333;
        line-height: 1.5;
        margin: 0 0 20px;
    }

    .container a {
        display: inline-block;
        text-decoration: none;
        background-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .container a:hover {
        background-color: #0056b3;
    }
</style>
@endsection

@section('content')
<div class="container">
    <p>
        Please click the button below to verify your email address.
    </p>
    <p>
        <a href="{{$data['verification_url']}}">
            Verify Email Address
        </a>
    </p>
</div>

@endsection