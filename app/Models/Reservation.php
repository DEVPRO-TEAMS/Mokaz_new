<?php

namespace App\Models;

use App\Models\Partner;
use App\Models\receipt;
use App\Models\Paiement;
use App\Models\Property;
use App\Models\Appartement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'code',
        'visit_uuid',
        'nom',
        'prenoms',
        'email',
        'phone',
        'appart_uuid',
        'property_uuid',
        'partner_uuid',
        'sejour',
        'start_time',
        'end_time',
        'nbr_of_sejour',
        'total_price',
        'unit_price',
        'payment_amount',
        'still_to_pay',
        'payment_method',
        'statut_paiement',
        'status',
        'notes',
        'traited_by',
        'traited_at',
        'etat',
    ];

    public function receipt()
    {
        return $this->hasOne(receipt::class, 'reservation_uuid', 'uuid');
    }

    public function paiement()
    {
        return $this->hasOne(Paiement::class, 'reservation_code', 'code');
    }

    public function appartement()
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

    

    protected $casts = [
    'start_time' => 'datetime',
    'end_time' => 'datetime',
];    
}

