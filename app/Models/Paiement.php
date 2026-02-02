<?php

namespace App\Models;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Paiement extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'code',
        'reservation_code',
        'payment_mode',
        'paid_sum',
        'paid_amount',
        'payment_token',
        'payment_status',
        'command_number',
        'payment_validation_date',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_code', 'code');
    }
}