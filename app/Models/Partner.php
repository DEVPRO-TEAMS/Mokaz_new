<?php

namespace App\Models;

use App\Models\User;
use App\Models\Comment;
use App\Models\Property;
use App\Models\Appartement;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Partner extends Model
{
    use HasFactory;

    protected $table = 'partners';

    protected $fillable = [
        'uuid',
        'code',
        'raison_social',
        'email',
        'phone',
        'website',
        'adresse',
        'etat',
    ];

    
    public function users()
    {
        return $this->hasMany(User::class, 'partner_uuid', 'uuid');
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'partner_uuid', 'uuid');
    }

    public function property()
    {
        return $this->hasMany(Property::class, 'partner_uuid', 'uuid');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'appart_uuid', 'uuid');
    }

}
