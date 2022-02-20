<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo+Narrow:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>

    h5,table,th,td{
        font-family: 'Archivo Narrow', sans-serif;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        text-align: left;
        vertical-align: top;
        border: 1px solid #8a8a8a;
        /* border-collapse: collapse; */
        padding: 0.3em;
        caption-side: bottom;
        font-size: 9px;
        text-wrap: inherit;
    }

    th{
        font-weight: bolder;
        text-align: center;
    }



    caption {
        padding: 0.3em;
    }

</style>
<table style="border:none;">
    <tr>
        <td style="width:50%" style="border:none;font-size:7px;text-align:left;">
            <p>Commercial Invoice</p>
        </td>
        <td style="width:50%" style="border:none;font-size:7px;text-align:right;">
            <p>{{ date('Y-m-d') }}</p>
            <p>{{ $warehouse->address }}</p>
            <p>{{ $warehouse->city }},{{ $warehouse->state }},{{ $warehouse->zip.',' ?? '' }}</p>
            <p>{{ $warehouse->country->nicename }}</p>
        </td>
    </tr>
</table>
<h5 style="text-align:center;"> COMMERCIAL INVOICE </h5>
<table style="margin-bottom: 30px;">
    <tr  >
        <td style="padding: 5px" width="50%">
            <strong>{{ strtoupper('Shipped From') }}</strong> : <br>
            <strong>Contact Name</strong> : {{ $warehouse->contact_person}}<br>
            <strong>EORI:</strong><br>
            <strong>Telephone No.</strong> : {{ $warehouse->phone}}<br>
            <strong>E-mail</strong> : {{ $warehouse->email}}<br>
            <strong>Company Name / Address</strong> :<br>
            {{ $warehouse->address}},{{ $warehouse->city }},<br>{{ $warehouse->state }}, {{ $warehouse->zip. ', ' ?? '' }}{{ $warehouse->country->nicename }}
            <br><br>
            <strong>Country</strong> : {{ $warehouse->country->nicename }}<br>
            <strong>Incoterms</strong> : DDU/DAP <br>
            <strong>Reason For Export</strong> : {{ $package->package_type}}<br>

        </td>
        <td style="padding: 5px" width="50%">
            <strong>Tracking Number</strong>:<br> {{ $package->tracking_number_out ?? 'N/A'}}<br><br>
            <strong>Date</strong> : {{ date('Y-m-d') }}<br>
            <strong>Package ID</strong>: {{ $package->package_no}} <br>
            {{--<b>Invoice No.</b> : --}}
        </td>
    </tr>
    <tr>
        <td style="padding: 5px" width="50%">
            <strong>{{ strtoupper('Shipped To') }}</strong> : <br>
            <strong>Contact Name</strong> : {{ strtoupper($address->fullname) ?? '- -' }}<br>
            <strong>Telephone No.</strong> : {{ $address->phone ?? '- -'}}<br>
            <strong>E-mail</strong> : {{ $user->email ?? '- -'}}<br>
            <strong>Company Name / Address</strong> :<br>
            {{ $address->address ?? '- -'}}
            <strong>City</strong> : {{ $address->city ?? '- -'}}<br>
            <strong>State</strong> : {{ $address->state ?? '- -'}}<br>
            <br><br>
            <strong>Country</strong> : {{ $address->country->name ?? '- -' }}
        </td>

        <td style="padding: 5px" width="50%">
            <strong>SOLD TO</strong> : Same as SHIPPED TO
        </td>
    </tr>
</table>
<table style="width:100%;" style="margin-top:5px;">
    {{--<tr>
        <td colspan="7" style="text-align: center;">
            <h3>Items</h3>
        </td>
    </tr>--}}
    <tr class="header-row">
        <th><strong>No. of Units</strong></th>
        <th><strong>Unit of measure</strong></th>
        <th colspan="3"><strong>Description of Goods</strong></th>
        <th><strong>Country of Origin</strong></th>
        <th><strong>Price per Unit (USD)</strong></th>
        <th><strong>Total Value(USD)</strong></th>
    </tr>

    @php
        $total = 0;
        $package_count = 1;
        $items_count = count($package->items);
    @endphp



    @foreach($package->items as $item)
        @php
            $total += $item->unit_price * $item->quantity;
            $country = $item->originCountry;
        @endphp
        <tr>
            <td style="text-align: center;">{{ $item->quantity}}</td>
            <td style="text-align: center;">PCS</td>
            <td colspan="3">{{ $item->description}}</td>
            <td style="text-align: center;">{{ $country->nicename ?? '- -'}}</td>
            <td style="text-align: center;">{{ $item->unit_price}}</td>
            <td style="text-align: center;">{{ $item->unit_price*$item->quantity}}</td>
        </tr>

    @endforeach
    <tr>
        <td colspan="6"><strong>Total Packages</strong>: {{ $package_count }}</td>
        <td colspan="2"><strong>Sub Total</strong></td>
        <td colspan="1">${{ $total}}</td>
    </tr>

    <tr>
        <td colspan="6"><strong>Total Number items</strong>: {{ $items_count}}</td>
        <td colspan="2"></td>
        <td colspan="1"></td>
    </tr>

    <tr>
        <td colspan="6"><strong>Total Weight</strong>: {{ $package->package_weight}} {{ $package->weight_unit }}</td>
        <td colspan="2"></td>
        <td colspan="1">
        </td>
    </tr>

    <tr>
        <td colspan="6"><strong>Special Instructions</strong></td>
        <td colspan="2"></td>
        <td colspan="1"></td>
    </tr>

    <tr>
        <td colspan="6"><strong>Declaration Statement</strong></td>
        <td colspan="2"></td>
        <td colspan="1"></td>
    </tr>
    <tr>
        <td colspan="6"></td>
        <td colspan="2"><strong>Invoice Total</strong></td>
        <td colspan="1">${{ $total}}</td>
    </tr>

    <tr style="height:80px;">
        <td colspan="6">These comodities, technology
            or software, were exported from us in accordance with exports administration regulations.
            Diversion contrary to US law is prohebited. I admit that all information contained in this invoice are true
            and correct.
        </td>
        <td colspan="2"> <strong>Currency Code</strong>
        </td>
        <td colspan="1"> USD</td>
    </tr>

    <tr>
        <td colspan="2" style="height:80px; padding: 10px;border-right:none">
            {{--<strong>Signature</strong>:<br> @if(isset($package->package_handler_id) && $package->package_handler_id != NULL) {{ $package->packageHandler->name }} @endif--}}
            <strong>Signature</strong>:<br> <b>{{ $warehouse->signature_name ?? '' }}</b>
        </td>
        <td colspan="6" style="height:80px;border-left:none;text-align: right">
            <img style="height: 75px; width: auto;margin-right:10px" src="{{ asset('storage/app/public/'.$warehouse->signature) }}" alt="">
        </td>
    </tr>
</table>


<script>
    window.print();
</script>