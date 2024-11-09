@extends('email-2')

@section('content')
<div style="margin-top:50px; text-align:center">
    {{-- <a href="https://selfshiplabel.com" target="_blank">
        <h1>https://selfshiplabel.com</h1>
    </a> <br> --}}

    <a href="https://selfshiplabel.com" target="_blank">
        <img src="https://app.shippingxps.com/images/promotional-banner.png" alt="" class="responsive-img">
    </a>

    <p>
        Thank you for using our application! <br>
    </p>

    <p>
        ©{{Carbon\Carbon::now()->year }} <a href="http://shippingxps.com">ShippingXPS.com</a> <br>
        <span>All Rights Reserved</span>
    </p>
</div>

<style>
    .responsive-img {
        width: 500px;
    }

    @media (max-width: 350px) {
        .responsive-img {
            width: 300px;
        }
    }
</style>
@endsection