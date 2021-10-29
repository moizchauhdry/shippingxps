<style>

table {
  width: 100%;  
  border-collapse: collapse;
}

th, td {
  text-align: left;
  vertical-align: top;
  border: 1px solid #000;
  /* border-collapse: collapse; */
  padding: 0.3em;
  caption-side: bottom;
  font-size: 12px;
}

caption {
  padding: 0.3em;
}

</style>
<table style="border:none;">
    <tr>
        <td style="width:50%" style="border:none;font-size:9px;text-align:left;">
            <p>Commercial Invoice</p>
        </td>
        <td style="width:50%" style="border:none;font-size:9px;text-align:right;">
            <p>{{ date('Y-m-d') }}</p>
            <p>{{ $warehouse->address }}</p>
        </td>
    </td>
    </tr>
</table>
<h1 style="text-align:center;"> Commercial Invoice </h1>
<table>
    <tr>
        <td width="50%">
            Ship From : {{ $warehouse->name }}<br>
            Contact Name : {{ $warehouse->contact_person}}<br>
            Phone No : {{ $warehouse->phone}}<br>
            Email : {{ $warehouse->email}}<br>
            Address : {{ $warehouse->address}}<br>
            Country : {{ $warehouse->country->nicename }}<br>
            Reason For Export : {{ $package->package_type}}<br>

        </td>
        <td width="50%">
            Package #: {{ $package->package_no}} <br>            
            Tracking Number OUT: {{ $package->tracking_number_out}}<br>
            Date : {{ date('Y-m-d') }}<br>
        </td>
    </tr>
    <tr>
        <td width="50%" >
            Ship To : <br>
            PHone : {{ $address->phone ?? '- -'}}<br>
            Email : {{ $user->email ?? '- -'}}
            City : {{ $address->city ?? '- -'}}
            State : {{ $address->state ?? '- -'}}
            Address : {{ $address->address ?? '- -'}}
            Country : {{ $address->country->name ?? '- -' }}
        </td>

        <td width="50%">
            Sold TO : Same as shipped to
        </td>
    </tr>
</table>
<table style="width:100%;" style="margin-top:5px;">
    <tr>
        <td colspan="7" style="text-align: center;">
            <h3>Items</h3>
        </td>
    </tr>
    <tr class="header-row">
        <td class="header-cell" style="">Qty</td>
        <td class="header-cell" >Unit</td>
        <td class="header-cell" >Description </td>
        <td class="header-cell" >Origin</td>
        <td class="header-cell" >Price</td>
        <td class="header-cell" >Total(USD)</td>
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
        <td>{{ $item->quantity}}</td>
        <td>PCS</td>
        <td>{{ $item->description}}</td>
        <td>{{ $country->nicename ?? '- -'}}</td>
        <td>{{ $item->unit_price}}</td>
        <td>{{ $item->unit_price*$item->quantity}}</td>
    </tr>
    
@endforeach
    <tr>
        <td colspan="4" >Total Packages {{ $package_count }}</td>
        <td colspan="2" >Sub Total</td>
        <td colspan="1" >${{ $total}}</td>
    </tr>

    <tr>
        <td colspan="4" >Total Number items </td>
        <td colspan="2" ></td>
        <td colspan="1" >{{ $items_count}}</td>
    </tr>

    <tr>
        <td colspan="4" >Total Weight</td>
        <td colspan="2" ></td>
        <td colspan="1" >{{ $package->package_weight}}
        </td>
    </tr>

    <tr>
        <td colspan="4" >Special Instructions </td>
        <td colspan="2" ></td>
        <td colspan="1" ></td>
    </tr>

    <tr>
        <td colspan="4" >Declaration Statement </td>
        <td colspan="2" > </td>
        <td colspan="1" ></td>
    </tr>
    <tr>
        <td colspan="4" ></td>
        <td colspan="2" >Invoice Total</td>
        <td colspan="1" >${{ $total}}</td>
    </tr>

    <tr style="height:80px;">
        <td colspan="4" >These comodities, technology
            or software, were exported from us in accordance with exports administration regulations.
            Diversion contrary to US law is prohebited. I admit that all information contained in this invoice are true
            and correct.</td>
        <td colspan="2" > Currency
            Code</td>
        <td colspan="1" > USD</td>
    </tr>

    <tr>
        <td colspan="2" style="height:80px;">
            Signature By : @if(isset($package->package_handler_id) && $package->package_handler_id != NULL) {{ $package->packageHandler->name }} @endif
        </td>
        <td colspan="5" style="height:80px;"></td>
    </tr>
</table>
