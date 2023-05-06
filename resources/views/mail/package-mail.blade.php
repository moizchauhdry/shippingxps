@extends('email')

@section('styles')
<style>
    p {
        text-align: left !important;
    }
</style>
@endsection

@section('content')
<div class="container">
    <p>
        Dear Customer, <br>
        We have received new package {{$data['package_id']}} at our {{$data['warehouse']}} warehouse.
    </p>
    <p>
        <strong>Package ID:</strong> {{$data['package_id']}} <br>
        <strong>Dimensions:</strong> {{$data['dimensions']}}
        <strong>Weight:</strong> {{$data['weight']}} <br>
        <strong>Tracking Number:</strong> {{$data['tracking_number_in']}} <br>
    </p>
    <p>
        @foreach ($data['images'] as $image)
        {{$image}}
        <img src="https://app.shippingxps.com/public/uploads/{{$image->image}}" alt="" style="height:300px">
        <img src="https://app.shippingxps.com/public/uploads/1683378307_1683365863_1683315040_mr-2%20(2).png" alt=""
            style="height:300px">
        @endforeach
    </p>
</div>
@endsection