<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use OwenIt\Auditing\Contracts\Auditable;

class GiftCardComment extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use HasFactory;
    protected $guarded = [];

    public function user()
    {
      return $this->belongsTo(User::class);
    }

    public function getCreatedAtAttribute( $value ) {
        return (new Carbon($value))->format('F d, Y H:i:s');
    }

    public function getUpdatedAtAttribute( $value ) {
        return (new Carbon($value))->format('F d, Y H:i:s');
    }
}
