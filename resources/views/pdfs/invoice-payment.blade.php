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
            <strong>{{ $address->fullname ?? '- -' }}</strong><br>
            Address:{{ $address->address ?? '- -' }}<br>
            Phone: {{ $address->phone ?? '- -' }}<br>
            Email: {{ $address->email ?? '- -' }}</td>
    </tr>


</table>
<br>
<strong>Charges</strong>
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
            <td style="width: 100px">{{ $order->sub_total }}</td>
        </tr>
        <tr>
            <td>
                Service Charges
            </td>
            <td style="width: 100px">{{ $order->service_charges }}</td>
        </tr>
        <tr>
            <td>
                Shipping From Shop
            </td>
            <td style="width: 100px">{{ $order->shipping_from_shop }}</td>
        </tr>
        <tr>
            <td>
                Pickup Charges
            </td>

            <td style="width: 100px">{{ $order->pickup_charges ?? 0.00 }}</td>
        </tr>

    @endisset
    </tbody>
</table>
<table style="width: 100%">
    <tr>
        <td style="text-align: right">Sub Total :  </td><td style="width: 100px">${{ $payment->charged_amount + $payment->discount }}</td>
    </tr>
    <tr>
        <td style="text-align: right">Discount :  </td><td style="width: 100px">${{  $payment->discount }}</td>
    </tr>
    <tr>
        <td style="text-align: right">Grand Total :  </td><td style="width: 100px">${{ $payment->charged_amount }}</td>
    </tr>
</table>
<br>
<br>

<b>Payments:</b>
<table class="border" style="width:100%">
    <tr>
        <th>Payment Method</th>
        <th>Date</th>
        <th>Status</th>
        <th>Payment Amount</th>
    </tr>
    <tr>
        <td>Card</td>
        <td>{{ date('d-m-Y',strtotime($payment->charged_at)) }}</td>
        <td>Payment Complete</td>
        <td>{{ $payment->charged_amount }}</td>
    </tr>
</table>
<br>
<br>
