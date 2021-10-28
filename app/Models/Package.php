<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
    

    public function customer(){
        return $this->belongsTo(User::class);
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
}
