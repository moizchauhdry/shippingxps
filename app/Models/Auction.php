<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['main_image','category_name'];

    /**
     * Get all of the images for the Auction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function images()
    {
        return $this->hasMany(AuctionImage::class);
    }

    public function category()
    {
        return $this->belongsTo(AuctionCategory::class);
    }

    public function getMainImageAttribute(){
        
        return $this->images()->where('featured',1)->first()->image ?? '';

    }

    public function getCategoryNameAttribute(){
        
        return $this->category->name ?? '';

    }
}
