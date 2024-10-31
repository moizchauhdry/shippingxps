<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Address extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $appends = ['country_name'];
    protected $guarded = [];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function getCountryNameAttribute()
    {
        return ucwords($this->country->name) ?? 'N/A';
    }
}
