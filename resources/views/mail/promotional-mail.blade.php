@extends('email')

@section('content')
<div style="margin-top:50px">
    <a href="https://selfshiplabel.com" target="_blank">
        <h1>https://selfshiplabel.com</h1>
    </a> <br>

    <a href="https://selfshiplabel.com" target="_blank">
        <img src="https://app.shippingxps.com/images/promotional-banner.png" alt="" class="responsive-img">
    </a>
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