<?php

namespace App\Models;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class receipt extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'code',
        'reservation_uuid',
        'filename',
        'filepath'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_uuid', 'uuid');
    }
}
