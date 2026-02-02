<?php

namespace App\Models;

use App\Models\country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class city extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'label',
        'country_code',
        'etat',
    ];

    public function country()
    {
        return $this->belongsTo(country::class, 'country_code', 'code');
    }

    public function locationImage()
    {
        return $this->hasOne(LocationImage::class, 'city_code', 'code');
    }
}
