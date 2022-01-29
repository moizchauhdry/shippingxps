<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AdditionalRequest extends Model
{
    use HasFactory,Notifiable;
    protected $guarded = [];

    public function comments()
    {
        return $this->hasMany(AdditionalRequestComment::class);
    }
}
