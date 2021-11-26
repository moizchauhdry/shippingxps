<style>
    table.border,.border  th,.border  td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>
<table style="width: 100%;padding-bottom: 10px">
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
        <td colspan="2">
            <h3>Invoiced From</h3><br>
            <strong>ShippingXPS</strong><br>
            {{ $package->warehouse->name ?? $order->warehouse->name }}<br>
            Address:{{ $package->warehouse->address ?? $order->warehouse->address   }}<br>
            Phone: {{ $package->warehouse->phone ?? $order->warehouse->phone   }}<br>
            Email : {{ $package->warehouse->email ?? $order->warehouse->email   }}
        </td>


    </tr>
    <tr>
        <td colspan="2">
            <h3>Invoiced To:</h3><br>
            <strong>{{ $customer->address->fullname ?? '- -' }}</strong><br>
            Address:{{ $customer->address->address ?? '- -' }}<br>
            Phone: {{ $customer->address->phone ?? '- -' }}<br>
            Email: {{ $customer->customer->email ?? '- -' }}</td>
    </tr>


</table>
<table class="border" style="width: 100%">
    <thead>
    <tr>
        <th>Services/Charges</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    @isset($package)
        <tr>
            <td>
                Mail Fee
            </td>

            <td >$5</td>
        </tr>
        @foreach($package->serviceRequests as $item)
            <tr>
                <td>
                    {{ $item->service->title }}
                </td>

                <td >${{ $item->service->price }}</td>
            </tr>
        @endforeach
        <tr>
            <td>
                Shipping
            </td>
            <td >${{ $package->shipping_charges }}</td>
        </tr>
    @endisset

    @isset($order)
        <tr>
            <td>
                Order Items Total
            </td>
            <td >{{ $order->sub_total }}</td>
        </tr>
        <tr>
            <td>
                Service Charges
            </td>
            <td >{{ $order->service_charges }}</td>
        </tr>
        <tr>
            <td>
                Shipping From Shop
            </td>
            <td >{{ $order->shipping_from_shop }}</td>
        </tr>
        <tr>
            <td>
                Pickup Charges
            </td>

            <td >{{ $order->pickup_charges ?? 0.00 }}</td>
        </tr>

    @endisset
    </tbody>
</table>
<br>
<br>
<table style="width: 100%">
    <tr>
        <td style="width: 100px">Sub Total :  </td><td>${{ $payment->charged_amount + $payment->discount }}</td>
    </tr>
    <tr>
        <td style="width: 100px">Discount :  </td><td>${{  $payment->discount }}</td>
    </tr>
    <tr>
        <td style="width: 100px">Grand Total :  </td><td>${{ $payment->charged_amount - $payment->discount }}</td>
    </tr>
</table>
<br>
<br>

<b>Payments:</b>
<table style="width:100%">
    <tr>
        <th>Payment Method</th>
        <th>Date</th>
        <th>Status</th>
        <th>Payment Amount</th>
    </tr>
    <tr>
        <td>Card</td>
        <td>{{ date('Y-m-d',strtotime($payment->charged_amount)) }}</td>
        <td>Payment Complete</td>
        <td>$payment->charged_amount - $payment->discount</td>
    </tr>
</table>
<br>
<br>
<table style="position: absolute;width: 100%;bottom: 0px">
    <tr>
        <th colspan="3" style="text-align: center">
            THANK YOU FOR YOUR BUSINESS
            <br><br>
        </th>
    </tr>
    <tr>
        <th style="text-align: center">657-201-7881</th>
        <th style="text-align: center">shippingxps.com</th>
        <th style="text-align: center">info@shippingxps.com</th>
    </tr>
</table>