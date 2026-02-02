<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\Property;
use App\Models\Variable;
use App\Models\AppartDoc;
use App\Models\Commodity;
use App\Models\Reservation;
use App\Models\Tarification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appartement extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'code',
        'property_uuid',
        'image',
        'title',
        'description',
        'type_uuid',
        'commodities', // enregistre comme suite pour chaque Appartement : climatiseur,Ventilateur,...
        'nbr_room',
        'nbr_bathroom',
        'nbr_available',
        'video_url',
        'updated_by',
        'created_by',
        'deleted_by',
        'etat',
    ];
    
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_uuid', 'uuid');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'appart_uuid', 'uuid');
    }
    public function images()
    {
        return $this->hasMany(AppartDoc::class, 'appartement_uuid', 'uuid');
    }
    public function tarifications()
    {
        return $this->hasMany(Tarification::class, 'appart_uuid', 'uuid');
    }

    public function type()
    {
        return $this->belongsTo(Variable::class, 'type_uuid', 'uuid');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'appart_uuid', 'uuid');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'uuid');
    }
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'uuid');
    }

    


}