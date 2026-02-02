<?php

namespace App\Models;

use App\Models\city;
use App\Models\User;
use App\Models\country;
use App\Models\Partner;
use App\Models\Variable;
use App\Models\Appartement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'code',
        'partner_uuid',
        'image',
        'title',
        'type_uuid',
        'category_uuid',
        'address',
        'country',
        'city',
        'longitude',
        'latitude',
        'description',
        'updated_by',
        'created_by',
        'deleted_by',
        'etat',
    ];

    
    public function pays()
    {
        return $this->belongsTo(country::class, 'country', 'code');
    }
    public function ville()
    {
        return $this->belongsTo(city::class, 'city', 'code');
    }

    public function apartements()
    {
        return $this->hasMany(Appartement::class, 'property_uuid', 'uuid');
    }

    public function type()
    {
        return $this->belongsTo(Variable::class, 'type_uuid', 'uuid');
    }
    public function category()
    {
        return $this->belongsTo(Variable::class, 'category_uuid', 'uuid');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_uuid', 'uuid');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }



}
