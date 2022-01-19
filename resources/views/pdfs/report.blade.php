<style>
    table.border,.border  th,.border  td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    th,td{
        padding:15px
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
        <th style="width:30%">Suite No</th>
        <td>{{ $payment->customer->suite_no  }}</td>
    </tr>
    <tr>
        <th style="width:30%">Tracking No</th>
        <td>{{ $payment->package->tracking_number_out }}</td>
    </tr>
    <tr>
        <th style="width:30%">Service Name</th>
        <td>{{ $payment->package->service_label }}</td>
    </tr>
    <tr>
        <th style="width:30%">Destination</th>
        <td>{{ $payment->package->shipping_address }}</td>
    </tr>
    @if(Auth::user()->type == "admin")
        <tr>
            <th style="width:30%">Shipping Charges (USD)</th>
            <td>{{ $payment->package != NULL ?($payment->package->shipping_total - $payment->package->markup_fee) : 'N/A'}}</td>
        </tr>
        <tr>
            <th style="width:30%">Markup Fee (USD)</th>
            <td>{{ $payment->package->markup_fee }}</td>
        </tr>
    @else
        <tr>
            <th style="width:30%">Shipping Charges (USD)</th>
            <td>{{ $payment->package->shipping_total }}</td>
        </tr>
    @endif
    <tr>
        <th style="width:30%">Service Charges (USD)</th>
        <td>{{ $payment->package->service_charges }}</td>
    </tr>
    <tr>
        <th style="width:30%">Charged Amount (USD)</th>
        <td>{{ $payment->charged_amount }}</td>
    </tr>
    <tr>
        <th style="width:30%">Charged Date</th>
        <td>{{ date('d-m-y H:i:s',strtotime($payment->charged_at))}}</td>
    </tr>
</table>

<br>
<br>
