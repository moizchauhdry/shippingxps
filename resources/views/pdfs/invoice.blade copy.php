<style>

table {
  width: 100%;
  border: 1px solid #000;
}

th, td {
  text-align: left;
  vertical-align: top;
  border: 1px solid #000;
  border-collapse: collapse;
  padding: 0.3em;
  caption-side: bottom;
}

caption {
  padding: 0.3em;
}

</style>

<h1 style="text-align:center;"> Invoice </h1>
<table style="width:100%;margin-top:30px;">
    <tr>
        <td cellspacing="0" width="50%" style="height: 120px; border:1px solid #c1c1c1; margin:0;">
            Ship From : {{ $warehouse->name }}<br>
            Contact Name : {{ $warehouse->contact_person}}<br>
            Phone No : {{ $warehouse->phone}}<br>
            Email : {{ $warehouse->email}}<br>
            Address : {{ $warehouse->address}}<br>
            Country : {{ $warehouse->country->nicename}}<br>
            Reason For Export : {{ $package->package_type}}<br>

        </td>
        <td cellspacing="0" width="50%" style="height: 120px; border:1px solid #c1c1c1; margin:0;">
            Package #: {{ $package->package_no}} <br>
            Tracking #: {{ $package->orders[0]->tracking_number_in}} <br>
            Tracking Number OUT: {{ $package->orders[0]->tracking_number_out}}<br>
            Date : '.date('Y-m-d')}}<br>
        </td>
    </tr>
    <tr>
        <td cellspacing="0" width="50%" style="height: 120px; border:1px solid #c1c1c1; margin:0; padding:5px;">
            Ship To : <br>
            PHone : {{ $address->phone}}<br>
            Email : {{ $user->email}}
            City : {{ $address->city}}
            State : {{ $address->state}}
            Address : {{ $address->address}}
            Country : {{ $address->Country}}
        </td>

        <td cellspacing="0" width="50%" style="height: 120px; border:1px solid #c1c1c1; margin:0;">
            Sold TO : Same as shipped to
        </td>
    </tr>

</table>

<h1> Items </h1>

<table style="width:100%;">

    <tr class="header-row">
        <td cellspacing="0" class="header-cell" style="">Qty</td>
        <td cellspacing="0" class="header-cell" >Unit</td>
        <td cellspacing="0" class="header-cell" >Description </td>
        <td cellspacing="0" class="header-cell" >Origin</td>
        <td cellspacing="0" class="header-cell" >Price</td>
        <td cellspacing="0" class="header-cell" >Total(USD)</td>
        <td cellspacing="0" class="header-cell" ></td>
    </tr>

@php 
    $total = 0;
    $items_count = count($package->items);
@endphp



@foreach($package->items as $item)
    @php
        $total += $item->unit_price * $item->quantity;
        $country = $item->originCountry;
    @endphp
    <tr>
        <td >{{ $item->quantity}}</td>
        <td cellspacing="0" >PCS</td>
        <td cellspacing="0" >{{ $item->description}}</td>
        <td cellspacing="0" >{{ $country->nicename}}</td>
        <td cellspacing="0" >{{ $item->unit_price}}</td>
        <td cellspacing="0" >{{ $item->unit_price*$item->quantity}}
        </td>
        <td cellspacing="0" ></td>
    </tr>
    
@endforeach
    <tr>
        <td cellspacing="0" colspan="6" style="border-bottom:1px solid #c1c1c1; margin:0;text-aligh:center;">Sub Total
        </td>
        <td cellspacing="0" colspan="1" >{{ $total}}USD</td>
    </tr>

    <tr>
        <td cellspacing="0" colspan="5" style="border-bottom:1px solid #c1c1c1; margin:0;text-aligh:center;">Total
            Number of packages </td>
        <td cellspacing="0" colspan="2" >1</td>
    </tr>

    <tr>
        <td cellspacing="0" colspan="5" style="border-bottom:1px solid #c1c1c1; margin:0;text-aligh:center;">Total
            Number items </td>
        <td cellspacing="0" colspan="2" >{{ $items_count}}</td>
    </tr>

    <tr>
        <td cellspacing="0" colspan="5" style="border-bottom:1px solid #c1c1c1; margin:0;text-aligh:center;">Total
            Weight </td>
        <td cellspacing="0" colspan="2" >{{ $package->package_weight}}
        </td>
    </tr>

    <tr>
        <td cellspacing="0" colspan="5" style="border-bottom:1px solid #c1c1c1; margin:0;text-aligh:center;">Declaration
            Statement </td>
        <td cellspacing="0" colspan="2" ></td>
    </tr>

    <tr>
        <td cellspacing="0" colspan="2" style="border-bottom:1px solid #c1c1c1; margin:0;text-aligh:center;">Special
            Instructions </td>
        <td cellspacing="0" colspan="5" ></td>
    </tr>

    <tr>
        <td cellspacing="0" colspan="5" style="border-bottom:1px solid #c1c1c1; margin:0;text-aligh:center;">Invoice
            Total </td>
        <td cellspacing="0" colspan="2" >{{ $total}} USD </td>
    </tr>

    <tr>
        <td cellspacing="0" colspan="4" >These comodities, technology
            or software, were exported from us in accordance with exports administration regulations.
            Diversion contrary to US law is prohebited. I admit that all information contained in this invoice are true
            and correct.</td>
        <td cellspacing="0" colspan="2" style="border-bottom:1px solid #c1c1c1; margin:0;text-aligh:center;"> Currency
            Code</td>
        <td cellspacing="0" colspan="1" style="border-bottom:1px solid #c1c1c1; margin:0;text-aligh:center;"> USD</td>
    </tr>

    <tr>
        <td cellspacing="0" colspan="3" style="height: 80px; border:1px solid #c1c1c1; margin:0; padding:5px;">Signature
        </td>
        <td cellspacing="0" colspan="4" style="height: 80px; border:1px solid #c1c1c1; margin:0; padding:5px;"></td>
    </tr>
</table>
