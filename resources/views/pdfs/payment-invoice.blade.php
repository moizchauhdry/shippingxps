<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
    <title>Payment Invoice</title>
    <style>
        body{
            margin-top:20px;
            background:#eee;
        }

        .invoice {
            background: #fff;
            padding: 20px
        }

        .invoice-company {
            font-size: 20px
        }

        .invoice-header {
            margin: 0 -20px;
            background: #f0f3f4;
            padding: 20px
        }

        .invoice-date,
        .invoice-from,
        .invoice-to {
            display: table-cell;
            width: 1%
        }

        .invoice-from,
        .invoice-to {
            padding-right: 20px
        }

        .invoice-date .date,
        .invoice-from strong,
        .invoice-to strong {
            font-size: 16px;
            font-weight: 600
        }

        .invoice-date {
            text-align: right;
            padding-left: 20px
        }

        .invoice-price {
            background: #f0f3f4;
            display: table;
            width: 100%
        }

        .invoice-price .invoice-price-left,
        .invoice-price .invoice-price-right {
            display: table-cell;
            padding: 20px;
            font-size: 20px;
            font-weight: 600;
            width: 75%;
            position: relative;
            vertical-align: middle
        }

        .invoice-price .invoice-price-left .sub-price {
            display: table-cell;
            vertical-align: middle;
            padding: 0 20px
        }

        .invoice-price small {
            font-size: 12px;
            font-weight: 400;
            display: block
        }

        .invoice-price .invoice-price-row {
            display: table;
            float: left
        }

        .invoice-price .invoice-price-right {
            width: 25%;
            background: #2d353c;
            color: #fff;
            font-size: 28px;
            text-align: right;
            vertical-align: bottom;
            font-weight: 300
        }

        .invoice-price .invoice-price-right small {
            display: block;
            opacity: .6;
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 12px
        }

        .invoice-footer {
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 10px
        }

        .invoice-note {
            color: #999;
            margin-top: 80px;
            font-size: 85%
        }

        .invoice>div:not(.invoice-footer) {
            margin-bottom: 20px
        }

        .btn.btn-white, .btn.btn-white.disabled, .btn.btn-white.disabled:focus, .btn.btn-white.disabled:hover, .btn.btn-white[disabled], .btn.btn-white[disabled]:focus, .btn.btn-white[disabled]:hover {
            color: #2d353c;
            background: #fff;
            border-color: #d9dfe3;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="col-md-12">
        <div class="invoice">
            <!-- begin invoice-company -->
            <div class="invoice-company text-center f-w-600">
                <img style="width: auto;height:80px" src="{{ asset('public/logo.png') }}" alt="">
            </div>
            <!-- end invoice-company -->
            <!-- begin invoice-header -->
            <div class="invoice-header">
                <div class="invoice-from">
                    <small>from</small>
                    <address class="m-t-5 m-b-5">
                        <strong class="text-inverse">ShippingXPS</strong><br>
                        {{ $package->warehouse->name ?? $order->warehouse->name }}<br>
                        Address:{{ $package->warehouse->address ?? $order->warehouse->address   }}<br>
                        Phone: {{ $package->warehouse->phone ?? $order->warehouse->phone   }}<br>
                        Email : {{ $package->warehouse->email ?? $order->warehouse->email   }}
                    </address>
                </div>
                <div class="invoice-to">
                    <small>to</small>
                    <address class="m-t-5 m-b-5">
                        <strong class="text-inverse">{{ $customer->address->fullname ?? '- -' }}</strong><br>
                        Address:{{ $customer->address->address ?? '- -' }}<br>
                        Phone: {{ $customer->address->phone ?? '- -' }}<br>
                        Email: {{ $customer->customer->email ?? '- -' }}
                    </address>
                </div>
                <div class="invoice-date">
                    <small><strong>Invoice</strong></small><br>
                    <strong>{{ $payment->invoice_id }}</strong>
                    <div class="date text-inverse m-t-5">{{ date_format(date_create($payment->charged_at),'d-m-Y') }}</div>

                </div>
            </div>
            <!-- end invoice-header -->
            <!-- begin invoice-content -->
            <div class="invoice-content">
                <!-- begin table-responsive -->
                <div class="table-responsive">
                    <table class="table table-invoice">
                        <thead>
                        <tr>
                            <th>Services/Charges</th>
                            <th class="text-center" width="30%"></th>
                            <th class="text-right" width="20%">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @isset($package)
                            <tr>
                                <td>
                                    Mail Fee
                                </td>
                                <td class="text-center"></td>
                                <td class="text-right">$5</td>
                            </tr>
                            @foreach($package->serviceRequests as $item)
                                <tr>
                                    <td>
                                        {{ $item->service->title }}
                                    </td>
                                    <td class="text-center"></td>
                                    <td class="text-right">${{ $item->service->price }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>
                                    Shipping
                                </td>
                                <td class="text-center"></td>
                                <td class="text-right">${{ $package->shipping_charges }}</td>
                            </tr>
                        @endisset

                        @isset($order)
                            <tr>
                                <td>
                                    Order Items Total
                                </td>
                                <td class="text-center"></td>
                                <td class="text-right">{{ (double)$order->sub_total }}</td>
                            </tr>
                            <tr>
                                <td>
                                    Service Charges
                                </td>
                                <td class="text-center"></td>
                                <td class="text-right">{{ (double)$order->service_charges }}</td>
                            </tr>
                            <tr>
                                <td>
                                    Shipping From Shop
                                </td>
                                <td class="text-center"></td>
                                <td class="text-right">{{ (double)$order->shipping_from_shop }}</td>
                            </tr>
                            <tr>
                                <td>
                                    Pickup Charges
                                </td>
                                <td class="text-center"></td>
                                <td class="text-right">{{ (double)$order->pickup_charges ?? 0.00 }}</td>
                            </tr>

                        @endisset
                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->
                <!-- begin invoice-price -->
                <div class="invoice-price">
                    <div class="invoice-price-left">

                    </div>
                    <div class="float-right">
                        <strong>
                            <p>Sub Total : <span class="f-w-600">$ {{ $payment->charged_amount + $payment->discount }}</span></p>
                            <p>Discount : <span class="f-w-600">$ {{  $payment->discount }}</span></p>
                            <p>Total : <span class="f-w-600">$ {{ $payment->charged_amount - $payment->discount }}</span></p>
                        </strong>
                    </div>
                </div>
                <!-- end invoice-price -->
            </div>

            <div class="invoice-footer">
                <p class="text-center m-b-5 f-w-600">
                    THANK YOU FOR YOUR BUSINESS
                </p>
                <p class="text-center">
                    <span class="m-r-10"><i class="fa fa-fw fa-lg fa-globe"></i> shippingxps.com</span>
                    {{--<span class="m-r-10"><i class="fa fa-fw fa-lg fa-phone-volume"></i> T:016-18192302</span>
                    <span class="m-r-10"><i class="fa fa-fw fa-lg fa-envelope"></i> rtiemps@gmail.com</span>--}}
                </p>
            </div>
            <!-- end invoice-footer -->
        </div>
    </div>
</div>
</body>
</html>