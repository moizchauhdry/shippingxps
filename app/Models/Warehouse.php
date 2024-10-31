<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Constraint\Count;
use OwenIt\Auditing\Contracts\Auditable;


class Warehouse extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use HasFactory;

    public $timestamps = false;

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function stores() {
        return $this->hasMany(Store::class, 'warehouse_id');
    }
    
}
