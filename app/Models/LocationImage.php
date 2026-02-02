<?php

namespace App\Models;

use App\Models\city;
use App\Models\country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LocationImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_code',
        'country_code',
        'image',
    ];

    public function city()
    {
        return $this->belongsTo(city::class, 'city_code', 'code');
    }

    public function country()
    {
        return $this->belongsTo(country::class, 'country_code', 'code');
    }
}
