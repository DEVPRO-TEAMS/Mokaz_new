<?php

namespace App\Models;

use App\Models\Appartement;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReconductedReservation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'uuid',
        'code',
        'original_reservation_uuid',
        'old_appart_uuid',
        'old_total_price',
        'already_paid',
        'new_reservation_uuid',
        'new_appart_uuid',
        'new_total_price',
        'remaining_to_pay',
        'amount_to_pay_now',
        'status',
        'notes',
        'etat',
    ];

    protected $casts = [
        'old_total_price' => 'decimal:2',
        'new_total_price' => 'decimal:2',
        'already_paid' => 'decimal:2',
        'remaining_to_pay' => 'decimal:2',
        'amount_to_pay_now' => 'decimal:2',
    ];

    public function originalReservation()
    {
        return $this->belongsTo(Reservation::class, 'original_reservation_uuid', 'uuid');
    }

    public function newReservation()
    {
        return $this->belongsTo(Reservation::class, 'new_reservation_uuid', 'uuid');
    }

    public function oldAppartement()
    {
        return $this->belongsTo(Appartement::class, 'old_appart_uuid', 'uuid');
    }

    public function newAppartement()
    {
        return $this->belongsTo(Appartement::class, 'new_appart_uuid', 'uuid');
    }
}


