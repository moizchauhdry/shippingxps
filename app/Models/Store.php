<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Store extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use HasFactory;

    public function warehouse() {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function country() {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
