<?php

namespace App\Models;

use App\Models\Partner;
use App\Models\Appartement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $fillable = [
        'uuid',
        'appart_uuid',
        'property_uuid',
        'partner_uuid',
        'name',
        'email',
        'comment',
        'rating',
        'etat'
    ];
    public function appart()
    {
        return $this->belongsTo(Appartement::class, 'appart_uuid', 'uuid');
    }
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_uuid', 'uuid');
    }
    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_uuid', 'uuid');
    }

}
