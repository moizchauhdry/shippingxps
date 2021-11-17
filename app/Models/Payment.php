<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public function customer(){
        return $this->belongsTo(User::class,'customer_id');
    }

    public function order(){
        return $this->belongsTo(Order::class,'order_id');
    }

    public function package(){
        return $this->belongsTo(Package::class,'package_id');
    }
}
