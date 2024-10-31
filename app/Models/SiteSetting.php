<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SiteSetting extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;

    public static function getByName($name)
    {
        $data =  self::where('name', '=', $name)->first();

        if (is_object($data)) {
            return $data->value;
        }

        return false;
    }
}
