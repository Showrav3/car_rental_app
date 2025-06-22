<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'name', 
        'brand', 
        'model', 
        'year', 
        'car_type', 
        'daily_rent_price', 
        'weekly_rent_price', 
        'status', 
        'availability', 
        'image'
    ];

    public function detail(){
        return $this->hasOne(CarDetails::class);
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}

