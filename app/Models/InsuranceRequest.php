<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function comments()
    {
        return $this->hasMany(InsuranceRequestComment::class);
    }
}
