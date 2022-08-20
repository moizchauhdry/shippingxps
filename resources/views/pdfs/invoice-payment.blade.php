<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SHIPPING-XPS-INVOICE-{{ $payment->invoice_id }}</title>
    <style>
        table.border,
        .border th,
        .border td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        td {
            padding: 10px
        }
    </style>
</head>

<body>
    <table style="width: 100%; padding-bottom: 10px">
        <tr>
            <td colspan="4" style="text-align: center;border-bottom: #f04c23 solid 4px">
                <img style="width: auto;height:80px" src="{{ asset('public/logo.png') }}" alt="">

            </td>
        </tr>
    </table>
    <br>
    <br>
    <br>
    <strong>Invoice ID : </strong> {{ $payment->invoice_id }}
    <table class="border" style="width: 100%">
        <tr>
            <td colspan="4">
                <h3>Invoiced From</h3>
                <strong>ShippingXPS</strong><br>
                {{ $package->warehouse->name ?? $order->warehouse->name ?? $warehouse->name ?? '- -' }}<br>
                Address:
                @if(isset($package->warehouse))
                {{ $package->warehouse->address}},{{ $package->warehouse->city }},{{ $package->warehouse->state }}{{
                ','.$package->warehouse->zip ?? '' }}
                @elseif(isset($order->warehouse))
                {{ $order->warehouse->address}},{{ $order->warehouse->city }},{{ $order->warehouse->state }}{{
                ','.$order->warehouse->zip ?? '' }}
                @elseif(isset($warehouse))
                {{ $warehouse->address}},{{ $warehouse->city }},{{ $warehouse->state }}{{ ','.$warehouse->zip ?? '' }}
                @else
                - -
                @endif
                <br>
                Phone: {{ $package->warehouse->phone ?? $order->warehouse->phone ?? $warehouse->phone ?? '- -' }}<br>
                Email : {{ $package->warehouse->email ?? $order->warehouse->email ?? $warehouse->email ?? '- -' }}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <h3>Ship To:</h3>
                <strong>{{ $address->fullname ?? '- -' }}</strong><br>
                Address:{{ $address->address ?? '- -' }}<br>
                Phone: {{ $address->phone ?? '- -' }}<br>
                Email: {{ $customer->email ?? '- -' }}
            </td>
            <td colspan="2">
                <h3>Bill To:</h3>
                <strong>{{ $address->fullname ?? '- -' }}</strong><br>
                Address:{{ $address->address ?? '- -' }}<br>
                Phone: {{ $address->phone ?? '- -' }}<br>
                Email: {{ $customer->email ?? '- -' }}
            </td>
        </tr>
        {{-- @if(!empty($billing))
        <tr>
            <td colspan="2">
                <h3>Billing :</h3><br>
                <strong>{{ $billing['fullname'] ?? '- -' }}</strong><br>
                Address:{{ $billing['address'] ?? '- -' }}<br>
                Email :{{ $billing['email'] ?? '- -' }}<br>
        </tr>
        @endif --}}

    </table>
    <br>
    {{-- <strong>Charges</strong> --}}
    <table class="border" style="width: 100%">
        <tbody>
            @isset($package)
            <tr>
                <td>
                    Mail Fee
                </td>

                <td style="width: 100px">$5</td>
            </tr>
            <tr>
                <td>
                    Storage Fee
                </td>

                <td style="width: 100px">${{ $package->storage_fee }}</td>
            </tr>
            @foreach($package->serviceRequests as $item)
            <tr>
                <td>
                    {{ $item->service->title }}
                </td>

                <td style="width: 100px">
                    @if($item->service->id == 1)
                    ${{ $item->service->price + $package->orders->count() * 1.5 }}
                    @else
                    ${{ $item->service->price }}
                    @endif
                </td>
            </tr>
            @endforeach
            <tr>
                <td>
                    Shipping
                </td>
                <td style="width: 100px">${{ $package->shipping_charges }}</td>
            </tr>
            @endisset

            @isset($order)
            <tr>
                <td>
                    Order Items Total
                </td>
                <td style="width: 100px">$ {{ $order->sub_total }}</td>
            </tr>
            <tr>
                <td>
                    Service Charges
                </td>
                <td style="width: 100px">$ {{ $order->service_charges }}</td>
            </tr>
            <tr>
                <td>
                    Shipping From Shop
                </td>
                <td style="width: 100px">$ {{ $order->shipping_from_shop }}</td>
            </tr>
            <tr>
                <td>
                    Pickup Charges
                </td>

                <td style="width: 100px">$ {{ $order->pickup_charges ?? 0.00 }}</td>
            </tr>

            @endisset

            @isset($additionalRequest)
            <tr>
                <td>
                    Additional Charges For Request - {{ $additionalRequest->message ?? '- -' }}
                </td>

                <td style="width: 100px">$ {{ $additionalRequest->price }}</td>
            </tr>
            @endisset

            @isset($insuranceRequest)
            <tr>
                <td>
                    Charges For Insurance Request with Shipping
                </td>

                <td style="width: 100px">$ {{ $insuranceRequest->amount }}</td>
            </tr>
            @endisset

            @isset($giftCard)
            <tr>
                <td>
                    <span style="text-transform:uppercase">{{$giftCard->type}} Gift Card</span> x {{$giftCard->qty}}
                </td>
                <td style="width: 100px; padding:10px;">$ {{ $giftCard->amount }}</td>
            </tr>
            @endisset

        </tbody>
    </table>
    <table style="width: 100%">
        <tr>
            <td></td>
            <td style="text-align: right">
                Sub Total : ${{ $payment->charged_amount + $payment->discount }} <br>
                Discount : ${{ $payment->discount }} <br>
                Grand Total : ${{ $payment->charged_amount }}
            </td>
        </tr>
    </table>
    <br>
    <br>

    <table class="border" style="width:100%; text-align:center">
        <tr>
            <th>Payment</th>
            <th>Date</th>
            <th>Status</th>
            <th>Amount</th>
        </tr>
        <tr>
            <td>
                Transaction ID: {{$payment->transaction_id}} <br>
                Type: {{$payment->payment_type}} <br>
                Method: Card <br>
            </td>
            <td>{{ date('d-m-Y',strtotime($payment->charged_at)) }}</td>
            <td>Paid</td>
            <td>$ {{ $payment->charged_amount }}</td>
        </tr>
    </table>
    <table style="position: absolute;width: 100%;bottom: 5px">
        <tr>
            <th colspan="3" style="text-align: center">
                THANK YOU FOR YOUR BUSINESS
            </th>
        </tr>
        <tr>
            <th style="text-align: center;font-size: 12px;font-weight: normal">+1-657-210-1801</th>
            <th style="text-align: center;font-size: 12px;font-weight: normal">shippingxps.com</th>
            <th style="text-align: center;font-size: 12px;font-weight: normal">info@shippingxps.com</th>
        </tr>
    </table>
</body>

</html>