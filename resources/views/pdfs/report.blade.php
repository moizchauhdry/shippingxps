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
<table class="border" style="width: 100%">
    <tr>
        <th style="width:30%">Invoice No.</th>
        <td>{{ $payment->invoice_id }}</td>
    </tr>
    <tr>
        <th style="width:30%">Transaction ID</th>
        <td>{{ $payment->transaction_id }}</td>
    </tr>
    <tr>
        <th style="width:30%">Customer Name</th>
        <td>{{ $payment->customer->name  }}</td>
    </tr>
    <tr>
        <th style="width:30%">Charged Amount</th>
        <td>{{ $payment->charged_amount }}</td>
    </tr>
    <tr>
        <th style="width:30%">Service Name</th>
        <td>{{ $payment->package->service_label }}</td>
    </tr>
    <tr>
        <th style="width:30%">Shipping Charges</th>
        <td>{{ $payment->package->shipping_total }}</td>
    </tr>
    <tr>
        <th style="width:30%">Charged Amount</th>
        <td>{{ $payment->charged_amount }}</td>
    </tr>
    <tr>
        <th style="width:30%">Charged Amount</th>
        <td>{{ date('d-m-y H:i:s',strtotime($payment->charged_at))}}</td>
    </tr>
</table>

<br>
<br>
