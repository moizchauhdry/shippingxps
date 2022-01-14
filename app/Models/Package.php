<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $appends = ['service_charges'];

    public function customer(){
        return $this->belongsTo(User::class);
    }

    public function packageHandler(){
        return $this->belongsTo(User::class,'package_handler_id');
    }

    public function warehouse(){
        return $this->belongsTo(Warehouse::class);
    }

    public function address(){
        return $this->belongsTo(Address::class,'address_book_id');
    }

    public function orders(){
        return $this->hasMany(Order::class);
    }

    public function serviceRequests(){
        return $this->hasMany(ServiceRequest::class);
    }

    public function items(){
        return $this->hasManyThrough('App\Models\OrderItem', 'App\Models\Order');
    }

    // public function orderItems(){
    //     return $this->hasManyThrough('App\Models\OrderItem', 'App\Models\Order');
    // }

    public function images(){
        return $this->hasManyThrough('App\Models\OrderImage', 'App\Models\Order');
    }
    
    public function getCreatedAtAttribute($value)
    {
        return date('Y-m-d h:i:s',strtotime($value));
    }

    public function setShippingAddressAttribute()
    {
        $address = $this->address;
        if($address == NULL){
            return '- -';
        }
        return $address->address;
    }

    public function getShippingAddressAttribute()
    {
        $address = $this->address;
        if($address == NULL){
            return '- -';
        }
        return $address->address.', '.$address->city.', '.ucfirst(strtolower($address->country->name));
    }

    public function getServiceChargesAttribute()
    {
        $serviceCharges = $this->serviceRequests;
        \Log::info($serviceCharges);
        $sum = 0;
        foreach($serviceCharges as $service){
            $sum += $service->price;
        }
        $mailOutFee = SiteSetting::where('name','mailout_fee')->orderBy('id','desc')->first()->value;
        $sum += $mailOutFee;
        return $sum;
    }
}
