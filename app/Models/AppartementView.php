<?php

namespace App\Models;

use App\Models\Visit;
use App\Models\Appartement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppartementView extends Model
{
    use HasFactory;
    protected $fillable = [
        'visit_uuid',
        'appartement_uuid',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function appartement()
    {
        return $this->belongsTo(Appartement::class, 'appartement_uuid', 'uuid');
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class, 'visit_uuid', 'uuid');
    }

}
